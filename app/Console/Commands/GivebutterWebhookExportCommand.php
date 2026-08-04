<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GivebutterWebhookExportCommand extends Command
{
    protected $signature = 'givebutter:webhook-export
                            {--id=* : One or more Givebutter transaction IDs}
                            {--from= : Start date YYYY-MM-DD (inclusive, app timezone); use with --to}
                            {--to= : End date YYYY-MM-DD (inclusive, app timezone)}
                            {--url= : Webhook URL for --post (default: GIVEBUTTER_WEBHOOK_CALLBACK_URL or {APP_URL}/payment-response)}
                            {--post : POST each payload to the webhook URL and record HTTP status/body (updates payments like a real webhook)}
                            {--output= : Write JSON to this path instead of stdout}';

    protected $description = 'Build Givebutter-style webhook JSON from the REST API (no webhook history API exists), using the same API key and webhook secret as the app; optionally POST to payment-response and capture responses';

    private const API_BASE = 'https://api.givebutter.com/v1';

    public function handle(): int
    {
        $ids = array_filter(array_map('trim', (array) $this->option('id')));
        $from = $this->option('from');
        $to = $this->option('to');
        $doPost = (bool) $this->option('post');

        if ($ids !== [] && ($from || $to)) {
            $this->error('Use either --id (one or more) or --from/--to, not both.');

            return self::FAILURE;
        }

        if ($ids === [] && (! $from || ! $to)) {
            $this->error('Provide --id=... (repeatable) or both --from=YYYY-MM-DD and --to=YYYY-MM-DD.');

            return self::FAILURE;
        }

        if ($doPost) {
            $this->warn('--post will invoke your real payment-response handler and may change payment rows. Continue only on staging or when you intend to replay.');
        }

        try {
            $apiKey = $this->resolveApiKey();
            $webhookSecret = $this->resolveWebhookSecret();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($doPost && $webhookSecret === '') {
            $this->error('Webhook secret is required for --post. Set givebutter_webhook_secret in settings or GIVEBUTTER_WEBHOOK_SECRET in .env.');

            return self::FAILURE;
        }

        [$webhookUrl, $webhookUrlSource] = $this->resolveWebhookTarget();
        if ($doPost) {
            $this->info("Webhook POST target ({$webhookUrlSource}): {$webhookUrl}");
            if ($webhookUrlSource === 'app' && str_contains($webhookUrl, 'localhost')) {
                $this->warn('APP_URL points at localhost. For production replays set GIVEBUTTER_WEBHOOK_CALLBACK_URL=https://ircc.world/payment-response or pass --url=...');
            }
        }

        if ($ids !== []) {
            $transactions = $this->fetchByIds($apiKey, $ids);
        } else {
            $bundle = $this->fetchByDateRange($apiKey, $from, $to);
            $transactions = $bundle['data'];
        }

        $items = [];
        foreach ($transactions as $transaction) {
            if (isset($transaction['error'])) {
                $items[] = [
                    'error' => 'fetch_failed',
                    'detail' => $transaction,
                    'webhook_request' => null,
                    'http_status' => null,
                    'response_body' => null,
                ];

                continue;
            }

            $event = $this->eventForTransaction($transaction);
            $webhookRequest = [
                'event' => $event,
                'data' => $transaction,
            ];

            $txnId = $transaction['id'] ?? $transaction['number'] ?? null;

            $entry = [
                'givebutter_transaction_id' => $txnId,
                'webhook_event' => $event,
                'webhook_request' => $webhookRequest,
                'webhook_url' => $webhookUrl,
                'http_status' => null,
                'response_body' => null,
                'response_json' => null,
                'response_content_type' => null,
                'response_body_bytes' => null,
                'http_client_error' => null,
            ];

            if ($doPost) {
                try {
                    $resp = Http::withHeaders([
                        'X-Givebutter-Signature' => $webhookSecret,
                        'Accept' => 'application/json',
                        'User-Agent' => 'IRCC-GivebutterWebhookExport/1.0',
                    ])
                        ->asJson()
                        ->timeout(120)
                        ->post($webhookUrl, $webhookRequest);

                    $body = $resp->body();
                    $entry['http_status'] = $resp->status();
                    $entry['response_body'] = $body;
                    $entry['response_json'] = $resp->json();
                    $entry['response_content_type'] = $resp->header('Content-Type');
                    $entry['response_body_bytes'] = strlen($body);
                    if ($body === '' && $resp->successful()) {
                        $entry['diagnostics'] = 'Empty body with 2xx: often a WAF, redirect stripping the POST body, or middleware returning a blank page. Confirm URL is the final HTTPS endpoint (try --url=https://ircc.world/payment-response).';
                    }
                } catch (ConnectionException $e) {
                    $entry['http_status'] = 0;
                    $entry['http_client_error'] = $e->getMessage();
                    $entry['response_body'] = '';
                    $entry['diagnostics'] = 'Connection failed. If you meant production, set GIVEBUTTER_WEBHOOK_CALLBACK_URL or use --url=https://ircc.world/payment-response';
                } catch (\Throwable $e) {
                    $entry['http_status'] = 0;
                    $entry['http_client_error'] = $e->getMessage();
                    $entry['response_body'] = '';
                }
            }

            $items[] = $entry;
        }

        $payload = [
            'meta' => [
                'webhook_url' => $webhookUrl,
                'webhook_url_source' => $webhookUrlSource,
                'posted' => $doPost,
                'note' => 'Givebutter does not provide an API to list past webhook deliveries. Payloads are reconstructed from GET /v1/transactions (same shape your app reads as $event in paymentResponse).',
            ],
            'items' => $items,
        ];

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $this->error('Failed to encode JSON: '.json_last_error_msg());

            return self::FAILURE;
        }

        $out = $this->option('output');
        if ($out) {
            if (file_put_contents($out, $json."\n") === false) {
                $this->error('Could not write to '.$out);

                return self::FAILURE;
            }
            $this->info('Wrote '.strlen($json).' bytes to '.$out);
        } else {
            $this->line($json);
        }

        return self::SUCCESS;
    }

    /**
     * @return array{0: string, 1: 'cli'|'env'|'app'}
     */
    private function resolveWebhookTarget(): array
    {
        $custom = $this->option('url');
        if (is_string($custom) && $custom !== '') {
            return [$custom, 'cli'];
        }

        $configured = config('services.givebutter.webhook_callback_url');
        if (is_string($configured) && trim($configured) !== '') {
            return [trim($configured), 'env'];
        }

        return [
            rtrim((string) config('app.url'), '/').'/payment-response',
            'app',
        ];
    }

    /**
     * Same resolution order as {@see \App\Traits\GivebutterHelper::getGivebutterApiKey()}.
     */
    private function resolveApiKey(): string
    {
        $apiKey = Setting::where('code', 'givebutter_api_key')->first()?->value;

        if (empty($apiKey)) {
            $apiKey = config('services.givebutter.api_key') ?? env('GIVEBUTTER_API_KEY');
        }

        if (empty($apiKey)) {
            throw new \RuntimeException(
                'Givebutter API key is not configured. Set givebutter_api_key in settings or GIVEBUTTER_API_KEY in .env.'
            );
        }

        return $apiKey;
    }

    /**
     * Same resolution order as {@see \App\Traits\GivebutterHelper::getGivebutterWebhookSecret()}.
     */
    private function resolveWebhookSecret(): string
    {
        $secret = Setting::where('code', 'givebutter_webhook_secret')->first()?->value;

        if (empty($secret)) {
            $secret = config('services.givebutter.webhook_secret') ?? env('GIVEBUTTER_WEBHOOK_SECRET');
        }

        return is_string($secret) ? $secret : '';
    }

    /**
     * @param  list<string>  $ids
     * @return list<array<string, mixed>>
     */
    private function fetchByIds(string $apiKey, array $ids): array
    {
        $results = [];
        foreach ($ids as $id) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
            ])->timeout(30)->get(self::API_BASE.'/transactions/'.$id);

            if (! $response->successful()) {
                $this->warn("ID {$id}: HTTP {$response->status()} — ".$response->body());
                $results[] = [
                    'id' => $id,
                    'error' => 'request_failed',
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ];

                continue;
            }

            $results[] = $response->json();
        }

        return $results;
    }

    /**
     * @return array{meta: array<string, mixed>, data: list<array<string, mixed>>}
     */
    private function fetchByDateRange(string $apiKey, string $fromStr, string $toStr): array
    {
        $tz = config('app.timezone');
        $from = Carbon::parse($fromStr, $tz)->startOfDay();
        $to = Carbon::parse($toStr, $tz)->endOfDay();

        if ($from->gt($to)) {
            throw new \InvalidArgumentException('--from must be on or before --to.');
        }

        $matched = [];
        $page = 1;
        $perPage = 100;
        $hasMore = true;

        while ($hasMore) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
            ])->timeout(60)->get(self::API_BASE.'/transactions', [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException(
                    'Givebutter list failed (page '.$page.'): HTTP '.$response->status().' — '.$response->body()
                );
            }

            $body = $response->json();
            $transactions = $body['data'] ?? [];

            foreach ($transactions as $transaction) {
                $raw = $transaction['created_at'] ?? $transaction['createdAt'] ?? null;
                if ($raw === null) {
                    continue;
                }
                $created = Carbon::parse($raw)->timezone($tz);
                if ($created->between($from, $to)) {
                    $matched[] = $transaction;
                }
            }

            $hasMore = count($transactions) >= $perPage;
            $page++;
            $this->output->write("\rFetched page ".($page - 1).' ('.count($matched).' in range)…');
        }

        $this->newLine();
        $this->info('Total transactions in range: '.count($matched));

        return [
            'meta' => [
                'from' => $from->toIso8601String(),
                'to' => $to->toIso8601String(),
                'timezone' => $tz,
                'pages_fetched' => $page - 1,
            ],
            'data' => $matched,
        ];
    }

    /**
     * Align with events handled in {@see \App\Http\Controllers\PaymentController::paymentResponse()}.
     */
    private function eventForTransaction(array $transaction): string
    {
        $s = strtolower((string) ($transaction['status'] ?? ''));

        if (in_array($s, ['completed', 'paid', 'success', 'succeeded', 'captured'], true)) {
            return 'transaction.succeeded';
        }

        if (in_array($s, ['failed', 'cancelled', 'canceled', 'declined', 'refunded'], true)) {
            return 'transaction.updated';
        }

        return 'transaction.updated';
    }
}
