<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DownloadGivebutterTransactions extends Command
{
    protected $signature = 'givebutter:download-transactions
                            {--id=* : One or more Givebutter transaction IDs (numeric/string as returned by the API)}
                            {--from= : Start date YYYY-MM-DD (inclusive, app timezone); use with --to for date range}
                            {--to= : End date YYYY-MM-DD (inclusive, app timezone)}
                            {--output= : Write JSON to this path instead of printing to stdout}';

    protected $description = 'Download Givebutter transactions by ID or by created date (paginates /transactions and filters locally; same API key source as the app)';

    private const API_BASE = 'https://api.givebutter.com/v1';

    public function handle(): int
    {
        $ids = array_filter(array_map('trim', (array) $this->option('id')));
        $from = $this->option('from');
        $to = $this->option('to');

        if ($ids !== [] && ($from || $to)) {
            $this->error('Use either --id (one or more) or --from/--to, not both.');

            return self::FAILURE;
        }

        if ($ids === [] && (! $from || ! $to)) {
            $this->error('Provide --id=... (repeatable) or both --from=YYYY-MM-DD and --to=YYYY-MM-DD.');

            return self::FAILURE;
        }

        try {
            $apiKey = $this->resolveApiKey();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($ids !== []) {
            $payload = $this->fetchByIds($apiKey, $ids);
        } else {
            $payload = $this->fetchByDateRange($apiKey, $from, $to);
        }

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
                'note' => 'Givebutter list endpoint does not filter by date server-side; this output is filtered client-side after full pagination.',
            ],
            'data' => $matched,
        ];
    }
}
