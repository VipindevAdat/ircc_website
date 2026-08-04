@extends('admin.layouts.app')

@section('page-title', 'Abstract')

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
            /* width of the entire scrollbar */
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #e3e3e3;
            /* color of the tracking area */
            border-radius: 20px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #d02027;
            /* color of the scroll thumb */
            border-radius: 20px;
            /* roundness of the scroll thumb */
            /* border: 0.5px solid #117dc0;  creates padding around scroll thumb */
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
                <li class="breadcrumb-item active" aria-current="page">Abstract</li>
            </ol>
        </nav>
    </div>

    <div class="content">
        <div class="">
            <div class="card">
            <form method="POST" action="{{route('admin.exportRegistration')}}" >
                @csrf
                <input type="hidden" name="type" value="abstract">
                <div class="card-body">
                    <div class="d-md-flex gap-4 align-items-center">
                        <div class="d-none d-md-flex">All Abstracts</div>
                        <div class="d-md-flex gap-4 align-items-center">

                                <div class="row g-3">
                                <div class="col-md-12">
                                    <select class="form-select" id="pageLength">
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="30">30</option>
                                        <option value="40">40</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>


                                </div>

                        </div>
                        <div class="ms-auto">
                            <button type="submit"  class="btn btn-info mt-4"><i class="fa fa-upload"></i> Export XLS</button>
                        </div>

                    </div>
                    </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="abstractTable">
                    <thead>
                      <tr>
                      <th>AbstractId</th>
                      <th>Registration</th>
                      <th>Title</th>
                      <th>Institute Name</th>
                      <th>Student Authors</th>
                      <th>View Abstract</th>
                      <th>Review Status</th>
                      <th>Category</th>
                      <th>Abstract Domain</th>
                      <th>Reviewer</th>
                      <th>Score</th>
                      <th>Media Download</th>
                       <!--  <th>Action</th> -->
                      </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    </table>
                </div>
            </div>
        </div>

    <div class="modal fade" id="abstractModal" tabindex="-1" aria-labelledby="abstractModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <pre id="abstractContent"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

        <div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Delete Abstract</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="delete-log-form"  method="POST">
      <div class="modal-body">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="deleteId" name="deleteId">
                <div id="msg_div"></div>
                <p>Are you sure you want to delete this abstract</p>
                <div class="modal-footer">

                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-danger btn_delete_abstract" data-loading-text="">Confirm Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Abstract Details</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="detailBody">
        <div class="row">
            <div class="col-4">
            Name :
            </div>
            <div class="col-8">
            Rajesh MM
            </div>

        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        </div>
        </div>
    </div>
    </div>
    @endsection


    @section('script')
        <script>
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
        </script>
        <!-- Range slider -->
        <script src="{{ url('libs/dataTable/datatables.min.js') }}"></script>
        <script src="{{ url('libs/range-slider/js/ion.rangeSlider.min.js') }}"></script>
        <script type="text/javascript">

            $(document).on('change', '.reviewer-select', function() {
                var abstractId = $(this).data('id');  
                var reviewerId = $(this).val(); 

                $.ajax({
                    url: '{{ route('admin.assignReviewer') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        abstract_id: abstractId,
                        reviewer_id: reviewerId,
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.success,
                            confirmButtonText: 'OK'
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.responseJSON.error,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

               $(document).ready(function() {
            var $column = $('#sort').find(':selected').data('column');
            var $sort = $('#sort').find(':selected').data('sort');
            $abstractTable = $('#abstractTable').DataTable({
                //order: [[$column==undefined?'4':$column, $sort==undefined?'desc':$sort]],
                processing: true,
                serverSide: true,
                // dom:'lBfrtip',
                ajax: {
                    url: '{{ route('admin.abstract.index') }}',
                    data: function(d) {

                    }
                },

                columns: [

                    {
                        data: 'pid',
                        name: 'pid'
                    },
                    {
                        data: 'registration_number',
                        name: 'registration_number'
                    },
                    {
                        data: 'abstract_title',
                        name: 'abstract_title'
                    },
                    {
                        data: 'institute_name',
                        name: 'institute_name',
                    },

                    {
                        data: 'student_authors',
                        name: 'student_authors'
                    },
                   
                    {
                        data: 'view',
                        name: 'view',
                        render: function(data, type, row) {
                            return '<i class="fa fa-eye view-abstract" data-id="' + row.id + '" style="cursor: pointer"></i>';
                        },
                        searchable:false
                    },
                    {
                        data: 'review_status',
                        name: 'review_status'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'abstract_domain',
                        name: 'abstract_domain'
                    },
                    {
                        data: 'reviewer',
                        name: 'reviewer'
                    },
                    
                    {
                        data: 'score',
                        name: 'score'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ],
                columnDefs: [{
                    'defaultContent': '--',
                    "targets": "_all"
                }],
                createdRow: function (row, data, dataIndex) {
                    if (data.row_style) {
                        $(row).attr('style', data.row_style);
                    }
                }
                // responsive: false, "lengthChange": false, "autoWidth": false,
                // buttons: ["excel"]
            });

            $(document).on("keyup", ".searchInput", function(e) {
                $abstractTable.search($(this).val()).draw();
            });
            $('#pageLength').on('change', function() {
                $abstractTable.page.len($(this).val()).draw();
            });
            $('#pageLength').val($abstractTable.page.len());

            $("#abstractTable_filter").css({
                "display": "none"
            });
            $("#abstractTable_length").css({
                "display": "none"
            });

            $('.filter').change(function() {
                $abstractTable.draw();
            });

            $('#sort').on('change', function() {
                $column = $(this).find(':selected').data('column');
                $sort = $(this).find(':selected').data('sort');
                $abstractTable.order([$column, $sort]).draw();
            })
            $('#pageLength').on('change', function() {
            $abstractTable.page.len($(this).val()).draw();
        });
        $('#pageLength').val($abstractTable.page.len());


        });


        $("#abstractTable_filter").css({
            "display": "none"
        });
        $("#abstractTable_length").css({
            "display": "none"
        });

        $('.filter').change(function() {
            $abstractTable.draw();
        });

        $('#sort').on('change', function() {
            $column = $(this).find(':selected').data('column');
            $sort = $(this).find(':selected').data('sort');
            $abstractTable.order([$column, $sort]).draw();
        })

               $(document).on('click', '.view-abstract', function() {
                   var id = $(this).data('id');
                   $.ajax({
                       type: 'GET',
                       data: { id: id },
                       dataType: 'html',
                       url: '{{ route('admin.abstractView') }}',
                       success: function(response) {
                           $('#abstractModal .modal-body').html(response);
                           $('#abstractModal').modal('show');
                       },
                       error: function(xhr, status, error) {
                           console.log(error);
                       }
                   });
               });


               $('table').off('click').on('click','.delete-abstract', function(e){
            var href=$(this).data('href');
            console.log(href)
            $('.btn_delete_abstract').click(function(){
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'DELETE',
                    //data:{},
                    dataType : 'JSON',
                    url : href,
                    success: function(response){
                        $('#delete-modal').modal('hide');
                        $('#abstractTable').DataTable().ajax.reload();
                        // toastr.success("plan deleted successfully", "Success");
                        Swal.fire({
                            icon: 'success',
                            title: 'Abstract deleted successfully',
                            // text: 'we will notify through email once the adjustment is complete',
                            footer: ''
                        })
                    }
                });
            });
        });
</script>

    @endsection
