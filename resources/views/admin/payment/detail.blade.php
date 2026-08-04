@extends('admin.layouts.app')

@section('page-title', 'Payments')
@section('head')
<link rel="stylesheet" href="{{ url("libs/datepicker/daterangepicker.css")}}" type="text/css">
<style>
    .payment-detail {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }
    .payment-title {    
        font-weight: bold;
        color: #6c757d;
        margin-bottom: 5px;
    }
    .payment-value {
        font-size: 14px;
        color: #333;
    }
    
</style>
@endsection

@section('content')
<div class="mb-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#">
                    <i class="bi bi-globe2 small me-2"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Payment Detail</li>
        </ol>
    </nav>
</div>

<div class="row flex-column-reverse flex-md-row">
    <div class="col-md-10">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="payment-detail">
                            <span class="payment-title">Payment Order ID:</span>
                            <span class="payment-value">{{ $paymentData['payment_order_id'] }}</span>
                        </div>
                        <div class="payment-detail">
                            <span class="payment-title">Payment Mode:</span>
                            <span class="payment-value">{{ $paymentData['payment_mode'] }}</span>
                        </div>
                        <div class="payment-detail">
                            <span class="payment-title">Payment Amount:</span>
                            <span class="payment-value">{{ $paymentData['amount'] }}</span>
                        </div>
                        <div class="payment-detail">
                            <span class="payment-title">Payment Date:</span>
                            <span class="payment-value">{{ $paymentData['payment_date'] }}</span>
                        </div>
                        @if ($paymentData['payment_mode'] == 'Online Transaction')
                        <div class="payment-detail">
                            <span class="payment-title">BdOrder Id:</span>
                            <span class="payment-value">{{ $paymentData['bdOrderId'] }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="payment-detail">
                            <span class="payment-title">Email:</span>
                            <span class="payment-value">{{ $paymentData['email'] }}</span>
                        </div>
                        <div class="payment-detail">
                            <span class="payment-title">Payment Status:</span>
                            <span class="badge
                                @if ($paymentData['payment_status_id']=='3')
                                bg-success
                                @elseif ($paymentData['payment_status_id']=='6')
                                bg-danger
                                @else
                                bg-warning
                                @endif">{{ $paymentData['payment_status'] }}</span>
                        </div>
                        @if ($paymentData['payment_mode'] == 'Online Transaction')
                        <div class="payment-detail">
                            <span class="payment-title">Payment Category:</span>
                            <span class="payment-value">{{ $paymentData['payment_category'] }}</span>
                        </div>
                        @else
                        <div class="payment-detail">
                            <span class="payment-title">Payment Reference:</span>
                            <span class="payment-value">{{ $paymentData['payment_reference'] }}</span>
                        </div>
                        @endif
                        <div class="payment-detail">
                            <span class="payment-title">Currency:</span>
                            <span class="payment-value">{{ $paymentData['currency'] }}</span>
                        </div>
                    </div>
                </div>

                @if ($paymentData['payment_mode'] == 'Online Transaction')
                <div class="row mt-4">
                    <h5 class="mb-3">Transaction Details</h5>
                    <div class="col-lg-6">
                        <div class="payment-detail">
                            <span class="payment-title">Transaction ID:</span>
                            <span class="payment-value">{{ $paymentData['transaction_id'] }}</span>
                        </div>
                        <div class="payment-detail">
                            <span class="payment-title">Transaction Error Type:</span>
                            <span class="payment-value">{{ $paymentData['transaction_error_type'] }}</span>
                        </div>
                        <div class="payment-detail">
                            <span class="payment-title">Transaction Error Code:</span>
                            <span class="payment-value">{{ $paymentData['transaction_error_code'] }}</span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="payment-detail">
                            <span class="payment-title">Transaction Error Description:</span>
                            <span class="payment-value">{{ $paymentData['transaction_error_description'] }}</span>
                        </div>
                        <div class="payment-detail">
                            <span class="payment-title">Authorization Status:</span>
                            <span class="payment-value">{{ $paymentData['auth_status'] }}</span>
                        </div>
                        <div class="payment-detail">
                            <span class="payment-title">Transaction Date:</span>
                            <span class="payment-value">{{ $paymentData['transaction_date'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <h5 class="mb-3">Bank Details</h5>
                    <div class="col-lg-6">
                        <div class="payment-detail">
                            <span class="payment-title">Bank Reference Number:</span>
                            <span class="payment-value">{{ $paymentData['bank_reference_number'] }}</span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="payment-detail">
                            <span class="payment-title">Bank ID:</span>
                            <span class="payment-value">{{ $paymentData['bank_id'] }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
