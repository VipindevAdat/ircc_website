@extends('admin.layouts.app')

@section('page-title', 'Overview')

@section('head')

@endsection

{{-- @section('header-action-button')
    <a href="{{ route('fetchProducts') }}" class="btn btn-primary btn-icon">
        <i class="bi bi-plus-circle"></i> Add Product
    </a>
@endsection --}}

@section('content')
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col-lg-12 col-md-12">
            <div class="card widget">
                <div class="card-header">
                    <h5 class="card-title"></h5>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                    <a href="{{ route('admin.registration.index') }}">
                        <div class="card border-0">
                            <div class="card-body text-center">
                                <div class="display-5">
                                    <i class="bi bi-person text-secondary"></i>
                                </div>
                                <h5 class="my-3">Registration</h5>
                                <div class="text-muted">{{$registerCount}} New Registration</div>
                                <div class="progress mt-3" style="height: 5px">
                                    <div class="progress-bar bg-secondary" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    </div>
                    
                    <div class="col-md-6">
                    <a href="{{ route('admin.registration.participants') }}">
                        <div class="card border-0">
                            <div class="card-body text-center">
                                <div class="display-5">
                                    <i class="bi bi-people text-primary"></i>
                                </div>
                                <h5 class="my-3">Participants</h5>
                                <div class="text-muted">{{$participantsCount}} Total Participants</div>
                                <div class="progress mt-3" style="height: 5px">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    </div>
                    <div class="col-md-6">
                    <a href="{{ route('admin.abstract.index') }}">
                        <div class="card border-0">
                            <div class="card-body text-center">
                                <div class="display-5">
                                    <!-- <i class="bi bi-receipt text-warning"></i> -->
                                    <i class="bi bi-card-list  text-danger"></i>
                                </div>
                                <h5 class="my-3">Abstract</h5>
                                <div class="text-muted">{{$abstractCount}} New Abstract</div>
                                <div class="progress mt-3" style="height: 5px">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    </div>

                    <div class="col-md-6">
                    <a href="{{ route('admin.payment.index') }}">
                        <div class="card border-0">
                            <div class="card-body text-center">
                                <div class="display-5">
                                    <i class="bi bi-credit-card-2-front text-success"></i>
                                </div>
                                <h5 class="my-3">Payment Completed</h5>
                                <div class="text-muted">{{$paymentCompletedCount}} Completed Payments</div>
                                <div class="progress mt-3" style="height: 5px">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    </div>

                    <div class="col-md-6">
                    <a href="{{ route('admin.abstract.index') }}">
                        <div class="card border-0">
                            <div class="card-body text-center">
                                <div class="display-5">
                                    <i class="bi bi-file-earmark-check text-info"></i>
                                </div>
                                <h5 class="my-3">Abstract (Payment Completed)</h5>
                                <div class="text-muted">{{$abstractCompletedCount}} Abstracts with Completed Payment</div>
                                <div class="progress mt-3" style="height: 5px">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    </div>
              
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<!-- Apex chart -->
<script src="{{ url("libs/charts/apex/apexcharts.min.js") }}"></script>

<!-- Slick -->
<script src="{{ url("libs/slick/slick.min.js") }}"></script>

<!-- Examples -->
<script src="{{ url("dist/js/examples/dashboard.js") }}"></script>
@endsection
