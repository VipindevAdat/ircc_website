<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentCategory;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportPayments;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Exception;
use App\Mail\PaymentDetails;
use App\Models\Registration;
use App\Mail\Register;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Traits\GivebutterHelper;

class PaymentController extends Controller
{
    use GivebutterHelper;

    public function index(Request $request, Datatables $datatables)
    {
        if ($request->ajax()) {
            $query = Payment::query();

            if ($request->filled('payment_mode')) {
                $query->where('payment_mode', $request->payment_mode);
            }
            
            // Filter by status
            if ($request->filled('status_filter')) {
                $statusFilter = $request->status_filter;
                if ($statusFilter === 'completed') {
                    $query->where('payment_status', 3);
                } elseif ($statusFilter === 'pending') {
                    $query->whereIn('payment_status', [1, 2]);
                } elseif ($statusFilter === 'failed') {
                    $query->where('payment_status', 6);
                }
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $from = Carbon::parse($request->date_from)->startOfDay();
                $to = Carbon::parse($request->date_to)->endOfDay();
                $query->where(function ($query) use ($from, $to) {
                    $query->whereBetween('payment_date', [$from, $to])
                        ->orWhereBetween('transaction_date', [$from, $to]);
                });
            }

            // Filter for old pending payments if requested
            if ($request->filled('old_pending_only') && $request->old_pending_only == '1') {
                $daysOld = $request->input('days_old', 7); // Default 7 days
                $cutoffDate = Carbon::now()->subDays($daysOld);
                $query->whereIn('payment_status', [1, 2]) // Pending or Processing
                      ->where(function ($q) use ($cutoffDate) {
                          $q->where('created_at', '<', $cutoffDate)
                            ->orWhere('payment_date', '<', $cutoffDate)
                            ->orWhere('transaction_date', '<', $cutoffDate);
                      });
            }
            
            // Filter to exclude payments not found in gateway (if requested)
            if ($request->filled('exclude_not_in_gateway') && $request->exclude_not_in_gateway == '1') {
                // This would require a flag or separate query - for now, we'll add a note
                // Payments not in gateway are typically old incomplete payments
            }

            $payments = $query->with('registration')->orderBy('id', 'DESC');

            return $datatables->eloquent($payments)
                ->addColumn('date', function (Payment $payment) {
                    try {
                        // Determine if it's an online payment (not Direct Bank Transfer)
                        $isOnlinePayment = $payment->payment_mode !== 'Direct Bank Transfer';
                        
                    if ($payment->payment_mode === 'Direct Bank Transfer') {
                            // For bank transfers, use payment_date (user-entered date)
                            $date = $payment->payment_date ?? $payment->created_at;
                            if ($date) {
                                // payment_date might be date-only, so handle both cases
                                $parsedDate = Carbon::parse($date);
                                // If payment_date is date-only, use created_at time or default to 00:00
                                if (!$payment->payment_date || strlen($payment->payment_date) <= 10) {
                                    return $parsedDate->format('d-m-Y') . '<br>' . ($payment->created_at ? Carbon::parse($payment->created_at)->format('H:i A') : '00:00 AM');
                                }
                                return $parsedDate->format('d-m-Y') . '<br>' . $parsedDate->format('H:i A');
                            }
                        } else {
                            // For online transactions (Givebutter Online, Online Transaction, etc.)
                            // Priority: transaction_date (actual payment completion time) > payment_date > created_at
                            // transaction_date is set when payment is completed via webhook
                            // This is especially important for partial payments which have transaction_date set
                            $date = null;
                            
                            if ($payment->transaction_date) {
                                $date = $payment->transaction_date;
                            } elseif ($payment->payment_date) {
                                $date = $payment->payment_date;
                            } elseif ($payment->created_at) {
                                $date = $payment->created_at;
                            }
                            
                            if ($date) {
                                $parsedDate = Carbon::parse($date);
                                return $parsedDate->format('d-m-Y') . '<br>' . $parsedDate->format('H:i A');
                            }
                        }
                        
                        // Final fallback
                        if ($payment->created_at) {
                            $parsedDate = Carbon::parse($payment->created_at);
                            return $parsedDate->format('d-m-Y') . '<br>' . $parsedDate->format('H:i A');
                        }
                    } catch (\Exception $e) {
                        \Log::error('Date parsing error', [
                            'payment_id' => $payment->id,
                            'payment_order_id' => $payment->payment_order_id,
                            'payment_mode' => $payment->payment_mode,
                            'transaction_date' => $payment->transaction_date,
                            'payment_date' => $payment->payment_date,
                            'created_at' => $payment->created_at,
                            'error' => $e->getMessage()
                        ]);
                    }
                    return '--';
                })  
                ->addColumn('payment_amount', function (Payment $payment) {
                    return $payment->payment_mode === 'Direct Bank Transfer' 
                        ? number_format($payment->amount, 2) 
                        : number_format($payment->paid_amount, 2);
                })
                ->addColumn('registration_number', function (Payment $payment) {
                    return $payment->registration ? $payment->registration->registration_number : '--';
                })
                ->editColumn('payment_status', function (Payment $payment) {
                    $status = strtolower($payment->payment_status ?? 'Pending');
                    $status_name=$payment->status?$payment->status->status:'Pending';
                    if ($status === '3') {
                        return '<span class="bg-success text-white px-2 py-1 rounded">Completed</span>';
                    } elseif ($status === '6') {
                        return '<span class="bg-danger text-white px-2 py-1 rounded">Failed</span>';
                    }
                    elseif ($status === '4' && $payment->payment_mode === 'Direct Bank Transfer') {
                        return '<span class="bg-danger text-white px-2 py-1 rounded">Rejected</span>';
                    }
                    return '<span class="bg-warning text-dark px-2 py-1 rounded">' . ucfirst($status_name) . '</span>';
                })
                ->addColumn('row_style', function (Payment $payment) {
                    $styles = [];
                    if ($payment->payment_mode === 'Direct Bank Transfer' && $payment->payment_status==1) {
                        $styles[] = 'background-color: #f8d7da;';
                    }
                    // Highlight old pending payments (older than 7 days)
                    // Use same date logic as days_old column for consistency
                    if (in_array($payment->payment_status, [1, 2])) {
                        $paymentDate = null;
                        if ($payment->payment_mode === 'Direct Bank Transfer') {
                            $paymentDate = $payment->payment_date ?? $payment->created_at;
                        } else {
                            // For online payments, use transaction_date if available (actual payment time)
                            $paymentDate = $payment->transaction_date ?? $payment->payment_date ?? $payment->created_at;
                        }
                        
                        if ($paymentDate) {
                            $daysOld = Carbon::parse($paymentDate)->diffInDays(Carbon::now());
                            if ($daysOld > 7) {
                                $styles[] = 'border-left: 4px solid #ff6b6b;';
                            }
                        }
                    }
                    return implode(' ', $styles);
                })
                ->addColumn('days_old', function (Payment $payment) {
                    try {
                        // Use the same date logic as the date column for consistency
                        $paymentDate = null;
                        
                        if ($payment->payment_mode === 'Direct Bank Transfer') {
                            $paymentDate = $payment->payment_date ?? $payment->created_at;
                        } else {
                            // For online transactions (all modes: Givebutter Online, Online Transaction, etc.)
                            // Prioritize transaction_date (actual payment completion time)
                            // This is especially important for partial payments which have transaction_date set
                            $paymentDate = $payment->transaction_date ?? $payment->payment_date ?? $payment->created_at;
                        }
                        
                        if ($paymentDate) {
                            $daysOld = Carbon::parse($paymentDate)->diffInDays(Carbon::now());
                            
                            // Show days old for pending/processing payments with color coding
                            if (in_array($payment->payment_status, [1, 2])) {
                                if ($daysOld > 7) {
                                    return '<span class="badge bg-danger" title="Payment pending for ' . $daysOld . ' days">' . $daysOld . ' days old</span>';
                                } elseif ($daysOld > 3) {
                                    return '<span class="badge bg-warning" title="Payment pending for ' . $daysOld . ' days">' . $daysOld . ' days</span>';
                                } else {
                                    return '<span class="badge bg-info" title="Payment pending for ' . $daysOld . ' days">' . $daysOld . ' days</span>';
                                }
                            } else {
                                // For completed/failed payments, show age from transaction date (when payment actually happened)
                                // For partial payments, this shows age from when partial payment was received
                                return '<span class="badge bg-secondary" title="Payment is ' . $daysOld . ' days old">' . $daysOld . ' days</span>';
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Days old calculation error', [
                            'payment_id' => $payment->id,
                            'payment_order_id' => $payment->payment_order_id,
                            'payment_mode' => $payment->payment_mode,
                            'transaction_date' => $payment->transaction_date,
                            'payment_date' => $payment->payment_date,
                            'created_at' => $payment->created_at,
                            'error' => $e->getMessage()
                        ]);
                    }
                    return '--';
                })
                ->addColumn('action', function (Payment $payment) {
                    $action='';
                    if ($payment->payment_mode === 'Direct Bank Transfer' && ($payment->payment_status==1 || $payment->payment_status==4)) {
                        $action='<a  class="btn-sm btn-success view-details" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#paymentDetailsModal"
                                        data-id="'.$payment->id.'" style="cursor: pointer; " title="Approve Payment" ><i class="fa fa-check" aria-hidden="true"></i> Verify</a>';
                    }
                    elseif($payment->payment_mode === 'Direct Bank Transfer' && $payment->payment_status==3) {
                        $action='<a  class="btn-sm btn-danger view-details" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#paymentDetailsModal"
                                        data-id="'.$payment->id.'" style="cursor: pointer; " title="Reject Payment" ><i class="fa fa-check" aria-hidden="true"></i> Reject</a>';
                    }
                    // Add resend email button for completed payments
                    if ($payment->payment_status == 3) {
                        $action.='<a  class="btn btn-sm btn-info resend-email" 
                                        data-payment-id="'.$payment->id.'" 
                                        title="Resend Confirmation Email" 
                                        style="cursor: pointer; margin-left: 5px;">
                            <i class="fa fa-envelope"></i></a>';
                    }
                   $action.='<a  class="btn btn-sm view-details" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#paymentDetailsModal"
                                        data-id="'.$payment->id.'" 
                                        style="margin-left: 5px;">
                            <i class="bi bi-eye-fill"></i></a>';
                   return $action;         
                })
                ->rawColumns(['date','payment_status','row_style','action','days_old'])
                ->make(true);
        }

        // Get statistics for old pending payments
        $oldPendingCount = Payment::whereIn('payment_status', [1, 2])
            ->where(function ($query) {
                $cutoffDate = Carbon::now()->subDays(7);
                $query->where('created_at', '<', $cutoffDate)
                      ->orWhere('payment_date', '<', $cutoffDate)
                      ->orWhere('transaction_date', '<', $cutoffDate);
            })
            ->count();
        
        $veryOldPendingCount = Payment::whereIn('payment_status', [1, 2])
            ->where(function ($query) {
                $cutoffDate = Carbon::now()->subDays(30);
                $query->where('created_at', '<', $cutoffDate)
                      ->orWhere('payment_date', '<', $cutoffDate)
                      ->orWhere('transaction_date', '<', $cutoffDate);
            })
            ->count();
        
        return view('admin.payment.list', compact('oldPendingCount', 'veryOldPendingCount'));
    }
    
