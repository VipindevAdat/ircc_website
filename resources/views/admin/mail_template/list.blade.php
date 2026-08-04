@extends('admin.layouts.app')

@section('page-title', 'Mail Template')

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
                <li class="breadcrumb-item active" aria-current="page">Mail Template</li>
            </ol>
        </nav>
    </div>

    <div class="content">
        <div class="">
            <div class="card">
                <div class="card-body">
                    <div class="d-md-flex gap-4 align-items-center">
                        <div class="d-none d-md-flex">All Mail Template</div>
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
                        <div class="dropdown ms-auto">

                                <a href="{{route('admin.mailTemplate.create')}}" class="btn btn-info">
                                <i class="bi bi-plus"></i>
                                New Template</a>
                                </div>
                       
                    </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="registrations">
                    <thead>
                      <tr>
                         <th>Name</th>  
                         <th>Subject</th>  
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                                    

                    </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirm-mail-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Send Mail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="delete-log-form" action="{{route('admin.sendMailSubmit')}}" method="POST">
            <div class="modal-body">
                    <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="templateId" name="templateId">
                        <p>Are you sure you want to send this mail 

                        </p>
                        <div class="modal-footer">
                        
                            <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-danger" data-loading-text="">Confirm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>   
        </div> 
        <div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Delete Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="delete-log-form" action="{{route('admin.deleteTemplate')}}" method="POST">
            <div class="modal-body">
                    <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="deleteId" name="deleteId">
                        <p>Are you sure you want to delete this mail template</p>
                        <div class="modal-footer">
                        
                            <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-danger" data-loading-text="">Delete</button>
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
        // text: 'we will notify through email once the adjustment is complete',
        footer: ''
    })
})
@endif
$(document).ready(function() {
    var $column = $('#sort').find(':selected').data('column');
    var $sort = $('#sort').find(':selected').data('sort');
    $registrationTable = $('#registrations').DataTable({
        //order: [[$column==undefined?'4':$column, $sort==undefined?'desc':$sort]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.mailTemplate.index') }}',
            data: function(d) {
                
            }
        },

        columns: [
           
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'subject',
                name: 'subject'
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
    $(document).on("click",".confirm_send",function() {
        $('#templateId').val($(this).data('id'));

    });
    

});
// $('#pageLength').on('change', function() {
//     $registrationTable.page.len($(this).val()).draw();
// });
// $('#pageLength').val($registrationTable.page.len());

// $("#registrations_filter").css({
//     "display": "none"
// });
// $("#registrations_length").css({
//     "display": "none"
// });

// $('.filter').change(function() {
//     $registrationTable.draw();
// });

// $('#sort').on('change', function() {
//     $column = $(this).find(':selected').data('column');
//     $sort = $(this).find(':selected').data('sort');
//     $registrationTable.order([$column, $sort]).draw();
// })

</script>
    @endsection
