@extends('admin.layouts.app')

@section('page-title', 'Category Class')

@section('head')
    <link rel="stylesheet" href="{{ url('libs/dataTable/datatables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ url('libs/range-slider/css/ion.rangeSlider.min.css') }}" type="text/css">
    <style>
        .table-responsive {
            overflow-x: scroll !important;
        }

        .table-responsive::-webkit-scrollbar {
            margin-top: 20px;
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #e3e3e3;
            border-radius: 20px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #d02027;
            border-radius: 20px;
        }
    </style>
@endsection


@section('content')

    <div class="mb-4">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-globe2 small me-2"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Category Class</li>
            </ol>
        </nav>
    </div>

    <div class="content">
        <div class="">
            <div class="card">
                <div class="card-body">
                    <div class="d-md-flex gap-4 align-items-center">
                        <div class="d-none d-md-flex">All Category Classes</div>
                        <div class="d-md-flex gap-4 align-items-center">
                            <form class="mb-3 mb-md-0">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <select class="form-select" id="sort">
                                            <option>Sort by</option>
                                            <option data-sort="asc" data-column="1" value="">Name A-z</option>
                                            <option data-sort="desc" data-column="1" value=""> Name Z-a
                                            </option>
                                        </select>
                                    </div>



                                </div>
                            </form>
                        </div>

{{--                        <div class="dropdown ms-auto">--}}

{{--                            <a href="{{route('admin.category-class.create')}}" class="btn btn-info">--}}
{{--                                <i class="bi bi-plus"></i>--}}
{{--                                New Category Class</a>--}}
{{--                        </div>--}}

                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-lg mb-0" id="registrations">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Class</th>
                            <th>Status</th>
{{--                            <th>Action</th>--}}
                        </tr>
                    </thead>
                    <tbody>


                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="delete-log-form"  method="POST">
                    <div class="modal-body">
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="deleteId" name="deleteId">
                        <p>Are you sure you want to delete this class</p>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-sm btn-danger btn_delete_review" data-loading-text="">Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection


@section('script')
    <!-- Range slider -->
    <script src="{{ url('libs/dataTable/datatables.min.js') }}"></script>
    <script src="{{ url('libs/range-slider/js/ion.rangeSlider.min.js') }}"></script>

    <script type="text/javascript">
        @if(Session::has('success_message'))
        $(document).ready( function () {
            Swal.fire({
                icon: 'success',
                title: '{{ Session::get('success_message') }}',
                footer: ''
            })
        })
        @endif
        $(document).ready(function() {
            var $column = $('#sort').find(':selected').data('column');
            var $sort = $('#sort').find(':selected').data('sort');
            $registrationTable = $('#registrations').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.category-class.index') }}',
                    data: function(d) {

                    }
                },

                columns: [

                    {
                        data: 'category_id',
                        name: 'category_id'
                    },
                    {
                        data: 'class',
                        name: 'class'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    // {
                    //     data: 'action',
                    //     name: 'action'
                    // }
                ],
                columnDefs: [{
                    'defaultContent': '--',
                    "targets": "_all"
                }],
            });

            $(document).on("keyup", ".searchInput", function(e) {
                $registrationTable.search($(this).val()).draw();
            });
            $('#pageLength').on('change', function() {
                $registrationTable.page.len($(this).val()).draw();
            });
            $('#pageLength').val($registrationTable.page.len());

            $("#registrations_filter").css({
                "display": "none"
            });
            $("#registrations_length").css({
                "display": "none"
            });

            $('.filter').change(function() {
                $registrationTable.draw();
            });

            $('#sort').on('change', function() {
                $column = $(this).find(':selected').data('column');
                $sort = $(this).find(':selected').data('sort');
                $registrationTable.order([$column, $sort]).draw();
            })
            $(document).on("click",".delete-template",function() {
                $('#deleteId').val($(this).data('id'));

            });
            $('table').off('click').on('click','.delete-review', function(e){
                var href=$(this).data('href');
                console.log(href)
                $('.btn_delete_review').click(function(){
                    console.log('delete')
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: 'DELETE',
                        dataType : 'JSON',
                        url : href,
                        success: function(response){
                            $('#delete-modal').modal('hide');
                            $('#registrations').DataTable().ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Class deleted successfully',
                                footer: ''
                            })
                        }
                    });
                });
            });


        });
    </script>

@endsection
