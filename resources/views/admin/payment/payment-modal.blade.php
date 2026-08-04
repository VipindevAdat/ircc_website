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
    <div class="donate-form form-inner">
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
   
        @if($paymentData['payment_status_id']==1 || $paymentData['payment_status_id']==4)
        <div class="card">
        <div class="card-body">
        <div class="row d-flex justify-content-center" id="approvedDiv">  

            <div class="col-lg-12" >
            <div style="font-size: 12px;text-align: center;">
                    <h2 style="font-size: 18px;" class="text-danger">Payment Verification Pending</h2>
            </div>
            </div>
            <div class="col-lg-10 pt-4  justify-content-center">

            <ul class="nav nav-pills nav-fill" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#approve" role="tab" aria-controls="approve" aria-selected="true">Verify</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#reject" role="tab" aria-controls="reject" aria-selected="false">Reject</a>
            </li>
            </ul>
            <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active bg-gradient" id="approve" role="tabpanel" aria-labelledby="approve-tab">
            <div class="col-lg-12 col-12 pt-4 form-group" style="text-align: center;">
                <span class="payment-title">Comments</span>
                <textarea class="form-control" id="comments" name="comments" rows="5" placeholder=""></textarea>
            </div>
            <div class="col-lg-12 col-12 pt-4 form-group" style="text-align: right;">
            <a href="javascript:void(0);" class="btn btn-success approve_payment me-1"  data-status="3" data-id="{{ $paymentData['payment_order_id'] }}">Verify Payment</a>
            </div>
            </div>
            <div class="tab-pane fade" id="reject" role="tabpanel" aria-labelledby="reject-tab">
                <div class="col-lg-12 col-12 pt-4 form-group" style="text-align: center;">
                <span class="payment-title">Reject reason</span>
                <textarea class="form-control" id="comments" name="comments" rows="5" placeholder=""></textarea>
                 </div>
                 <div class="col-lg-12 col-12 pt-4 form-group" style="text-align: right;">
                 <a href="javascript:void(0);" class="btn btn-danger approve_payment me-1" data-status="4" data-id="{{ $paymentData['payment_order_id'] }}">Reject Payment</a>
                 </div>
                </div>
           
        
            </div>
          
                      
        </div>
        </div>
        </div>
       @endif
       @if($paymentData['payment_status_id']==3)
        <div class="card">
        <div class="card-body">
        <div class="row d-flex justify-content-center" id="approvedDiv">  

            <div class="col-lg-12" >
            <div style="font-size: 12px;text-align: center;">
                    <h2 style="font-size: 18px;" class="text-success">Payment Verfied</h2>
            </div>
            </div>
            <div class="col-lg-10 pt-4  justify-content-center">

            <ul class="nav nav-pills nav-fill" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link " id="home-tab" data-bs-toggle="tab" href="#approve" role="tab" aria-controls="approve" aria-selected="true">Verify</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#reject" role="tab" aria-controls="reject" aria-selected="false">Reject</a>
            </li>
            </ul>
            <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade  bg-gradient" id="approve" role="tabpanel" aria-labelledby="approve-tab">
            <div class="col-lg-12 col-12 pt-4 form-group" style="text-align: center;">
                <span class="payment-title">Comments</span>
                <textarea class="form-control" id="comments" name="comments" rows="5" placeholder=""></textarea>
            </div>
            <div class="col-lg-12 col-12 pt-4 form-group" style="text-align: right;">
            <a href="javascript:void(0);" class="btn btn-success approve_payment me-1"  data-status="3" data-id="{{ $paymentData['payment_order_id'] }}">Approve Payment</a>
            </div>
            </div>
            <div class="tab-pane fade show active" id="reject" role="tabpanel" aria-labelledby="reject-tab">
                <div class="col-lg-12 col-12 pt-4 form-group" style="text-align: center;">
                <span class="payment-title">Reject reason</span>
                <textarea class="form-control" id="comments" name="comments" rows="5" placeholder=""></textarea>
                 </div>
                 <div class="col-lg-12 col-12 pt-4 form-group" style="text-align: right;">
                 <a href="javascript:void(0);" class="btn btn-danger approve_payment me-1" data-status="4" data-id="{{ $paymentData['payment_order_id'] }}">Reject Payment</a>
                 </div>
                </div>
           
        
            </div>
          
                      
        </div>
        </div>
        </div>
       @endif
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
