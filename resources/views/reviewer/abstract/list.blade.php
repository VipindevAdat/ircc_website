@extends('reviewer.layouts.app')

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
                        <!-- <div class="ms-auto">
                            <button type="submit"  class="btn btn-info mt-4"><i class="fa fa-upload"></i> Export XLS</button>
                        </div> -->

                    </div>
                    </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="abstractTable">
                    <thead>
                      <tr>
                        <th>Title</th>
                        <th>View Abstract</th>
                        <th>Clarity</th>
                        <th>Relevance</th>
                        <th>Originality</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Comments</th>
                        <th>Action</th>
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

    <div class="modal fade" id="scoreModal" tabindex="-1" aria-labelledby="scoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <pre id="scoreContent"></pre>
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

               $(document).ready(function() {
            var $column = $('#sort').find(':selected').data('column');
            var $sort = $('#sort').find(':selected').data('sort');
            $abstractTable = $('#abstractTable').DataTable({
                //order: [[$column==undefined?'4':$column, $sort==undefined?'desc':$sort]],
                processing: true,
                serverSide: true,
                // dom:'lBfrtip',
                ajax: {
                    url: '{{ route('reviewer.abstracts') }}',
                    data: function(d) {

                    }
                },

                columns: [

                    {
                        data: 'title',
                        name: 'abstract.abstract_title'
                    },
                    {
                        data: 'view',
                        name: 'view',
                        render: function(data, type, row) {
                            return '<i class="fa fa-eye view-abstract" data-id="' + row.id + '" style="cursor: pointer"></i>';
                        }
                    },
                    {
                        data: 'clarity',
                        name: 'clarity',
                    },

                    {
                        data: 'relevance',
                        name: 'relevance'
                    },
                    {
                        data: 'originality',
                        name: 'originality',
                    },
                    {
                        data: 'score',
                        name: 'score'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'comments',
                        name: 'comments'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        render: function(data, type, row) {
                            return '<i class="fa fa-star-o add-score" data-id="' + row.id + '" style="cursor: pointer; color: #35b563; font-size: 1.2em;"></i>' +
                                '<span class="ml-1 add-score" data-id="' + row.id + '" style="cursor: pointer; color: #35b563; font-size: 14px"> Add Score</span>';
                        }

                    }
                ],
                columnDefs: [{
                    'defaultContent': '--',
                    "targets": "_all"
                }],
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
               url: '{{ route('reviewer.abstractView') }}',
               success: function(response) {
                   $('#abstractModal .modal-body').html(response);
                   $('#abstractModal').modal('show');
               },
               error: function(xhr, status, error) {
                   console.log(error);
               }
           });
       });

       $(document).on('click', '.add-score', function() {
        var id = $(this).data('id');
        $.ajax({
            type: 'GET',
            data: { id: id },
            dataType: 'html',
            url: '{{ route('reviewer.addScore') }}',
            success: function(response) {
                $('#scoreModal .modal-body').html(response);
                $('#scoreModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });

});



</script>

@endsection
