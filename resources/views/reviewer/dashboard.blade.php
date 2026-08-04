@extends('reviewer.layouts.app')

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
                    <a href="{{ route('reviewer.abstracts') }}">
                        <div class="card border-0">
                            <div class="card-body text-center">
                                <div class="display-5">
                                    <!-- <i class="bi bi-receipt text-warning"></i> -->
                                    <i class="bi bi-card-list  text-danger"></i>
                                </div>
                                <h5 class="my-3">Abstract</h5>
                                <div class="text-muted"> New Abstract</div>
                                <div class="progress mt-3" style="height: 5px">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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
