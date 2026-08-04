@extends('admin.layouts.app')

@section('page-title', 'Contact Us')

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
                <li class="breadcrumb-item active" aria-current="page">Contact us</li>
            </ol>
        </nav>
    </div>

    <div class="content">
        <div class="">
            <div class="card">
                <div class="card-body">
                    <div class="d-md-flex gap-4 align-items-center">
                        <div class="d-none d-md-flex">All Enquiries</div>
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
                        
                       
                    </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="registrations">
                    <thead>
                      <tr>
                         <th>Name</th>  
                         <th>Email</th>  
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                                    

                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Registration Details</h5>
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
        <!-- Range slider -->
        <script src="{{ url('libs/dataTable/datatables.min.js') }}"></script>
        <script src="{{ url('libs/range-slider/js/ion.rangeSlider.min.js') }}"></script>

<script type="text/javascript">

   $(document).ready(function() {
            var $column = $('#sort').find(':selected').data('column');
            var $sort = $('#sort').find(':selected').data('sort');
            $registrationTable = $('#registrations').DataTable({
                //order: [[$column==undefined?'4':$column, $sort==undefined?'desc':$sort]],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.contact-us.index') }}',
                    data: function(d) {
                        
                    }
                },

                columns: [
                    {
                        data: 'name',
                        name: 'name'
                    },
                
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'subject',
                        name: 'subject'
                    },
                    {
                        data: 'message',
                        name: 'message'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                   
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
// $(function() {
//   'use strict';

//   $(function() {
//  var table= $('#dataTableExample').DataTable({
//          "dom": 'Blfrtip',
//          "lengthMenu": [
//              [10, 30, 50,100, -1],
//             [10, 30, 50,100, "All"]
//         ],
         
   
//       "iDisplayLength": 10,
//       "language": {
//         search: ""
//       },
//       "aaSorting": []
//     });
//     $('#dataTableExample').each(function() {
//       var datatable = $(this);
//       // SEARCH - Add the placeholder for Search and Turn this into in-line form control
//       var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
//       search_input.attr('placeholder', 'Search');
//       search_input.removeClass('form-control-sm');
//       // LENGTH - Inline-Form control
//       var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
//       length_sel.removeClass('form-control-sm');
//     });
//   });

// });
</script>
    @endsection