    /**
     * Verify payments against payment gateway
     * Fetches all transactions from Givebutter and compares with database
     * Matches by: 1) metadata order_id, 2) transaction_id, 3) email+amount
     */
    public function verifyPaymentsAgainstGateway(Request $request)
    {
        try {
            $apiKey = $this->getGivebutterApiKey();
            $apiUrl = $this->getGivebutterApiUrl();
            
            if (!$apiKey || !$apiUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Givebutter API credentials not configured'
                ], 400);
            }
            
            // Fetch ALL transactions from Givebutter (all pages)
            $allGatewayTransactions = [];
            $page = 1;
            $perPage = 100;
            $hasMore = true;
            
            while ($hasMore) {
                try {
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                    ])->timeout(30)->get($apiUrl . '/transactions', [
                        'page' => $page,
                        'per_page' => $perPage,
                    ]);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        $transactions = $data['data'] ?? [];
                        
                        foreach ($transactions as $transaction) {
                            $txnId = $transaction['id'] ?? $transaction['number'] ?? null;
                            $rawAmount = $transaction['amount'] ?? null;
                            // Givebutter API returns amounts in dollars (not cents)
                            $parsedAmount = ($rawAmount !== null) ? (float) $rawAmount : null;
                            $txnEmail = strtolower(trim(
                                $transaction['email'] 
                                ?? $transaction['donor']['email'] 
                                ?? $transaction['giving_space']['email']
                                ?? ''
                            ));
                            $txnStatus = $transaction['status'] ?? 'unknown';
                            $metadataOrderId = $transaction['metadata']['order_id'] 
                                            ?? $transaction['custom_fields']['order_id'] 
                                            ?? $transaction['customFields']['order_id']
                                            ?? null;
                            
                            $allGatewayTransactions[] = [
                                'transaction_id' => $txnId,
                                'status' => $txnStatus,
                                'amount' => $parsedAmount,
                                'email' => $txnEmail,
                                'metadata_order_id' => $metadataOrderId,
                                'created_at' => $transaction['created_at'] ?? $transaction['createdAt'] ?? null,
                            ];
                        }
                        
