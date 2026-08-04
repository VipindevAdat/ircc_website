<?php

namespace App\Traits;

use App\Models\Participant;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

trait GivebutterHelper
{
    /**
     * Get Givebutter API key from settings or environment
     */
    private function getGivebutterApiKey()
    {
        $apiKey = Setting::where('code', 'givebutter_api_key')->first()?->value;
        
        if (empty($apiKey)) {
            $apiKey = config('services.givebutter.api_key') ?? env('GIVEBUTTER_API_KEY');
        }

        if (empty($apiKey)) {
            throw new \Exception('Givebutter API key is not configured. Please set GIVEBUTTER_API_KEY in .env or givebutter_api_key in settings table.');
        }

        return $apiKey;
    }

    /**
     * Get Givebutter webhook secret for verification
     */
    private function getGivebutterWebhookSecret()
    {
        $secret = Setting::where('code', 'givebutter_webhook_secret')->first()?->value;
        
        if (empty($secret)) {
            $secret = config('services.givebutter.webhook_secret') ?? env('GIVEBUTTER_WEBHOOK_SECRET');
        }

        return $secret;
    }

    /**
     * Get Givebutter campaign code from settings or environment
     */
    private function getGivebutterCampaignCode()
    {
        $campaignCode = Setting::where('code', 'givebutter_campaign_code')->first()?->value;
        
        if (empty($campaignCode)) {
            $campaignCode = config('services.givebutter.campaign_code') ?? env('GIVEBUTTER_CAMPAIGN_CODE');
        }
        
        // Fallback to campaign_id for backward compatibility
        if (empty($campaignCode)) {
            $campaignCode = Setting::where('code', 'givebutter_campaign_id')->first()?->value;
            if (empty($campaignCode)) {
                $campaignCode = config('services.givebutter.campaign_id') ?? env('GIVEBUTTER_CAMPAIGN_ID');
            }
        }

        return $campaignCode; // Can be null if not configured
    }

    /**
     * Get Givebutter campaign title from settings or environment
     */
    private function getGivebutterCampaignTitle()
    {
        $campaignTitle = Setting::where('code', 'givebutter_campaign_title')->first()?->value;
        
        if (empty($campaignTitle)) {
            $campaignTitle = config('services.givebutter.campaign_title') ?? env('GIVEBUTTER_CAMPAIGN_TITLE');
        }

        return $campaignTitle; // Can be null if not configured
    }

    /**
     * Get INR to USD exchange rate from settings or use default
     */
    private function getExchangeRate()
    {
        $rate = Setting::where('code', 'inr_to_usd_rate')->first()?->value;
        
        if (empty($rate)) {
            $rate = config('services.givebutter.exchange_rate') ?? env('INR_TO_USD_RATE', 0.012); // Default: 1 INR = 0.012 USD (approx)
        }

        return (float)$rate;
    }

    /**
     * Get Givebutter API base URL
     */
    private function getGivebutterApiUrl()
    {
        return 'https://api.givebutter.com/v1';
    }

