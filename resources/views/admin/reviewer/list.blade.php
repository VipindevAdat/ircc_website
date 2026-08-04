@extends('admin.layouts.app')

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
                <li class="breadcrumb-item active" aria-current="page">Reviewer</li>
            </ol>
        </nav>
    </div>

    <div class="content">
        <div class="">
            <div class="card">
                <div class="card-body">
                    <div class="d-md-flex gap-4 align-items-center">
                        <div class="d-none d-md-flex">All Reviewers</div>
                        <div class="d-md-flex gap-4 align-items-center">
                            <form class="mb-3 mb-md-0">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <select class="form-select" id="sort">
                                            <option>Sort by</option>
                                            <option data-sort="asc" data-column="1" value="">Name A-z</option>
                                            <option data-sort="desc" data-column="1" value="">Name Z-a</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="dropdown ms-auto">
                            <a href="{{ route('admin.reviewer.create') }}" class="btn btn-info">
                                <i class="bi bi-plus"></i>
                                Add Reviewer</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-lg mb-0" id="reviewers">
                    <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Reviewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="delete-log-form"  method="POST">
                    <div class="modal-body">
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="deleteId" name="deleteId">
                        <p>Are you sure you want to delete this Reviewer</p>
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
    <script src="{{ url('libs/dataTable/datatables.min.js') }}"></script>
    <script src="{{ url('libs/range-slider/js/ion.rangeSlider.min.js') }}"></script>

    <script type="text/javascript">
        @if(Session::has('success_message'))
        $(document).ready( function () {
            Swal.fire({
                icon: 'success',
                title: '{{ Session::get('success_message') }}',
                // text: 'we will notify through email once the adjustment is complete',
                footer: ''
            })
        })
        @endif

        $(document).ready(function() {
            var $column = $('#sort').find(':selected').data('column');
            var $sort = $('#sort').find(':selected').data('sort');

            $reviewerTable = $('#reviewers').DataTable({
                //order: [[$column==undefined?'4':$column, $sort==undefined?'desc':$sort]],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reviewer.index') }}',
                    data: function(d) {
                        // Custom data if needed
                    }
                },
                columns: [
                    {
                        data: 'full_name',
                        name: 'full_name',
                    },
                    {
                        data: 'email',
                        name: 'email',
                    },
                    {
                        data: 'category',
                        name: 'category',
                        searchable: false,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ],
                columnDefs: [{
                    'defaultContent': '--',
                    "targets": "_all"
                }],
                order: [[($column === undefined ? 1 : $column), ($sort === undefined ? 'desc' : $sort)]], // Set default sorting
            });

        $(document).on("keyup", ".searchInput", function(e) {
            $reviewerTable.search($(this).val()).draw();
        });
        $('#pageLength').on('change', function() {
            $reviewerTable.page.len($(this).val()).draw();
        });
        $('#pageLength').val($reviewerTable.page.len());

        $("#reviewers_filter").css({
            "display": "none"
        });
        $("#reviewers_length").css({
            "display": "none"
        });

        $('.filter').change(function() {
            $reviewerTable.draw();
        });

        $('#sort').on('change', function() {
            $column = $(this).find(':selected').data('column');
            $sort = $(this).find(':selected').data('sort');
            $reviewerTable.order([$column, $sort]).draw();
        })
        });

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
                    //data:{},
                    dataType : 'JSON',
                    url : href,
                    success: function(response){
                        $('#delete-modal').modal('hide');
                        $('#reviewers').DataTable().ajax.reload();
                        // toastr.success("plan deleted successfully", "Success");
                        Swal.fire({
                            icon: 'success',
                            title: 'Reviewer deleted successfully',
                            // text: 'we will notify through email once the adjustment is complete',
                            footer: ''
                        })
                    }
                });
            });
        });
    </script>
@endsection