                        // Check if there are more pages
                        $hasMore = !empty($transactions) && count($transactions) >= $perPage;
                        $page++;
                    } else {
                        $hasMore = false;
                        \Log::error('Failed to fetch transactions from Givebutter', [
                            'page' => $page,
                            'status' => $response->status(),
                            'response' => $response->body()
                        ]);
                    }
                } catch (\Exception $e) {
                    $hasMore = false;
                    \Log::error('Error fetching transactions from Givebutter', [
                        'page' => $page,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            \Log::info('Payment verification: fetched gateway transactions', [
                'total_gateway_transactions' => count($allGatewayTransactions),
                'pages_fetched' => $page - 1,
            ]);
            
            // Build lookup indexes for fast matching
            $byMetadataOrderId = [];
            $byTransactionId = [];
            $byEmailAmount = []; // key: "email|amount" => array of transactions
            
            foreach ($allGatewayTransactions as $txn) {
                if (!empty($txn['metadata_order_id'])) {
                    $byMetadataOrderId[$txn['metadata_order_id']] = $txn;
                }
                if (!empty($txn['transaction_id'])) {
                    $byTransactionId[$txn['transaction_id']] = $txn;
                }
                if (!empty($txn['email']) && $txn['amount'] !== null) {
                    $key = $txn['email'] . '|' . round($txn['amount'], 2);
                    $byEmailAmount[$key][] = $txn;
                }
            }
            
            // Get all online transactions from database
            $dbPayments = Payment::where('payment_mode', 'Online Transaction')
                ->get()
                ->keyBy('payment_order_id');
            
            $validPayments = [];
            $invalidPayments = [];
            $matchedGatewayTxnIds = []; // Track which gateway transactions were matched
            
            // Check database payments against gateway using multiple matching strategies
            foreach ($dbPayments as $orderId => $payment) {
                $matched = false;
                $matchMethod = '';
                $matchedTxn = null;
                
                // Strategy 1: Match by metadata order_id
                if (isset($byMetadataOrderId[$orderId])) {
                    $matched = true;
                    $matchMethod = 'metadata_order_id';
                    $matchedTxn = $byMetadataOrderId[$orderId];
                }
                
                // Strategy 2: Match by stored transaction_id (only if not already matched)
                if (!$matched && !empty($payment->transaction_id) && isset($byTransactionId[$payment->transaction_id])) {
                    $txn = $byTransactionId[$payment->transaction_id];
                    // Validate amount to avoid corrupted transaction_ids
                    $expectedAmount = (float) ($payment->amount ?? 0);
                    if ($txn['amount'] !== null && $expectedAmount > 0) {
                        $amountDiff = abs($expectedAmount - $txn['amount']);
                        $allowedDiff = max(1.0, $expectedAmount * 0.10);
                        if ($amountDiff <= $allowedDiff) {
                            $matched = true;
                            $matchMethod = 'transaction_id';
                            $matchedTxn = $txn;
                        }
                    }
                }
                
                // Strategy 3: Match by email + amount (only for successful transactions)
                if (!$matched && !empty($payment->email)) {
                    $dbEmail = strtolower(trim($payment->email));
                    $expectedAmount = (float) ($payment->amount ?? 0);
                    $key = $dbEmail . '|' . round($expectedAmount, 2);
                    
                    if (isset($byEmailAmount[$key])) {
                        foreach ($byEmailAmount[$key] as $txn) {
                            $txnStatus = strtolower($txn['status']);
                            if (in_array($txnStatus, ['completed', 'paid', 'success', 'succeeded', 'captured'])) {
                                // Check this transaction wasn't already matched to another payment
                                if (!in_array($txn['transaction_id'], $matchedGatewayTxnIds)) {
                                    $matched = true;
                                    $matchMethod = 'email_amount';
                                    $matchedTxn = $txn;
                                    break;
                                }
                            }
                        }
                    }
                }
                
                if ($matched && $matchedTxn) {
                    if ($matchedTxn['transaction_id']) {
                        $matchedGatewayTxnIds[] = $matchedTxn['transaction_id'];
                    }
                    $validPayments[] = [
                        'order_id' => $orderId,
                        'payment_id' => $payment->id,
                        'match_method' => $matchMethod,
                        'status' => "Valid - matched by {$matchMethod}",
                        'gateway_status' => $matchedTxn['status'],
                        'gateway_amount' => $matchedTxn['amount'],
                        'db_amount' => $payment->amount,
                        'db_status' => $payment->payment_status,
                        'gateway_txn_id' => $matchedTxn['transaction_id'],
                    ];
                } else {
                    $invalidPayments[] = [
                        'order_id' => $orderId,
                        'payment_id' => $payment->id,
                        'registration_number' => $payment->registration ? $payment->registration->registration_number : 'N/A',
                        'amount' => $payment->paid_amount ?? $payment->amount,
                        'status' => $payment->payment_status,
                        'status_name' => $payment->payment_status_name ?? 'Unknown',
                        'created_at' => $payment->created_at ? $payment->created_at->format('Y-m-d H:i:s') : 'N/A',
                        'days_old' => $payment->created_at ? $payment->created_at->diffInDays(Carbon::now()) : 0,
                        'has_transaction_id' => !empty($payment->transaction_id),
                        'has_email' => !empty($payment->email),
                    ];
                }
            }
            
            // Check for transactions in gateway that aren't matched to any DB payment
            $missingInDb = [];
            foreach ($allGatewayTransactions as $txn) {
                if ($txn['transaction_id'] && !in_array($txn['transaction_id'], $matchedGatewayTxnIds)) {
                    // Only report successful unmatched transactions (not test/failed ones)
                    $txnStatus = strtolower($txn['status']);
                    if (in_array($txnStatus, ['completed', 'paid', 'success', 'succeeded', 'captured'])) {
                        $missingInDb[] = [
                            'transaction_id' => $txn['transaction_id'],
                            'metadata_order_id' => $txn['metadata_order_id'],
                            'status' => $txn['status'],
                            'amount' => $txn['amount'],
                            'email' => $txn['email'],
                            'created_at' => $txn['created_at'],
                        ];
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'summary' => [
                    'gateway_transactions' => count($allGatewayTransactions),
                    'database_payments' => $dbPayments->count(),
                    'valid_payments' => count($validPayments),
                    'invalid_payments' => count($invalidPayments),
                    'missing_in_database' => count($missingInDb),
                ],
                'valid_payments' => $validPayments,
                'invalid_payments' => $invalidPayments,
                'missing_in_database' => $missingInDb,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Payment verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error verifying payments: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark invalid payments as failed (payments not found in gateway)
     */
    public function markInvalidPaymentsAsFailed(Request $request)
    {
        try {
            $orderIds = $request->input('order_ids', []);
            
            if (empty($orderIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No order IDs provided'
                ], 400);
            }
            
            $updated = 0;
            foreach ($orderIds as $orderId) {
                $payment = Payment::where('payment_order_id', $orderId)
                    ->where('payment_mode', 'Online Transaction')
                    ->first();
                
                if ($payment && in_array($payment->payment_status, [1, 2])) { // Only update pending/processing
                    $payment->update([
                        'payment_status' => 6,
                        'payment_status_name' => 'Failed',
                        'error_description' => 'Payment not found in payment gateway - marked as invalid',
                        'auth_status' => '0399',
                    ]);
                    $updated++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Marked {$updated} payments as failed",
                'updated' => $updated
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Mark invalid payments error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error marking payments: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Resend payment confirmation email
     */
    public function resendPaymentEmail(Request $request)
    {
        try {
            $paymentId = $request->input('payment_id');
            
            if (!$paymentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment ID is required'
                ], 400);
            }
            
            $payment = Payment::find($paymentId);
            
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }
            
            if ($payment->payment_status != 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only resend emails for completed payments'
                ], 400);
            }
            
            $user = Registration::find($payment->registration_id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found for this payment'
                ], 404);
            }
            
            $adminEmail = Setting::where('code', 'email')->first()?->value ?? 'ignite@ircc.world';
            
            try {
                \Log::info('Resending payment confirmation email', [
                    'user_email' => $user->email,
                    'payment_id' => $payment->id,
                    'payment_order_id' => $payment->payment_order_id,
                ]);
                
                Mail::to($user->email)->send(new PaymentDetails($user, $payment->id, 'user'));
                \Log::info('Payment confirmation email resent successfully', [
                    'user_email' => $user->email,
                    'payment_id' => $payment->id,
                ]);
                
                Mail::to($adminEmail)->send(new PaymentDetails($user, $payment->id, 'admin'));
                \Log::info('Admin payment confirmation email resent successfully', [
                    'admin_email' => $adminEmail,
                    'payment_id' => $payment->id,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Confirmation emails have been resent successfully'
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Failed to resend payment confirmation email', [
                    'error' => $e->getMessage(),
                    'payment_id' => $payment->id,
                    'user_email' => $user->email,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email: ' . $e->getMessage()
                ], 500);
            }
            
        } catch (\Exception $e) {
            \Log::error('Resend payment email error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get report of old pending payments
     */
    public function getOldPendingPayments(Request $request)
    {
        $daysOld = $request->input('days', 7); // Default 7 days
        $cutoffDate = Carbon::now()->subDays($daysOld);
        
        $oldPayments = Payment::whereIn('payment_status', [1, 2]) // Pending or Processing
            ->where(function ($query) use ($cutoffDate) {
                $query->where('created_at', '<', $cutoffDate)
                      ->orWhere('payment_date', '<', $cutoffDate)
                      ->orWhere('transaction_date', '<', $cutoffDate);
            })
            ->with('registration')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->map(function ($payment) {
                $paymentDate = $payment->transaction_date ?? $payment->payment_date ?? $payment->created_at;
                $daysOld = $paymentDate ? Carbon::parse($paymentDate)->diffInDays(Carbon::now()) : 0;
                
                return [
                    'id' => $payment->id,
                    'payment_order_id' => $payment->payment_order_id,
                    'registration_number' => $payment->registration ? $payment->registration->registration_number : 'N/A',
                    'payment_mode' => $payment->payment_mode,
                    'amount' => $payment->paid_amount ?? $payment->amount,
                    'status' => $payment->payment_status,
                    'status_name' => $payment->payment_status_name ?? ($payment->status ? $payment->status->status : 'Unknown'),
                    'created_at' => $payment->created_at ? Carbon::parse($payment->created_at)->format('Y-m-d H:i:s') : 'N/A',
                    'payment_date' => $payment->payment_date ? Carbon::parse($payment->payment_date)->format('Y-m-d H:i:s') : 'N/A',
                    'days_old' => $daysOld,
                    'email' => $payment->email,
                ];
            });
        
        return response()->json([
            'success' => true,
            'count' => $oldPayments->count(),
            'payments' => $oldPayments,
            'days_threshold' => $daysOld
        ]);
    }


 
     /**
     * Show the form for creating a new resource.
     */
    public function create()
        {
            //
        }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
        {
           //
        }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $paymentDetails = Payment::find($payment->id);

    if (!$paymentDetails) {
        return response()->json(['error' => 'Payment not found'], 404);
    }

    $category = PaymentCategory::where('category_id', $paymentDetails->payment_category)->first();

    $paymentData = [
        'payment_order_id' => $paymentDetails->payment_order_id,
        'bdOrderId' => $paymentDetails->bdOrderId,
        'transaction_id' => $paymentDetails->transaction_id,
        'transaction_error_type' => $paymentDetails->transaction_error_type,
        'transaction_error_code' => $paymentDetails->transaction_error_code,
        'transaction_error_description' => $paymentDetails->transaction_error_desc,
        'auth_status' => $paymentDetails->auth_status,
        'payment_mode' => $paymentDetails->payment_mode,
        'payment_mode_type' => $paymentDetails->payment_mode_type,
        'payment_category' => $category ? $category->category : 'Category not found',
        'amount' => number_format($paymentDetails->amount,2),
        'payment_reference' => $paymentDetails->payment_reference,
        'currency' => $paymentDetails->currency,
        'email' => $paymentDetails->email,
        'payment_status_id' => $paymentDetails->payment_status,
        'payment_status' => $paymentDetails->status->status,
        'bank_reference_number' => $paymentDetails->bank_reference_number,
        'bank_id' => $paymentDetails->bank_id,
        'payment_date' => Carbon::parse($paymentDetails->payment_date)->format('d M Y h:i A'),
        'transaction_date' => Carbon::parse($paymentDetails->transaction_date)->format('d M Y h:i A'),
        'comments' =>$paymentDetails->comments,
    ];
        return view('admin.payment.detail',compact('paymentData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
        {
            //
        }

    /**
     * Update the specified resource in storage.
     */
    public function update()
        {
            //
        }
   
    public function destroy(string $id)
        {
            //
        }

    public function exportPayments(Request $request)
    {
        // Validate and handle the request parameters (if needed)
        return Excel::download(new ExportPayments($request), 'payments.xlsx');
    }
    public function approvePayment(Request $request){
         $payment=Payment::where('payment_order_id',$request->id)->first();
         if(!empty($payment)){
         $user=Registration::where('id',$payment->registration_id)->first();
         $adminEmail =Setting::where('code', 'email')->first()?Setting::where('code', 'email')->first()->value:'ignite@ircc.world';
            $payment->update(['payment_status'=>$request->status,'payment_status_name'=>'Completed','comments'=>$request->comments]);  
           if($request->status==3){
            // try{
            //      Mail::to($user->email)->send(new PaymentDetails($user,$payment->id, 'user'));
            //      Mail::to($adminEmail)->send(new PaymentDetails($user,$payment->id, 'admin'));
            //      Mail::to($user->email)->send(new Register($user,'user'));
            //     // Mail::to($adminEmail)->send(new Register($user,'admin'));
            //  }
            //  catch(Exception $e){
            //    ;
            //  }
             return response()->json(["message" => "Payment Approved successfully"], 200);
           }
           else{
            return response()->json(["message" => "Payment Rejected successfully"], 200);
           }
           
           
         }
         else{
            return response()->json(['error' => 'Payment not found'], 404);
         }
         
    }
    public function paymentDetail(Request $request)
    {
        $paymentDetails = Payment::find($request->id);
    
        if (!$paymentDetails) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
    
        $category = PaymentCategory::where('category_id', $paymentDetails->payment_category)->first();
    
        $paymentData = [
            'payment_order_id' => $paymentDetails->payment_order_id,
            'bdOrderId' => $paymentDetails->bdOrderId,
            'transaction_id' => $paymentDetails->transaction_id,
            'transaction_error_type' => $paymentDetails->transaction_error_type,
            'transaction_error_code' => $paymentDetails->transaction_error_code,
            'transaction_error_description' => $paymentDetails->transaction_error_desc,
            'auth_status' => $paymentDetails->auth_status,
            'payment_mode' => $paymentDetails->payment_mode,
            'payment_mode_type' => $paymentDetails->payment_mode_type,
            'payment_category' => $category ? $category->category : 'Category not found',
            'amount' => $paymentDetails->amount,
            'payment_reference' => $paymentDetails->payment_reference,
            'currency' => $paymentDetails->currency,
            'email' => $paymentDetails->email,
            'payment_status_id' => $paymentDetails->payment_status,
            'payment_status' => $paymentDetails->status->status,
            'bank_reference_number' => $paymentDetails->bank_reference_number,
            'bank_id' => $paymentDetails->bank_id,
            'payment_date' => Carbon::parse($paymentDetails->payment_date)->format('d M Y h:i A'),
            'transaction_date' => Carbon::parse($paymentDetails->transaction_date)->format('d M Y h:i A'),
            'comments' =>$paymentDetails->comments,
        ];
    
        
        return view('admin.payment.payment-modal', compact('paymentData'));
    }
    /**
     * Clear corrupted transaction_id values from payments
     * Use after a bad sync that stored wrong Givebutter transaction IDs
     */
    public function clearCorruptedTransactionIds(Request $request)
    {
        try {
            $dryRun = $request->has('dry_run') ? $request->boolean('dry_run') : true;
            
            // Find all online payments that have a transaction_id set
            // We'll check if the stored transaction_id actually belongs to this payment
            $payments = Payment::where('payment_mode', 'Online Transaction')
                ->whereNotNull('transaction_id')
                ->where('transaction_id', '!=', '')
                ->get();
            
            $cleared = 0;
            $kept = 0;
            $results = [];
            
            foreach ($payments as $payment) {
                try {
                    $apiKey = $this->getGivebutterApiKey();
                    $apiUrl = $this->getGivebutterApiUrl();
                    
                    // Fetch the transaction by stored ID
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                    ])->timeout(10)->get($apiUrl . '/transactions/' . $payment->transaction_id);
                    
                    $isCorrupted = false;
                    $reason = '';
                    
                    if ($response->successful()) {
                        $transaction = $response->json();
                        // Givebutter API returns amounts in dollars (not cents)
                        $gatewayAmount = isset($transaction['amount']) ? (float) $transaction['amount'] : null;
                        $expectedAmount = (float) ($payment->amount ?? 0);
                        
                        // Check metadata match
                        $txnOrderId = $transaction['metadata']['order_id'] 
                                   ?? $transaction['custom_fields']['order_id'] 
                                   ?? $transaction['customFields']['order_id'] 
                                   ?? null;
                        
                        if ($txnOrderId !== null && $txnOrderId !== $payment->payment_order_id) {
                            $isCorrupted = true;
                            $reason = "Metadata order_id mismatch: expected {$payment->payment_order_id}, got {$txnOrderId}";
                        } elseif ($gatewayAmount !== null && $expectedAmount > 0) {
                            $amountDiff = abs($expectedAmount - $gatewayAmount);
                            $allowedDiff = max(1.0, $expectedAmount * 0.10);
                            if ($amountDiff > $allowedDiff) {
                                $isCorrupted = true;
                                $reason = "Amount mismatch: expected \${$expectedAmount}, gateway has \${$gatewayAmount}";
                            }
                        }
                    } else {
                        // Transaction ID doesn't exist in Givebutter
                        $isCorrupted = true;
                        $reason = "Transaction ID not found in Givebutter API (status: {$response->status()})";
                    }
                    
                    if ($isCorrupted) {
                        $results[] = [
                            'order_id' => $payment->payment_order_id,
                            'transaction_id' => $payment->transaction_id,
                            'reason' => $reason,
                            'action' => $dryRun ? 'Would clear' : 'Cleared',
                        ];
                        
                        if (!$dryRun) {
                            $payment->update([
                                'transaction_id' => null,
                                'error_description' => 'Transaction ID cleared (was corrupted): ' . $reason . '. Previous ID: ' . $payment->transaction_id,
                            ]);
                        }
                        $cleared++;
                    } else {
                        $kept++;
                    }
                } catch (\Exception $e) {
                    \Log::error('Error checking transaction_id', [
                        'payment_id' => $payment->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $modeLabel = $dryRun ? '[DRY RUN] ' : '';
            
            return response()->json([
                'success' => true,
                'message' => "{$modeLabel}Checked {$payments->count()} payments. Corrupted: {$cleared}, Valid: {$kept}",
                'total_checked' => $payments->count(),
                'corrupted' => $cleared,
                'valid' => $kept,
                'dry_run' => $dryRun,
                'details' => $results,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Clear corrupted transaction IDs error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync payments with payment gateway
     * Retrieves latest transaction status from Givebutter and updates local records
     * 
     * SAFETY: Only syncs pending/processing payments by default.
     * Saves a snapshot of previous state before updating, allowing rollback.
     */
    public function paymentRetrive(Request $request){
        try {
            $paymentId = $request->input('payment_id');
            // Use Laravel's ->boolean() to correctly parse "false"/"0"/"no" from AJAX
            $syncAll = $request->boolean('sync_all');
            $dryRun = $request->boolean('dry_run');
            
            $query = Payment::where('payment_mode', 'Online Transaction');
            
            if ($paymentId) {
                $query->where('id', $paymentId);
            } elseif (!$syncAll) {
                // Only sync pending/processing payments by default
                $query->whereIn('payment_status', [1, 2]);
            }
            
            $payments = $query->get();
            
            if ($payments->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payments found to sync'
                ], 404);
            }
            
            $synced = 0;
            $failed = 0;
            $notFound = 0;
            $skipped = 0;
            $errors = [];
            $snapshots = []; // Store pre-sync state for rollback
            $changes = []; // Store what would/did change for review
            
            foreach($payments as $payment) {
                try {
                    $data = $this->retrieve_transaction($payment->payment_order_id);
                    
                    // Log the response for debugging
                    $responseStatus = $data['status'] ?? 'unknown';
                    $transactionStatus = isset($data['transaction']) ? ($data['transaction']['status'] ?? 'unknown') : 'N/A';
                    $matchPath = $data['match_path'] ?? 'N/A';
                    
                    \Log::info('Payment sync response', [
                        'order_id' => $payment->payment_order_id,
                        'response_status' => $responseStatus,
                        'transaction_status' => $transactionStatus,
                        'match_path' => $matchPath,
                        'gateway_amount' => $data['amount'] ?? null,
                        'db_amount' => $payment->amount,
                        'has_transaction' => isset($data['transaction']),
                        'error' => $data['error'] ?? null,
                        'dry_run' => $dryRun,
                    ]);
                    
                    // Check response status (SUCCESS, ERROR, NOT_FOUND)
                    if (strtoupper($responseStatus) === 'SUCCESS') {
                        // Check if we have transaction data from gateway
                        if (isset($data['transaction'])) {
                            $transaction = $data['transaction'];
                            
                            // Map Givebutter status to payment status
                            $gatewayStatus = strtolower($transaction['status'] ?? 'unknown');
                            $paymentStatus = 1; // Default to pending
                            $paymentStatusName = 'Pending';
                            
                            if (in_array($gatewayStatus, ['completed', 'paid', 'success', 'succeeded', 'captured'])) {
                                $paymentStatus = 3;
                                $paymentStatusName = 'Completed';
                            } elseif (in_array($gatewayStatus, ['failed', 'cancelled', 'canceled', 'declined', 'refunded'])) {
                                $paymentStatus = 6;
                                $paymentStatusName = 'Failed';
                            } elseif (in_array($gatewayStatus, ['pending', 'processing', 'authorized'])) {
                                $paymentStatus = 2;
                                $paymentStatusName = 'Processing';
                            }
                            
                            // Amount already validated and parsed by retrieve_transaction()
                            $gatewayAmount = $data['amount'] ?? null;
                            
                            // SAFETY CHECK: If we're about to mark as Completed, validate amount
                            if ($paymentStatus == 3 && $gatewayAmount === null) {
                                $skipped++;
                                $errors[] = "Payment #{$payment->payment_order_id}: SKIPPED - no amount returned from gateway";
                                continue;
                            }
                            
                            // FINAL SAFETY CHECK: Compare gateway amount against expected DB amount
                            $expectedDbAmount = (float) ($payment->amount ?? 0);
                            if ($gatewayAmount !== null && $expectedDbAmount > 0) {
                                $amountDifference = abs($expectedDbAmount - $gatewayAmount);
                                $allowedDifference = max(1.0, $expectedDbAmount * 0.10);
                                
                                if ($amountDifference > $allowedDifference) {
                                    $skipped++;
                                    $errors[] = "Payment #{$payment->payment_order_id}: SKIPPED - amount mismatch (DB expects \${$expectedDbAmount}, gateway returned \${$gatewayAmount}, matched via: {$matchPath})";
                                    \Log::warning('Payment sync: amount mismatch', [
                                        'order_id' => $payment->payment_order_id,
                                        'db_amount' => $expectedDbAmount,
                                        'gateway_amount' => $gatewayAmount,
                                        'difference' => $amountDifference,
                                        'match_path' => $matchPath,
                                    ]);
                                    continue;
                                }
                            }
                            
                            // Use gateway amount if it passes validation, otherwise keep DB amount
                            $amountToSet = $gatewayAmount ?? $payment->paid_amount ?? $payment->amount;
                            
                            // Save snapshot of current state before update
                            $snapshot = [
                                'payment_id' => $payment->id,
                                'payment_order_id' => $payment->payment_order_id,
                                'registration_id' => $payment->registration_id,
                                'old_transaction_id' => $payment->transaction_id,
                                'old_transaction_date' => $payment->transaction_date,
                                'old_payment_mode_type' => $payment->payment_mode_type,
                                'old_paid_amount' => $payment->paid_amount,
                                'old_amount' => $payment->amount,
                                'old_currency' => $payment->currency,
                                'old_payment_status' => $payment->payment_status,
                                'old_payment_status_name' => $payment->payment_status_name,
                                'old_error_description' => $payment->error_description,
                                'old_auth_status' => $payment->auth_status,
                                'new_payment_status' => $paymentStatus,
                                'new_payment_status_name' => $paymentStatusName,
                                'new_paid_amount' => $amountToSet,
                                'gateway_status' => $gatewayStatus,
                                'match_path' => $matchPath,
                                'synced_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            ];
                            $snapshots[] = $snapshot;
                            
                            $changes[] = [
                                'order_id' => $payment->payment_order_id,
                                'old_status' => $payment->payment_status_name ?? $payment->payment_status,
                                'new_status' => $paymentStatusName,
                                'old_amount' => $payment->paid_amount,
                                'new_amount' => $amountToSet,
                                'match_path' => $matchPath,
                            ];
                            
                            if (!$dryRun) {
                                // Update payment record
                                $payment->update([
                                    'transaction_id' => $transaction['id'] ?? $transaction['number'] ?? $payment->transaction_id,
                                    'transaction_date' => isset($transaction['createdAt']) 
                                        ? Carbon::parse($transaction['createdAt'])->format("Y-m-d H:i:s")
                                        : ($payment->transaction_date ?? Carbon::now()->format("Y-m-d H:i:s")),
                                    'payment_mode_type' => $transaction['paymentMethod'] ?? $transaction['method'] ?? $payment->payment_mode_type,
                                    'paid_amount' => $amountToSet,
                                    'currency' => strtoupper($transaction['currency'] ?? $payment->currency ?? 'USD'),
                                    'payment_status' => $paymentStatus,
                                    'payment_status_name' => $paymentStatusName,
                                    'error_description' => $transaction['errorMessage'] ?? $transaction['failureReason'] ?? ($paymentStatus == 3 ? 'Transaction is successful' : $payment->error_description),
                                    'auth_status' => $paymentStatus == 3 ? '0300' : ($paymentStatus == 6 ? '0399' : '0002'),
                                ]);
                            }
                            
                            $synced++;
                        } 
                        // Handle fallback case where we only have payment data from database
                        elseif (isset($data['payment'])) {
                            $notFound++;
                            $errors[] = "Payment #{$payment->payment_order_id}: Found in database but not in payment gateway (may be incomplete)";
                        } else {
                            $failed++;
                            $errors[] = "Payment #{$payment->payment_order_id}: Invalid response format from gateway";
                        }
                    } elseif (strtoupper($responseStatus) === 'NOT_FOUND') {
                        $notFound++;
                        $errorMsg = $data['error'] ?? 'Transaction not found in gateway';
                        $errors[] = "Payment #{$payment->payment_order_id}: {$errorMsg}";
                        
                        // DON'T auto-mark as failed - this is dangerous
                        // Only log it for manual review
                        $paymentDate = $payment->transaction_date ?? $payment->payment_date ?? $payment->created_at;
                        if ($paymentDate) {
                            $daysOld = Carbon::parse($paymentDate)->diffInDays(Carbon::now());
                            if ($daysOld > 30 && in_array($payment->payment_status, [1, 2])) {
                                $errors[] = "Payment #{$payment->payment_order_id}: 30+ days old and not in gateway - may need manual review";
                            }
                        }
                    } elseif (strtoupper($responseStatus) === 'ERROR') {
                        $failed++;
                        $errorMsg = $data['error'] ?? 'Unknown error occurred';
                        $errors[] = "Payment #{$payment->payment_order_id}: {$errorMsg}";
                    } else {
                        $failed++;
                        $errorMsg = $data['error'] ?? 'Unexpected response status: ' . $responseStatus;
                        $errors[] = "Payment #{$payment->payment_order_id}: {$errorMsg}";
                    }
                } catch (\Exception $e) {
                    $failed++;
                    $errorMsg = $e->getMessage();
                    $errors[] = "Payment #{$payment->payment_order_id}: {$errorMsg}";
                    \Log::error('Payment sync exception', [
                        'payment_id' => $payment->id,
                        'order_id' => $payment->payment_order_id,
                        'error' => $errorMsg,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            // Store snapshots in cache/session for potential rollback (24 hour TTL)
            if (!empty($snapshots) && !$dryRun) {
                $syncBatchId = 'payment_sync_' . Carbon::now()->format('Y_m_d_H_i_s');
                \Cache::put($syncBatchId, $snapshots, now()->addHours(24));
                \Log::info('Payment sync completed - snapshot saved', [
                    'batch_id' => $syncBatchId,
                    'snapshots_count' => count($snapshots),
                    'synced' => $synced,
                    'failed' => $failed,
                    'skipped' => $skipped,
                    'not_found' => $notFound,
                ]);
            }
            
            $modeLabel = $dryRun ? '[DRY RUN] ' : '';
            $message = "{$modeLabel}Synced: {$synced}, Failed: {$failed}, Skipped: {$skipped}, Not Found: {$notFound}";
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'synced' => $synced,
                'failed' => $failed,
                'skipped' => $skipped,
                'not_found' => $notFound,
                'errors' => $errors,
                'dry_run' => $dryRun,
                'changes' => $dryRun ? $changes : [],
                'batch_id' => isset($syncBatchId) ? $syncBatchId : null,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Payment retrieve error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing payments: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Revert a payment sync batch using saved snapshots
     */
    public function revertPaymentSync(Request $request)
    {
        try {
            $batchId = $request->input('batch_id');
            
            if (!$batchId) {
                // Find the most recent sync batch
                // We'll search recent cache keys
                return response()->json([
                    'success' => false,
                    'message' => 'Batch ID is required. Check logs for the batch_id.'
                ], 400);
            }
            
            $snapshots = \Cache::get($batchId);
            
            if (!$snapshots || empty($snapshots)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No snapshot found for this batch. It may have expired (24h TTL) or the batch ID is incorrect.'
                ], 404);
            }
            
            $reverted = 0;
            $errors = [];
            
            foreach ($snapshots as $snapshot) {
                try {
                    $payment = Payment::find($snapshot['payment_id']);
                    if (!$payment) {
                        $errors[] = "Payment #{$snapshot['payment_order_id']}: Not found";
                        continue;
                    }
                    
                    $payment->update([
                        'transaction_id' => $snapshot['old_transaction_id'],
                        'transaction_date' => $snapshot['old_transaction_date'],
                        'payment_mode_type' => $snapshot['old_payment_mode_type'],
                        'paid_amount' => $snapshot['old_paid_amount'],
                        'currency' => $snapshot['old_currency'],
                        'payment_status' => $snapshot['old_payment_status'],
                        'payment_status_name' => $snapshot['old_payment_status_name'],
                        'error_description' => $snapshot['old_error_description'],
                        'auth_status' => $snapshot['old_auth_status'],
                    ]);
                    
                    $reverted++;
                } catch (\Exception $e) {
                    $errors[] = "Payment #{$snapshot['payment_order_id']}: " . $e->getMessage();
                }
            }
            
            \Log::info('Payment sync reverted', [
                'batch_id' => $batchId,
                'reverted' => $reverted,
                'errors_count' => count($errors),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Reverted {$reverted} of " . count($snapshots) . " payments",
                'reverted' => $reverted,
                'errors' => $errors,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Payment sync revert error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error reverting payments: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Manually revert payments that were updated during a specific time window
     * Use this to fix data after an accidental mass sync
     */
    public function revertPaymentsByTimestamp(Request $request)
    {
        try {
            $from = $request->input('from'); // e.g., '2026-03-18 03:56:50'
            $to = $request->input('to'); // e.g., '2026-03-18 03:58:00'
            $dryRun = $request->has('dry_run') ? $request->boolean('dry_run') : true; // Default to dry run for safety
            
            if (!$from || !$to) {
                return response()->json([
                    'success' => false,
                    'message' => 'Both "from" and "to" timestamps are required'
                ], 400);
            }
            
            $fromTime = Carbon::parse($from);
            $toTime = Carbon::parse($to);
            
            // Find payments updated during the sync window
            $affectedPayments = Payment::where('payment_mode', 'Online Transaction')
                ->whereBetween('updated_at', [$fromTime, $toTime])
                ->get();
            
            if ($affectedPayments->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "No payments found updated between {$from} and {$to}"
                ], 404);
            }
            
            $results = [];
            $reverted = 0;
            
            foreach ($affectedPayments as $payment) {
                $result = [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->payment_order_id,
                    'registration_id' => $payment->registration_id,
                    'registration_number' => $payment->registration ? $payment->registration->registration_number : 'N/A',
                    'current_status' => $payment->payment_status,
                    'current_status_name' => $payment->payment_status_name,
                    'current_paid_amount' => $payment->paid_amount,
                    'expected_amount' => $payment->amount, // Original expected amount (should not have been corrupted)
                    'updated_at' => $payment->updated_at ? $payment->updated_at->format('Y-m-d H:i:s') : null,
                ];
                
                if (!$dryRun) {
                    // Reset status AND paid_amount since the sync corrupted both
                    // Reset paid_amount to 0 (unknown true state) and status to Processing
                    $payment->update([
                        'payment_status' => 2,
                        'payment_status_name' => 'Processing',
                        'paid_amount' => 0,
                        'error_description' => 'Reverted from accidental sync on ' . Carbon::now()->format('Y-m-d H:i:s') . '. Previous: status=' . ($payment->payment_status_name ?? $payment->payment_status) . ', paid_amount=' . $payment->paid_amount . '. Expected amount: ' . $payment->amount . '. Needs manual verification.',
                    ]);
                    $result['action'] = 'Reverted (status→Processing, paid_amount→0)';
                    $reverted++;
                } else {
                    $result['action'] = 'Would revert (DRY RUN)';
                }
                
                $results[] = $result;
            }
            
            $modeLabel = $dryRun ? '[DRY RUN] ' : '';
            
            return response()->json([
                'success' => true,
                'message' => "{$modeLabel}Found {$affectedPayments->count()} payments. Reverted: {$reverted}",
                'total_found' => $affectedPayments->count(),
                'reverted' => $reverted,
                'dry_run' => $dryRun,
                'payments' => $results,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Payment revert by timestamp error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check if a payment exists in the payment gateway
     */
    public function checkPaymentStatus(Request $request)
    {
        $paymentId = $request->input('payment_id');
        
        if (!$paymentId) {
            return response()->json(['error' => 'Payment ID required'], 400);
        }
        
        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
        
        if ($payment->payment_mode !== 'Online Transaction') {
            return response()->json([
                'exists' => true,
                'message' => 'Direct Bank Transfer payments are not checked against gateway'
            ]);
        }
        
        $data = $this->retrieve_transaction($payment->payment_order_id);
        
        return response()->json([
            'exists' => $data['status'] === 'SUCCESS',
            'status' => $data['status'],
            'gateway_status' => $data['status'] ?? null,
            'local_status' => $payment->payment_status,
            'match' => $data['status'] === 'SUCCESS' && isset($data['transaction']),
            'data' => $data
        ]);
    }
}
