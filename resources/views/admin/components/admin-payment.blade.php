<style>
    .payment-title {
        font-weight: bold;
        color: #6c757d;
        margin-right: 10px;
    }
    .table td {
        padding: 8px 15px;
        vertical-align: middle;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title" id="abstractModalLabel">Payment Details</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="donate-form form-inner" style="margin-bottom: 20px !important;">
        <div class="row">
            <div class="col-lg-6">
                <table class="table table-borderless mb-2">
                    <tbody>
                        <tr>
                            <td><span class="payment-title">Payment Order ID:</span></td>
                            <td>{{ $paymentData['payment_order_id'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="payment-title">Payment Mode:</span></td>
                            <td>{{ $paymentData['payment_mode'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="payment-title">Payment Amount:</span></td>
                            <td>{{ $paymentData['amount'] }}</td>
                        </tr>
                        @if (! empty($paymentData['coupon_code']))
                        <tr>
                            <td><span class="payment-title">Coupon:</span></td>
                            <td>{{ $paymentData['coupon_code'] }}</td>
                        </tr>
                        @endif
                        @if (! empty($paymentData['original_amount']))
                        <tr>
                            <td><span class="payment-title">Amount before coupon:</span></td>
                            <td>{{ $paymentData['original_amount'] }}</td>
                        </tr>
                        @endif
                        @if (! empty($paymentData['discount_amount']))
                        <tr>
                            <td><span class="payment-title">Discount:</span></td>
                            <td>{{ $paymentData['discount_amount'] }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><span class="payment-title">Payment Date:</span></td>
                            <td>{{ $paymentData['payment_date'] }}</td>
                        </tr>
                        @if ($paymentData['payment_mode']=='Online Transaction')
                            <tr>
                                <td><span class="payment-title">BdOrder Id:</span></td>
                                <td>{{ $paymentData['bdOrderId'] }}</td>
                            </tr>
                        @endif
                        @if($paymentData['comments'])
                        <tr>
                            <td><span class="payment-title">Comments:</span></td>
                            <td>{{ $paymentData['comments'] }}</td>
                           
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="col-lg-6">
                <table class="table table-borderless mb-2">
                    <tbody>
                        <tr>
                            <td><span class="payment-title">Email:</span></td>
                            <td>{{ $paymentData['email'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="payment-title">Payment Status:</span></td>
                            <td><span class=" badge
                                @if ($paymentData['payment_status_id']=='3')
                                bg-success
                                @elseif ($paymentData['payment_status_id']=='6')
                                bg-danger
                                @else
                                bg-warning
                                @endif">{{ $paymentData['payment_status']}}</span></td>
                        </tr>
                        @if ($paymentData['payment_mode']=='Online Transaction')
                            <tr>
                                <td><span class="payment-title">Payment Category:</span></td>
                                <td>{{ $paymentData['payment_category'] }}</td>
                            </tr>
                        @else
                            <tr>
                                <td><span class="payment-title">Payment Reference:</span></td>
                                <td>{{ $paymentData['payment_reference'] }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td><span class="payment-title">Currency:</span></td>
                            <td>{{ $paymentData['currency'] }}</td>
                           
                        </tr>
                      
                    </tbody>
                </table>
            </div>
        </div>
   
        </div>
        @if ($paymentData['payment_mode'] == 'Online Transaction')
            <div class="row mt-4">
                <h5 class="mb-3">Transaction Details</h5>
                <div class="col-lg-6">
                    <table class="table table-borderless mb-2">
                        <tbody>
                            <tr>
                                <td><span class="payment-title">Transaction ID:</span></td>
                                <td>{{ $paymentData['transaction_id'] }}</td>
                            </tr>
                            <tr>
                                <td><span class="payment-title">Transaction Error Type:</span></td>
                                <td>{{ $paymentData['transaction_error_type'] }}</td>
                            </tr>
                            <tr>
                                <td><span class="payment-title">Transaction Error Code:</span></td>
                                <td>{{ $paymentData['transaction_error_code'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-borderless mb-2">
                        <tbody>
                            <tr>
                                <td><span class="payment-title">Transaction Error Description:</span></td>
                                <td>{{ $paymentData['transaction_error_description'] }}</td>
                            </tr>
                            <tr>
                                <td><span class="payment-title">Authorization Status:</span></td>
                                <td>{{ $paymentData['auth_status'] }}</td>
                            </tr>
                            <tr>
                                <td><span class="payment-title">Transaction Date:</span></td>
                                <td>{{ $paymentData['transaction_date'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row mt-4">
                    <h5 class="mb-3">Bank Details</h5>
                        <div class="col-lg-6">
                            <table class="table table-borderless mb-2">
                                <tbody>
                                    <tr>
                                        <td><span class="payment-title">Bank Reference Number:</span></td>
                                        <td>{{ $paymentData['bank_reference_number'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6">
                            <table class="table table-borderless mb-2">
                                <tbody>
                                    <tr>
                                        <td><span class="payment-title">Bank ID:</span></td>
                                        <td>{{ $paymentData['bank_id'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>  
        @endif          
    </div>
</div>