    /**
     * Create a payment/donation using Givebutter API
     * 
     * @param string $orderId Payment order ID
     * @param float $amount Amount in rupees (will be converted to cents)
     * @param array $customer Customer information
     * @param array $additionalInfo Additional metadata
     * @return array
     */
    public function createOrder($orderId, $amount, $customer = [], $additionalInfo = [])
    {
        try {
            $apiKey = $this->getGivebutterApiKey();
            $apiUrl = $this->getGivebutterApiUrl();
            $campaignCode = $this->getGivebutterCampaignCode();
            $campaignTitle = $this->getGivebutterCampaignTitle();

            // Amount is already in USD - no conversion needed
            // The amount parameter passed is in USD dollars
            $amountInUSD = (float)$amount;
            
            // IMPORTANT: We do NOT create transactions via API
            // The /transactions endpoint is ONLY for recording past/offline transactions
            // For online payments, we redirect to Givebutter's hosted donation page
            // Transactions will be created automatically by Givebutter when payment is completed
            // and we'll receive webhooks to update our database
            
            // Require campaign_code - it's essential for the hosted donation page
            if (!$campaignCode && !$campaignTitle) {
                throw new \Exception('Givebutter campaign_code or campaign_title is required. Please configure GIVEBUTTER_CAMPAIGN_CODE or GIVEBUTTER_CAMPAIGN_TITLE in your .env file or settings table.');
            }
            
            // Build the hosted donation page URL with pre-filled amount
            // This redirects users to Givebutter's payment page - no transaction is created until they pay
            // Givebutter URL parameters: https://help.givebutter.com/en/articles/4868782-how-to-leverage-url-and-html-parameters
            
            // Format amount in dollars (e.g., "300.00" for $300.00)
            $amountFormatted = number_format($amountInUSD, 2, '.', '');
            
            // Build URL with properly encoded query parameters
            // Givebutter accepts amount in dollars (e.g., 300.00 for $300.00)
            $baseUrl = "https://givebutter.com/" . urlencode($campaignCode);
            
            // Build query parameters array for proper URL encoding
            $queryParams = [
                'amount' => $amountFormatted,
                // Helps webhooks / exports tag this checkout to our pending Payment row
                'order_id' => (string) $orderId,
                'ref' => (string) $orderId,
            ];
            
            // Try to lock the amount (prevent editing)
            // Note: Givebutter may not support this via URL - you may need to configure in campaign settings
            // Try these parameters (Givebutter may support one of them):
            $queryParams['locked'] = 'true';
            $queryParams['readonly'] = 'true';
            $queryParams['fixed'] = 'true';
            // Alternative: Set minimum and maximum to the same value
            $queryParams['min'] = $amountFormatted;
            $queryParams['max'] = $amountFormatted;
            
            // Add return URL as a query parameter
            // Givebutter may support different parameter names - try multiple
            $returnUrl = route('payment.success') . '?order_id=' . urlencode($orderId);
            $queryParams['redirect'] = $returnUrl;
            $queryParams['return_url'] = $returnUrl;
            $queryParams['success_url'] = $returnUrl;
            $queryParams['redirect_url'] = $returnUrl;
            $queryParams['returnUrl'] = $returnUrl;
            $queryParams['successUrl'] = $returnUrl;
            
            // Optional: Add prefill parameters for donor information
            // These help pre-fill the form but don't affect the amount
            if (!empty($customer['email'])) {
                $queryParams['email'] = $customer['email'];
            }
            if (!empty($customer['first_name'])) {
                $queryParams['first_name'] = $customer['first_name'];
            }
            if (!empty($customer['last_name'])) {
                $queryParams['last_name'] = $customer['last_name'];
            }
            
            // Build final URL with properly encoded query string
            $checkoutUrl = $baseUrl . '?' . http_build_query($queryParams);
            
            // Add metadata to URL if supported (for tracking)
            // Note: Givebutter will handle the payment and send webhooks with order_id in metadata
            
            Log::info('Givebutter redirecting to hosted donation page', [
                'campaign_code' => $campaignCode,
                'amount_usd' => $amountInUSD,
                'amount_formatted' => $amountFormatted,
                'amount_in_url' => $amountFormatted . ' USD',
                'checkout_url' => $checkoutUrl,
                'note' => 'No transaction created - will be created by Givebutter after payment completion',
                'currency' => 'USD (amount in dollars format)',
                'amount_locked' => 'Attempted via URL parameters (locked, readonly, fixed, min/max). To fully lock amount, disable "Other" option in Givebutter campaign settings.',
            ]);
            
                return [
                    'status' => 'ACTIVE',
                    'donation_id' => null, // Will be set after payment completion via webhook
                    'checkout_url' => $checkoutUrl,
                    'url' => $checkoutUrl,
                    'payment_url' => $checkoutUrl,
                    'amount_usd' => $amountInUSD,
                ];
        } catch (\Exception $e) {
            Log::error('Givebutter createOrder exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return [
                'status' => 'FAILED',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Parse Givebutter amount from API response.
     * 
     * IMPORTANT: Givebutter API v1 returns amounts in DOLLARS, not cents.
     * Evidence: A known $150.00 payment returns amount=150 from the API.
     * Previously this function divided by 100, causing all amounts to be 
     * 1/100th of the actual value (e.g., $150 → $1.50).
     * 
     * The raw amount from the API is already in dollars and should be returned as-is.
     */
    private function parseGivebutterAmount($rawAmount)
    {
        if ($rawAmount === null) {
            return null;
        }
        
        $rawAmount = (float) $rawAmount;
        
        // Log raw amount for debugging (only in debug mode)
        Log::debug('parseGivebutterAmount: raw API amount', [
            'raw_amount' => $rawAmount,
            'note' => 'Givebutter API returns amounts in dollars (not cents)',
        ]);
        
        // Return as-is - Givebutter API returns amounts in dollars
        return $rawAmount;
    }

    /**
     * Retrieve transaction details from Givebutter API
     * 
     * @param string $orderId Payment order ID or Givebutter transaction ID
     * @return array
     */
    public function retrieve_transaction($orderId)
    {
        try {
            $apiKey = $this->getGivebutterApiKey();
            $apiUrl = $this->getGivebutterApiUrl();

            if (!$apiKey || !$apiUrl) {
                return [
                    'status' => 'ERROR',
                    'error' => 'Givebutter API credentials not configured',
                ];
            }

            // First, try to find payment by order_id in database
            $payment = Payment::where('payment_order_id', $orderId)->first();
            $expectedAmount = $payment ? (float) ($payment->amount ?? 0) : 0;
            
            // ───────────────────────────────────────────────────────────
            // PATH 1: Direct lookup by stored transaction_id
            // ───────────────────────────────────────────────────────────
            if ($payment && $payment->transaction_id) {
                $transactionId = $payment->transaction_id;
                
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                    ])->timeout(10)->get($apiUrl . '/transactions/' . $transactionId);

                    if ($response->successful()) {
                        $transaction = $response->json();
                        $rawApiAmount = $transaction['amount'] ?? null;
                        $gatewayAmount = $this->parseGivebutterAmount($rawApiAmount);
                        
                        Log::info('retrieve_transaction: Path 1 - API response amount', [
                            'order_id' => $orderId,
                            'raw_api_amount' => $rawApiAmount,
                            'parsed_amount' => $gatewayAmount,
                            'expected_amount' => $expectedAmount,
                            'transaction_status' => $transaction['status'] ?? 'unknown',
                        ]);
                        
                        // VALIDATION: Check that the returned transaction's metadata order_id matches
                        $txnOrderId = $transaction['metadata']['order_id'] 
                                   ?? $transaction['custom_fields']['order_id'] 
                                   ?? $transaction['customFields']['order_id'] 
                                   ?? null;
                        
                        // VALIDATION: Check amount - if there's a large discrepancy, this transaction_id
                        // is likely corrupted (set by a previous bad sync)
                        $amountValid = true;
                        if ($gatewayAmount !== null && $expectedAmount > 0) {
                            $amountDiff = abs($expectedAmount - $gatewayAmount);
                            $allowedDiff = max(1.0, $expectedAmount * 0.10);
                            if ($amountDiff > $allowedDiff) {
                                $amountValid = false;
                            }
                        }
                        
                        // Accept transaction only if:
                        // 1. metadata order_id matches, OR metadata has no order_id AND amount is valid
                        $metadataMatches = ($txnOrderId !== null && $txnOrderId === $orderId);
                        $metadataMissing = ($txnOrderId === null);
                        
                        if ($metadataMatches || ($metadataMissing && $amountValid)) {
                            Log::info('retrieve_transaction: Path 1 SUCCESS - matched by transaction_id', [
                                'order_id' => $orderId,
                                'transaction_id' => $transactionId,
                                'metadata_match' => $metadataMatches,
                                'amount_valid' => $amountValid,
                                'gateway_amount' => $gatewayAmount,
                                'expected_amount' => $expectedAmount,
                            ]);
                            return [
                                'status' => 'SUCCESS',
                                'transaction' => $transaction,
                                'amount' => $gatewayAmount,
                                'currency' => $transaction['currency'] ?? 'USD',
                                'transaction_status' => $transaction['status'] ?? 'unknown',
                                'match_path' => 'transaction_id',
                            ];
                        } else {
                            // Transaction fetched but doesn't match - likely corrupted transaction_id
                            Log::warning('retrieve_transaction: Path 1 REJECTED - stored transaction_id returned wrong transaction', [
                                'order_id' => $orderId,
                                'stored_transaction_id' => $transactionId,
                                'gateway_order_id' => $txnOrderId,
                                'expected_amount' => $expectedAmount,
                                'raw_api_amount' => $rawApiAmount,
                                'gateway_amount' => $gatewayAmount,
                                'amount_valid' => $amountValid,
                                'note' => 'Stored transaction_id may be corrupted from a previous bad sync. Falling through to metadata search.',
                            ]);
                            // Fall through to Path 2
                        }
                    } else {
                        $errorBody = $response->json();
                        $errorMsg = $errorBody['message'] ?? $errorBody['error'] ?? 'API request failed with status ' . $response->status();
                        Log::warning('retrieve_transaction: Path 1 API error', [
                            'order_id' => $orderId,
                            'transaction_id' => $transactionId,
                            'status' => $response->status(),
                            'error' => $errorMsg
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('retrieve_transaction: Path 1 exception', [
                        'order_id' => $orderId,
                        'transaction_id' => $transactionId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // ───────────────────────────────────────────────────────────
            // PATH 2: Search by metadata (order_id in transaction metadata)
            // Note: Givebutter hosted donations may NOT store order_id in metadata
            // ───────────────────────────────────────────────────────────
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                ])->timeout(10)->get($apiUrl . '/transactions', [
                    'metadata[order_id]' => $orderId,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['data']) && count($data['data']) > 0) {
                        // IMPORTANT: Validate that the returned transaction's metadata 
                        // actually contains our order_id. The Givebutter API may not support 
                        // metadata filtering and could return ALL transactions instead.
                        $matchedTransaction = null;
                        foreach ($data['data'] as $txn) {
                            $txnOrderId = $txn['metadata']['order_id'] 
                                       ?? $txn['custom_fields']['order_id'] 
                                       ?? $txn['customFields']['order_id'] 
                                       ?? null;
                            if ($txnOrderId === $orderId) {
                                $matchedTransaction = $txn;
                                break;
                            }
                        }
                        
                        if ($matchedTransaction) {
                            $gatewayAmount = $this->parseGivebutterAmount($matchedTransaction['amount'] ?? null);
                            Log::info('retrieve_transaction: Path 2 SUCCESS - matched by metadata order_id', [
                                'order_id' => $orderId,
                                'gateway_amount' => $gatewayAmount,
                                'expected_amount' => $expectedAmount,
                            ]);
                            return [
                                'status' => 'SUCCESS',
                                'transaction' => $matchedTransaction,
                                'amount' => $gatewayAmount,
                                'currency' => $matchedTransaction['currency'] ?? 'USD',
                                'transaction_status' => $matchedTransaction['status'] ?? 'unknown',
                                'match_path' => 'metadata_search',
                            ];
                        } else {
                            Log::info('retrieve_transaction: Path 2 - no matching order_id in API results', [
                                'order_id' => $orderId,
                                'results_count' => count($data['data']),
                                'note' => 'Givebutter API metadata filter likely not supported for hosted donations.',
                            ]);
                        }
                    } else {
                        Log::info('retrieve_transaction: Path 2 - no transactions returned from API', [
                            'order_id' => $orderId,
                        ]);
                    }
                } else {
                    $errorBody = $response->json();
                    $errorMsg = $errorBody['message'] ?? $errorBody['error'] ?? 'API request failed with status ' . $response->status();
                    Log::warning('retrieve_transaction: Path 2 API error', [
                        'order_id' => $orderId,
                        'status' => $response->status(),
                        'error' => $errorMsg
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('retrieve_transaction: Path 2 exception', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage()
                ]);
            }

            // ───────────────────────────────────────────────────────────
            // PATH 3: Search by donor email + amount (fallback)
            // If we have the donor's email from the payment record, try to 
            // find a matching transaction by email and amount
            // ───────────────────────────────────────────────────────────
            if ($payment && $payment->email && $expectedAmount > 0) {
                try {
                    // Fetch recent transactions and try to match by email + amount
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                    ])->timeout(15)->get($apiUrl . '/transactions', [
                        'per_page' => 100,
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        if (!empty($data['data'])) {
                            $donorEmail = strtolower(trim($payment->email));
                            
                            foreach ($data['data'] as $txn) {
                                $txnEmail = strtolower(trim(
                                    $txn['email'] 
                                    ?? $txn['donor']['email'] 
                                    ?? $txn['giving_space']['email']
                                    ?? ''
                                ));
                                
                                $txnAmount = $this->parseGivebutterAmount($txn['amount'] ?? null);
                                $txnStatus = strtolower($txn['status'] ?? '');
                                
                                // Match by email + amount (within tolerance) + successful status
                                if ($txnEmail === $donorEmail && $txnAmount !== null) {
                                    $amountDiff = abs($expectedAmount - $txnAmount);
                                    $allowedDiff = max(1.0, $expectedAmount * 0.10);
                                    
                                    if ($amountDiff <= $allowedDiff && in_array($txnStatus, ['completed', 'paid', 'success', 'succeeded', 'captured'])) {
                                        Log::info('retrieve_transaction: Path 3 SUCCESS - matched by email + amount', [
                                            'order_id' => $orderId,
                                            'donor_email' => $donorEmail,
                                            'gateway_amount' => $txnAmount,
                                            'expected_amount' => $expectedAmount,
                                            'transaction_id' => $txn['id'] ?? 'N/A',
                                        ]);
                                        return [
                                            'status' => 'SUCCESS',
                                            'transaction' => $txn,
                                            'amount' => $txnAmount,
                                            'currency' => $txn['currency'] ?? 'USD',
                                            'transaction_status' => $txn['status'] ?? 'unknown',
                                            'match_path' => 'email_amount_search',
                                        ];
                                    }
                                }
                            }
                            
                            Log::info('retrieve_transaction: Path 3 - no matching email+amount found', [
                                'order_id' => $orderId,
                                'donor_email' => $donorEmail,
                                'expected_amount' => $expectedAmount,
                                'transactions_checked' => count($data['data']),
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('retrieve_transaction: Path 3 exception', [
                        'order_id' => $orderId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Fallback to database - only if payment exists but not found in gateway
            if ($payment) {
                return [
                    'status' => 'NOT_FOUND',
                    'error' => 'Payment found in database but no matching transaction in payment gateway. The stored transaction_id may be corrupted or the payment was never completed.',
                    'payment' => $payment,
                ];
            }

            return [
                'status' => 'NOT_FOUND',
                'error' => 'Transaction not found in database or gateway',
            ];
        } catch (\Exception $e) {
            Log::error('Givebutter retrieve_transaction error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'status' => 'ERROR',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify Givebutter webhook signature
     * 
     * @param string $payload Raw request payload
     * @param string $signature Webhook signature from header
     * @return array|false
     */
    public function verifyWebhook($payload, $signature)
    {
        try {
            $webhookSecret = $this->getGivebutterWebhookSecret();

            // 1. If no secret is configured, we cannot verify – log and still process
            if (empty($webhookSecret)) {
                Log::warning('Givebutter webhook secret not configured - processing without verification');
                return is_string($payload) ? json_decode($payload, true) : $payload;
            }

            // 2. If no signature header, treat as invalid (match sample: exit early)
            if (empty($signature)) {
                Log::warning('Givebutter webhook signature missing');
                return false;
            }

            // Givebutter sends the webhook secret directly as the signature (not an HMAC hash)
            // Compare the secret directly with the signature
            if (hash_equals($webhookSecret, $signature)) {
                Log::info('Givebutter webhook signature verified (direct secret comparison)', [
                    'secret_length' => strlen($webhookSecret),
                    'signature_length' => strlen($signature),
                ]);
                return json_decode($payload, true);
            }
            
            // If direct comparison fails, try HMAC-SHA256 as fallback (for backward compatibility)
            // This allows testing different signature formats
            $useHmacFallback = env('GIVEBUTTER_USE_HMAC_VERIFICATION', false);
            if ($useHmacFallback) {
                $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
                if (hash_equals($expectedSignature, $signature)) {
                    Log::info('Givebutter webhook signature verified (HMAC-SHA256 fallback)');
                    return json_decode($payload, true);
                }
            }
            
            // Signature verification failed
            Log::warning('Givebutter webhook signature verification failed', [
                'secret_preview' => substr($webhookSecret, 0, 20) . '...',
                'signature_preview' => substr($signature, 0, 20) . '...',
                'secret_length' => strlen($webhookSecret),
                'signature_length' => strlen($signature),
                'note' => 'Direct secret comparison failed. Givebutter sends secret as signature.',
            ]);
            return false;

            // TESTING MODE: Temporarily bypass signature verification
            // Set GIVEBUTTER_SKIP_SIGNATURE_VERIFICATION=true in .env for testing
            $skipVerification = env('GIVEBUTTER_SKIP_SIGNATURE_VERIFICATION', false);
            if ($skipVerification) {
                Log::warning('Givebutter webhook signature verification SKIPPED (testing mode)', [
                    'received_signature' => substr($signature, 0, 20) . '...',
                    'signature_length' => strlen($signature),
                    'secret_length' => strlen($webhookSecret),
                    'secret_preview' => substr($webhookSecret, 0, 10) . '...',
                    'note' => 'This is for testing only - disable in production!',
                ]);
                return json_decode($payload, true);
            }

            // 3. Compute expected signature using HMAC-SHA256 over the RAW payload
            //    This matches the sample you provided:
            //    $expectedSignature = hash_hmac('sha256', $payload, $signingSecret);
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

            // 4. Compare using hash_equals to prevent timing attacks
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Givebutter webhook signature verification failed', [
                    'expected' => substr($expectedSignature, 0, 20) . '...',
                    'received' => substr($signature, 0, 20) . '...',
                    'signature_length' => strlen($signature),
                    'expected_length' => strlen($expectedSignature),
                    'payload_length' => strlen($payload),
                    'secret_length' => strlen($webhookSecret),
                    'secret_preview' => substr($webhookSecret, 0, 10) . '...',
                    'note' => 'HMAC-SHA256(payload, webhook_secret) did not match received signature',
                ]);
                return false;
            }

            // 5. Signature is valid – return decoded JSON
            Log::info('Givebutter webhook signature verified successfully');
            return json_decode($payload, true);
        } catch (\Exception $e) {
            Log::error('Givebutter verifyWebhook error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate verification token for payment
     * 
     * @param Payment $payment
     * @return string
     */
    public function generateVerificationToken(Payment $payment)
    {
        // Generate a unique token for this payment
        $token = Str::random(64);
        
        // Store token in payment record (if you add verification_token column)
        // Or use signed URLs instead (recommended)
        
        return $token;
    }

    /**
     * Calculate user payment summary
     * 
     * @param int|null $registrationId Optional registration ID, uses authenticated user if not provided
     * @return array
     */
    public function user_payments($registrationId = null)
    {
        $user = Auth::guard('customer')->user();
        
        if (!$user && !$registrationId) {
            return [
                'subTotal' => '0.00',
                'grandTotal' => '0.00',
                'paidAmount' => '0.00',
                'amountPayable' => '0.00',
                'studentAmountPayable' => '0.00',
                'adultAmountPayable' => '0.00',
                'refund' => '0.00',
                'amountPerHead' => '0.00',
                'registrationFee' => '0.00',
            ];
        }

        $registrationId = $registrationId ?? $user->id;
        
        // Refresh user data to get the latest is_attending status
        if ($user) {
            $user->refresh();
        }

        $studentPriceEarly = (float) config('ircc.student_price_early', 150.00);
        $studentPriceRegular = (float) config('ircc.student_price_regular', 200.00);
        $regularStartsAt = Carbon::parse(
            config('ircc.student_regular_pricing_starts_at', '2026-04-01 00:00:00'),
            config('app.timezone')
        );
        // Default display price (used by UI text/labels).
        $studentPrice = Registration::studentUnitPriceForNewAccount();
        $adultPrice = (float) optional(Setting::where('code', 'adult_fee')->first())->value ?? 50.00; // Adult Attendee price (configurable)
        $registrationFee = (float) optional(Setting::where('code', 'registration_fee')->first())->value ?? 0;

        // Get participants, excluding deleted ones (type = 2)
        $participants = Participant::where('registration_id', $registrationId)->where('type', '<>', 2)->get();
        
        // First, ensure all participants have the role field set
        // This is a one-time fix for existing data
        foreach ($participants as $participant) {
            if (empty($participant->role) || $participant->role === null) {
                // Determine role based on type field
                // type 1 = adult, type 0 or null = student
                $role = ($participant->type == 1) ? 'adult' : 'student';
                try {
                    $participant->update(['role' => $role]);
                    // Refresh the model to get the updated role
                    $participant->refresh();
                } catch (\Exception $e) {
                    // Ignore update errors, but log them
                    \Log::warning('Failed to update participant role', [
                        'participant_id' => $participant->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        // Refresh participants collection to get updated roles
        $participants = Participant::where('registration_id', $registrationId)->where('type', '<>', 2)->get();
        
        // Count by role
        $studentCount = 0;
        $adultCount = 0;
        $hasStudentUnder12 = false;
        $studentSubtotal = 0.0;
        $studentCountEarly = 0;
        $studentCountRegular = 0;
        $studentSubtotalEarly = 0.0;
        $studentSubtotalRegular = 0.0;
        
        foreach ($participants as $participant) {
            // Use the participant's stored role for pricing calculation
            // Do NOT override the DB - admin may have explicitly set the role
            $role = $participant->role;
            
            if (empty($role) || $role === null) {
                // If role is not set, use type as fallback for calculation
                $role = ($participant->type == 1) ? 'adult' : 'student';
            }
            
            // Count based on role
            if ($role === 'student') {
                $studentCount++;
                // Price each student by when that participant was added.
                // This prevents retroactive repricing of already-paid early-bird participants
                // while still charging regular rate for newly added participants after cutoff.
                $participantCreatedAt = $participant->created_at
                    ? Carbon::parse($participant->created_at, config('app.timezone'))
                    : null;
                if ($participantCreatedAt && $participantCreatedAt->lt($regularStartsAt)) {
                    $studentSubtotal += $studentPriceEarly;
                    $studentCountEarly++;
                    $studentSubtotalEarly += $studentPriceEarly;
                } else {
                    $studentSubtotal += $studentPriceRegular;
                    $studentCountRegular++;
                    $studentSubtotalRegular += $studentPriceRegular;
                }
                // Check if student is under 12
                $age = $participant->age ? (int)$participant->age : null;
                if ($age && $age > 0 && $age < 12) {
                    $hasStudentUnder12 = true;
                } elseif (!$age || $age == 0) {
                    // If age is not provided, infer from class
                    // Classes 1-5 are typically under 12 (5th grade is typically 10-11 years old)
                    if ($participant->class) {
                        $class = (int)$participant->class;
                        if ($class > 0 && $class <= 5) {
                            $hasStudentUnder12 = true;
                        }
                    }
                }
            } elseif ($role === 'adult') {
                // Don't count adults yet - we'll count them only if adult fee should be charged
                // This prevents charging for adults when there's no student under 12 and registered user is not attending
            }
        }
        
        // Adult fee is only charged for adults who are in the participants list
        // If there's a student under 12, the registered user must be in the participants list (auto-added)
        // Count all adult participants (only those in the participants list)
        foreach ($participants as $participant) {
            $role = $participant->role;
            if (empty($role) || $role === null) {
                $role = ($participant->type == 1) ? 'adult' : 'student';
            }
            
            // Count all adults in the participants list
            if ($role === 'adult') {
                $adultCount++;
            }
        }
        
        // If there's a student under 12, we MUST have at least 1 adult in the participants list
        // This should already be handled by auto-adding the registered user when a student under 12 is added
        if ($hasStudentUnder12 && $adultCount == 0) {
            // This should not happen if the auto-add logic is working correctly
            // But as a safety measure, we'll still charge for 1 adult
            $adultCount = 1;
        }
        
        $participantsCount = $participants->count();
        
        // Calculate subtotal based on role
        $adultSubtotal = $adultCount * $adultPrice;
        $subTotal = $studentSubtotal + $adultSubtotal;
        
        // For backward compatibility, calculate average amount per head
        $amountPerHead = $participantsCount > 0 ? ($subTotal / $participantsCount) : 0;

        // Include only truly completed payments (status 3 = Completed)
        // Exclude failed payments (status 6) and other non-completed statuses
        // Also exclude payments with error descriptions indicating failure
        $payments = Payment::where('registration_id', $registrationId)
            ->where('payment_status', 3) // Only Completed payments
            ->where(function($query) {
                // Exclude payments that have error descriptions indicating failure
                $query->whereNull('error_description')
                      ->orWhere('error_description', 'NOT LIKE', '%failed%')
                      ->orWhere('error_description', 'NOT LIKE', '%Failed%')
                      ->orWhere('error_description', 'NOT LIKE', '%Givebutter recording failed%');
            })
            ->get();
        
        // Sum paid_amount if available, otherwise use amount
        // Only count payments that are actually completed (not failed)
        $paidAmount = $payments->isEmpty() ? 0 : $payments->sum(function($payment) {
            // Double-check: only count if status is actually 3 (Completed) and no failure errors
            if ($payment->payment_status != 3) {
                return 0;
            }
            // Don't count if error_description indicates failure
            if ($payment->error_description && (
                stripos($payment->error_description, 'failed') !== false ||
                stripos($payment->error_description, 'Givebutter recording failed') !== false
            )) {
                return 0;
            }
            $amount = $payment->paid_amount ?? $payment->amount ?? 0;
            return (float) $amount;
        });
        
        // Count participants only from truly completed payments (no failure errors)
        $paidParticipantsCount = Payment::where('registration_id', $registrationId)
            ->where('payment_status', 3) // Only Completed payments
            ->where(function($query) {
                // Exclude payments with error descriptions indicating failure
                $query->whereNull('error_description')
                      ->orWhere('error_description', 'NOT LIKE', '%failed%')
                      ->orWhere('error_description', 'NOT LIKE', '%Failed%')
                      ->orWhere('error_description', 'NOT LIKE', '%Givebutter recording failed%');
            })
            ->sum('participants');

        // Calculate grand total
        // Participant fees ARE the registration fee (no separate registration fee to add)
        $grandTotal = $subTotal;

        // Compute dues by unpaid participant slots instead of retroactively repricing all participants.
        // Paid payments store how many participant slots were covered; any newly added participant(s)
        // should be the only additional payable amount.
        $unpaidParticipantsCount = max($participantsCount - (int) $paidParticipantsCount, 0);
        $pendingStudentCountEarly = 0;
        $pendingStudentCountRegular = 0;
        $pendingAdultCount = 0;
        $pendingStudentSubtotalEarly = 0.0;
        $pendingStudentSubtotalRegular = 0.0;
        $pendingAdultSubtotal = 0.0;
        /** Dollar savings if RISE150 reprices unpaid regular-rate students to early-bird (same loop as amount payable). */
        $rise150StudentDiscount = 0.0;

        if ($unpaidParticipantsCount > 0) {
            $participantsSortedNewestFirst = $participants->sortByDesc('created_at')->values();
            $unpaidParticipants = $participantsSortedNewestFirst->take($unpaidParticipantsCount);
            $totalAmountPayable = 0.0;

            foreach ($unpaidParticipants as $participant) {
                $role = $participant->role;
                if (empty($role) || $role === null) {
                    $role = ($participant->type == 1) ? 'adult' : 'student';
                }

                if ($role === 'adult') {
                    $pendingAdultCount++;
                    $pendingAdultSubtotal += $adultPrice;
                    $totalAmountPayable += $adultPrice;
                    continue;
                }

                $participantCreatedAt = $participant->created_at
                    ? Carbon::parse($participant->created_at, config('app.timezone'))
                    : null;
                if ($participantCreatedAt && $participantCreatedAt->lt($regularStartsAt)) {
                    $pendingStudentCountEarly++;
                    $pendingStudentSubtotalEarly += $studentPriceEarly;
                    $totalAmountPayable += $studentPriceEarly;
                } else {
                    $pendingStudentCountRegular++;
                    $pendingStudentSubtotalRegular += $studentPriceRegular;
                    $totalAmountPayable += $studentPriceRegular;
                    $rise150StudentDiscount += max(0.0, $studentPriceRegular - $studentPriceEarly);
                }
            }
        } else {
            $totalAmountPayable = 0;
        }
        $refund = $paidAmount > $subTotal ? $paidAmount - $subTotal : 0;

        $studentAmountPayable = round($pendingStudentSubtotalEarly + $pendingStudentSubtotalRegular, 2);
        $adultAmountPayable = round($pendingAdultSubtotal, 2);

        return [
            'subTotal' => number_format($subTotal, 2),
            'grandTotal' => number_format($grandTotal, 2),
            'paidAmount' => number_format($paidAmount, 2),
            'amountPayable' => number_format($totalAmountPayable, 2),
            'studentAmountPayable' => number_format($studentAmountPayable, 2),
            'adultAmountPayable' => number_format($adultAmountPayable, 2),
            'refund' => number_format($refund, 2),
            'amountPerHead' => number_format($amountPerHead, 2),
            'registrationFee' => number_format($registrationFee, 2),
            // New fields for role-based pricing
            'studentCount' => $studentCount,
            'adultCount' => $adultCount,
            'studentPrice' => number_format($studentPrice, 2),
            'adultPrice' => number_format($adultPrice, 2),
            'studentSubtotal' => number_format($studentSubtotal, 2),
            'adultSubtotal' => number_format($adultSubtotal, 2),
            'studentPriceEarly' => number_format($studentPriceEarly, 2),
            'studentPriceRegular' => number_format($studentPriceRegular, 2),
            'studentCountEarly' => $studentCountEarly,
            'studentCountRegular' => $studentCountRegular,
            'studentSubtotalEarly' => number_format($studentSubtotalEarly, 2),
            'studentSubtotalRegular' => number_format($studentSubtotalRegular, 2),
            'pendingStudentCountEarly' => $pendingStudentCountEarly,
            'pendingStudentCountRegular' => $pendingStudentCountRegular,
            'pendingAdultCount' => $pendingAdultCount,
            'pendingStudentSubtotalEarly' => number_format($pendingStudentSubtotalEarly, 2),
            'pendingStudentSubtotalRegular' => number_format($pendingStudentSubtotalRegular, 2),
            'pendingAdultSubtotal' => number_format($pendingAdultSubtotal, 2),
            'rise150_student_discount' => number_format(round($rise150StudentDiscount, 2), 2),
        ];
    }
}
