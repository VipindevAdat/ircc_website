@extends('admin.layouts.app')

@section('page-title', 'Registration')

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
                <li class="breadcrumb-item active" aria-current="page">Registration</li>
            </ol>
        </nav>
    </div>

    <div class="content">
        <div class="">
            <div class="card">
            <form method="POST" action="{{route('admin.exportRegistration')}}" >
                @csrf
                <input type="hidden" name="type" value="registration">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Export Year</label>
                        <select name="year" class="form-select">
                            <option value="">All Years</option>
                            @for ($y = (int) date('Y') + 1; $y >= 2024; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="d-md-flex gap-4 align-items-center">
                        <div class="d-none d-md-flex">All Registrations</div>
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
                    <table class="table table-custom table-lg mb-0" id="registrations">
                    <thead>
                      <tr>
                         <th>RegisterNum</th>  
                         <th>CreatedDate</th>  
                         <th>Category</th>  
                         <th>School Name</th>  
                        <th>Teacher Name</th>
                        <th>Members</th>
                        <th>Accompany Person</th>
                        <th>Email ID</th>
                        <th>Phone Number</th>
                        <th>Abstract Status</th>
                        <th></th>
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
    <div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Delete Registration</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="delete-log-form"  method="POST">
      <div class="modal-body">
              <!-- <input type="hidden" name="_method" value="DELETE"> -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="deleteId" name="deleteId">
                <div id="msg_div"></div>
                <p>Are you sure you want to delete this registration</p>
                <div class="modal-footer">
                   
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-danger btn_delete_registration" data-loading-text="">Confirm Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>   
</div> 
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Registration Details</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form method="POST" action="" id="confirm-form">
                @csrf
        <div class="modal-body"  id="detailPayment">
      
        
       
        </div>
        <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="confirmVerify" style="display: none;">Confirm</button>
        <button type="button" class="btn btn-secondary" id="verifyAccompany">Verify Accompany</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        
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
<script type="text/javascript">
    $(document).on('click', '#registerDetail', function() {
      var id=$(this).data('id');
      $.ajax({
         type: 'POST',
         data:{id:id},
         dataType : 'JSON', 
         url : '{{ route('admin.getRegistrationById') }}',
         success: function(response){
               $('#detailModal').modal('show');
               $('#detailBody').html(response.html);
           
         } 
       });
   
   });
   $(document).on('click', '#paymentDetail', function() {
      var id=$(this).data('id');
      $('#verifyAccompany').show();
      $('#confirmVerify').hide();
      $.ajax({
         type: 'POST',
         data:{id:id},
         dataType : 'JSON', 
         url : '{{ route('admin.getRegistrationById') }}',
         success: function(response){
               $('#paymentModal').modal('show');
               $('#detailPayment').html(response.htmlPayment);
           
         } 
       });
   
   });

   
$(document).ready(function() {
    var $column = $('#sort').find(':selected').data('column');
    var $sort = $('#sort').find(':selected').data('sort');
    $registrationTable = $('#registrations').DataTable({
        //order: [[$column==undefined?'4':$column, $sort==undefined?'desc':$sort]],
        processing: true,
        serverSide: true,
       dom:'lBfrtip',
        ajax: {
            url: '{{ route('admin.registration') }}',
            data: function(d) {
                
            }
        },

        columns: [
            {
                data: 'registration_number',
                name: 'registration_number'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
        
            {
                data: 'category',
                name: 'category'
            },
            {
                data: 'school_name',
                name: 'school_name'              
               
            },
            {
                data: 'teacher_name',
                name: 'teacher_name'
            },
         
            {
                data: 'members',
                name: 'members'
            },
            {
                data: 'accompany',
                name: 'accompany'
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
                data: 'abstract_status',
                name: 'abstract_status'
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
        responsive: false, "lengthChange": false, "autoWidth": false,
        buttons: ["excel"]
        
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
    $(".dataTables_filter").css({
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
    $(document).on('click', '#verifyAccompany', function() {
    var id=$('#registration_id').val();
    // $('#paymentModal').modal('hide');
    $('#verifyAccompany').hide();
    $('#confirmVerify').show();
    $('#amountDiv').show();
    $('#confirm-form').on('submit',  function (e) {
        e.preventDefault();
      $.ajax({
         type: 'POST',
         data: $(e.target).serialize(),
         dataType : 'JSON', 
         url : '{{ route('admin.verifyAccompany') }}',
         success: function(response){
            $('#paymentModal').modal('hide');
            $registrationTable.draw();
            Swal.fire({
                icon: 'success',
                title: 'Successfully Verified',
                text:'',
                footer: ''
            });
         
         } 
       });
    });
   });
    

});
$('table').off('click').on('click','.delete-registration', function(e){
    var href=$(this).data('href');
    console.log(href)
    $('.btn_delete_registration').click(function(){
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            type: 'DELETE',
            //data:{},
            dataType : 'JSON', 
            url : href,
            success: function(response){ 
                $('#delete-modal').modal('hide');
                $('#registrations').DataTable().ajax.reload();
                // toastr.success("plan deleted successfully", "Success"); 
                Swal.fire({
                    icon: 'success',
                    title: 'Registration deleted successfully',
                    // text: 'we will notify through email once the adjustment is complete',
                    footer: ''
                })
            } 
        }); 
    });
});
$(document).on("click","#delete-reg",function() {
            $('#deleteId').val($(this).data('id'));
            console.log('reg');
          $('#delete-modal').modal('show');

        });



</script>
    @endsection
