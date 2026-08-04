@extends('admin.layouts.app')

@section('page-title', 'Payments')

@section('head')
    <link rel="stylesheet" href="{{ url('libs/dataTable/datatables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ url("libs/datepicker/daterangepicker.css") }}" type="text/css">
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
            /* border: 0.5px solid #117dc0;  creates padding around scroll thumb */
        }
        #loading {
            background: url('../front/images/loading.gif') no-repeat center center;
            position: fixed;
            width: 100%;
            height: 100vh;
            z-index: 999;
        }
    </style>
@endsection


@section('content')
<div id="loading" style="display: none;"></div>
    <div class="mb-4">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-globe2 small me-2"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Payments</li>
            </ol>
        </nav>
    </div>

    <div class="content">
        <div class="">
            <div class="card">
                <form method="POST" action="{{ route('admin.exportPayments') }}">
                    @csrf
                    <input type="hidden" name="type" value="payment">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Payments</div>
                            <div class="d-md-flex justify_content-around gap-1 align-items-center">
                                <form class="mb-3 mb-md-0">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                        <label>Sort by</label> 
                                            <select class="form-select" id="sort" name="sort_by">
                                                <option value="">Sort by</option>
                                                <option value="name_asc">Name A-Z</option>
                                                <option value="name_desc">Name Z-A</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                        <label>Payment Mode</label> 
                                            <select class="form-select" id="payment_mode" name="payment_mode">
                                                <option value="">All Modes</option>
                                                <option value="Direct Bank Transfer">Direct Bank Transfer</option>
                                                <option value="Online Transaction">Online Transaction</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                        <label>Status Filter</label> 
                                            <select class="form-select" id="status_filter" name="status_filter">
                                                <option value="">All Statuses</option>
                                                <option value="completed">Completed Only</option>
                                                <option value="pending">Pending/Processing</option>
                                                <option value="failed">Failed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>From</label>
                                            <input type="text" name="date_from" class="form-control filter_date" id="filter-from" readonly placeholder="From" />
                                        </div>
                                        <div class="col-md-3">
                                        <label>To</label>
                                            <input type="text" name="date_to" class="form-control filter_date" id="filter-to" readonly placeholder="To" />
                                        </div>
                                    </div>
                                 
                                </div>
                                <div class="ms-auto d-flex gap-2">
                                    <button type="button" class="btn btn-primary" id="verifyPaymentsBtn" title="Verify all payments against payment gateway">
                                        <i class="fa fa-check-circle"></i> Verify Payments
                                    </button>
                                    @if(isset($oldPendingCount) && $oldPendingCount > 0)
                                    <button type="button" class="btn btn-danger" id="viewOldPendingBtn" title="View old pending payments ({{ $oldPendingCount }} payments older than 7 days)">
                                        <i class="fa fa-exclamation-triangle"></i> Old Pending ({{ $oldPendingCount }})
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-warning" id="syncPaymentsBtn" title="Sync pending/processing payments with payment gateway">
                                        <i class="fa fa-refresh"></i> Sync Payments
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="revertSyncBtn" title="Revert payments that were updated during a specific time window">
                                        <i class="fa fa-undo"></i> Revert Sync
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="clearCorruptedIdsBtn" title="Find and clear corrupted transaction IDs from a bad sync">
                                        <i class="fa fa-wrench"></i> Fix Corrupted IDs
                                    </button>
                                    <button type="submit" class="btn btn-info"><i class="fa fa-upload"></i> Export XLS</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-lg mb-0" id="payments">
                    <thead>
                        <tr>
                            <th>Payment Order Id</th>  
                            <th>Registration Number</th>  
                            <th>Payment Mode</th>  
                            <th>Payment Date</th>
                            <th>Payment Amount</th>  
                            <th>Payment Status</th>
                            <th>Days Old</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <pre id="paymentDetails"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Confirm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="delete-log-form"  method="POST">
                    <div class="modal-body">
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <p id="confirm-message"></p>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-sm btn-danger btn_confirm_payment" data-loading-text="">Delete</button>
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
        <script src="{{ url("libs/datepicker/daterangepicker.js") }}"></script>
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
    $paymentTable = $('#payments').DataTable({
        //order: [[$column==undefined?'4':$column, $sort==undefined?'desc':$sort]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.payment.index") }}',
            data: function(d) {
                d.payment_mode = $('#payment_mode').val();
                d.date_from = $('#filter-from').val();
                d.date_to = $('#filter-to').val();
                d.status_filter = $('#status_filter').val();
            }
        },

        columns: [  
            { 
                data: 'payment_order_id', 
                name: 'payment_order_id' 
            },
            { 
                data: 'registration_number', 
                name: 'registration_number',
            },
            { 
                data: 'payment_mode', 
                name: 'payment_mode' 
            },
            { 
                data: 'date', 
                name: 'date', 
            },
            { 
                data: 'payment_amount', 
                name: 'payment_amount', 
            },
            { 
                data: 'payment_status', 
                name: 'payment_status', 
            },
            { 
                data: 'days_old', 
                name: 'days_old',
                orderable: false,
                searchable: false
            },
            { 
                data: 'action', 
                name: 'action', 
            },
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
    });

    $('.filter_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: false,
        locale: {
            format: 'DD-MM-YYYY'
        }
    });

    $('.filter_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
        $paymentTable.ajax.reload();
    });

    $('.filter_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $paymentTable.ajax.reload(); 
    });

    $('#payment_mode, #status_filter').on('change', function() {
        $paymentTable.draw();
    });

    $(document).on("keyup", ".searchInput", function(e) {
        $paymentTable.search($(this).val()).draw();
    });
    $('#pageLength').on('change', function() {
        $paymentTable.page.len($(this).val()).draw();
    });
    $('#pageLength').val($paymentTable.page.len());

    $('#payments_filter').css({
        "display": "none"
    })
    $('#payments_length').css({
        "display": "none"
    })
    $('.filter').change(function() {
        $paymentTable.draw();
    });

    $('#sort').on('change', function() {
        $paymentTable.draw();
    });

    $(document).on('click', '.view-details', function() {
        var id = $(this).data('id');
        $.ajax({
            type: 'GET',
            data: { id: id },
            dataType: 'html',
            url: '{{ route('admin.paymentDetail') }}',
            success: function (response) {
                $('#paymentDetailsModal .modal-body').html(response);
                $('#paymentDetailsModal').modal('show');
            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    });
    $(document).on('click', '.approve_payment', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var comments=$('#comments').val();
         $('#paymentDetailsModal').modal('hide');
        $('#loading').show();
        $.ajax({
            type: 'POST',
            data: { id: id,status:status,comments:comments},
            dataType: 'html',
            url: '{{ route('admin.approvePayment') }}',
            success: function (response) {
                response=JSON.parse(response);
                $('#loading').hide();
                $('#paymentDetailsModal').modal('hide');
                $paymentTable.draw();
                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    footer: ''
                })
            },
            error: function (xhr, status, error) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'error',
                    title:error,
                    footer: ''
                })
            }
        });
    });
   
    // Sync payments with payment gateway - always does DRY RUN first
    $('#syncPaymentsBtn').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        
        Swal.fire({
            title: 'Sync Payments?',
            text: 'This will first preview changes (dry run) before syncing pending/processing online transactions with the payment gateway.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Preview Changes (Dry Run)',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Checking...');
                $('#loading').show();
                
                // First, do a DRY RUN to show what would change
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.paymentRetrive") }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sync_all: false,
                        dry_run: true
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#loading').hide();
                        btn.prop('disabled', false).html(originalHtml);
                        
                        if (response.success) {
                            let html = '<div style="text-align: left; max-height: 400px; overflow-y: auto;">';
                            html += '<p><strong>' + response.message + '</strong></p>';
                            
                            if (response.changes && response.changes.length > 0) {
                                html += '<table class="table table-sm table-bordered" style="font-size: 12px;">';
                                html += '<thead><tr><th>Order ID</th><th>Current Status</th><th>New Status</th><th>Current Amount</th><th>New Amount</th><th>Match Via</th></tr></thead><tbody>';
                                response.changes.forEach(function(c) {
                                    var matchLabel = c.match_path === 'transaction_id' ? '<span class="badge bg-success">Transaction ID</span>' 
                                                   : c.match_path === 'metadata_search' ? '<span class="badge bg-info">Metadata</span>'
                                                   : c.match_path === 'email_amount_search' ? '<span class="badge bg-warning">Email+Amount</span>'
                                                   : '<span class="badge bg-secondary">' + (c.match_path || 'Unknown') + '</span>';
                                    html += '<tr><td>' + c.order_id + '</td><td>' + (c.old_status || '-') + '</td><td>' + c.new_status + '</td><td>$' + (parseFloat(c.old_amount) || 0).toFixed(2) + '</td><td>$' + (parseFloat(c.new_amount) || 0).toFixed(2) + '</td><td>' + matchLabel + '</td></tr>';
                                });
                                html += '</tbody></table>';
                            } else {
                                html += '<p>No changes would be made.</p>';
                            }
                            
                            if (response.errors && response.errors.length > 0) {
                                html += '<hr><p><strong>Issues (' + response.errors.length + '):</strong></p><ul>';
                                response.errors.slice(0, 15).forEach(function(e) {
                                    html += '<li style="font-size: 11px;">' + e + '</li>';
                                });
                                if (response.errors.length > 15) {
                                    html += '<li style="font-size: 11px;">... and ' + (response.errors.length - 15) + ' more</li>';
                                }
                                html += '</ul>';
                            }
                            html += '</div>';
                            
                            Swal.fire({
                                title: 'Sync Preview (Dry Run)',
                                html: html,
                                width: '80%',
                                showCancelButton: response.changes && response.changes.length > 0,
                                confirmButtonText: response.changes && response.changes.length > 0 ? 'Apply Changes Now' : 'OK',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: response.changes && response.changes.length > 0 ? '#d33' : '#3085d6',
                            }).then((applyResult) => {
                                if (applyResult.isConfirmed && response.changes && response.changes.length > 0) {
                                    // Now do the actual sync
                                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Syncing...');
                                    $('#loading').show();
                                    
                                    $.ajax({
                                        type: 'POST',
                                        url: '{{ route("admin.paymentRetrive") }}',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            sync_all: false,
                                            dry_run: false
                                        },
                                        dataType: 'json',
                                        success: function(syncResponse) {
                                            $('#loading').hide();
                                            btn.prop('disabled', false).html(originalHtml);
                                            
                                            let msg = syncResponse.message;
                                            if (syncResponse.batch_id) {
                                                msg += '<br><br><small>Batch ID for rollback: <code>' + syncResponse.batch_id + '</code></small>';
                                            }
                                            
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Sync Completed',
                                                html: msg,
                                                footer: ''
                                            });
                                            $paymentTable.draw();
                                        },
                                        error: function(xhr) {
                                            $('#loading').hide();
                                            btn.prop('disabled', false).html(originalHtml);
                                            Swal.fire({ icon: 'error', title: 'Sync Failed', text: xhr.responseJSON?.message || 'Error syncing' });
                                        }
                                    });
                                }
                            });
                        } else {
                            Swal.fire({ icon: 'info', title: 'No Changes', text: response.message || 'Nothing to sync' });
                        }
                    },
                    error: function(xhr) {
                        $('#loading').hide();
                        btn.prop('disabled', false).html(originalHtml);
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Error checking payments' });
                    }
                });
            }
        });
    });
    
    // Revert payment sync by timestamp
    $('#revertSyncBtn').on('click', function() {
        Swal.fire({
            title: 'Revert Payment Sync',
            html: '<div style="text-align: left;">' +
                  '<p>Enter the time window of the sync you want to revert:</p>' +
                  '<div class="mb-2"><label>From (UTC):</label><input type="text" id="revert-from" class="form-control" placeholder="2026-03-18 03:56:50" value="2026-03-18 03:56:50"></div>' +
                  '<div class="mb-2"><label>To (UTC):</label><input type="text" id="revert-to" class="form-control" placeholder="2026-03-18 03:58:00" value="2026-03-18 03:58:00"></div>' +
                  '<p class="text-muted"><small>This will first preview affected payments (dry run), then you can choose to apply.</small></p>' +
                  '</div>',
            showCancelButton: true,
            confirmButtonText: 'Preview (Dry Run)',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                return {
                    from: document.getElementById('revert-from').value,
                    to: document.getElementById('revert-to').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const fromVal = result.value.from;
                const toVal = result.value.to;
                
                if (!fromVal || !toVal) {
                    Swal.fire('Error', 'Both timestamps are required', 'error');
                    return;
                }
                
                $('#loading').show();
                
                // First dry run
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.revertPaymentsByTimestamp") }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        from: fromVal,
                        to: toVal,
                        dry_run: true
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#loading').hide();
                        
                        if (response.success) {
                            let html = '<div style="text-align: left; max-height: 400px; overflow-y: auto;">';
                            html += '<p><strong>' + response.message + '</strong></p>';
                            html += '<p class="text-danger"><strong>This will reset paid_amount to $0 and status to Processing. You can then manually verify or re-sync individual payments.</strong></p>';
                            
                            if (response.payments && response.payments.length > 0) {
                                html += '<table class="table table-sm table-bordered" style="font-size: 12px;">';
                                html += '<thead><tr><th>Order ID</th><th>Reg #</th><th>Current Status</th><th>Corrupted Amount</th><th>Expected Amount</th><th>Action</th></tr></thead><tbody>';
                                response.payments.forEach(function(p) {
                                    var amountMismatch = parseFloat(p.current_paid_amount) !== parseFloat(p.expected_amount);
                                    var rowClass = amountMismatch ? 'style="background-color: #fff3cd;"' : '';
                                    html += '<tr ' + rowClass + '><td>' + p.order_id + '</td><td>' + (p.registration_number || '-') + '</td><td>' + (p.current_status_name || p.current_status) + '</td><td class="text-danger">$' + (parseFloat(p.current_paid_amount) || 0).toFixed(2) + '</td><td>$' + (parseFloat(p.expected_amount) || 0).toFixed(2) + '</td><td>' + p.action + '</td></tr>';
                                });
                                html += '</tbody></table>';
                            }
                            html += '</div>';
                            
                            Swal.fire({
                                title: 'Revert Preview',
                                html: html,
                                width: '80%',
                                showCancelButton: true,
                                confirmButtonText: 'Apply Revert Now',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#d33',
                            }).then((applyResult) => {
                                if (applyResult.isConfirmed) {
                                    $('#loading').show();
                                    
                                    $.ajax({
                                        type: 'POST',
                                        url: '{{ route("admin.revertPaymentsByTimestamp") }}',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            from: fromVal,
                                            to: toVal,
                                            dry_run: false
                                        },
                                        dataType: 'json',
                                        success: function(revertResponse) {
                                            $('#loading').hide();
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Revert Completed',
                                                text: revertResponse.message,
                                            });
                                            $paymentTable.draw();
                                        },
                                        error: function(xhr) {
                                            $('#loading').hide();
                                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Error reverting' });
                                        }
                                    });
                                }
                            });
                        } else {
                            Swal.fire({ icon: 'info', title: 'No Payments Found', text: response.message });
                        }
                    },
                    error: function(xhr) {
                        $('#loading').hide();
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Error checking payments' });
                    }
                });
            }
        });
    });
    
    // View old pending payments
    $('#viewOldPendingBtn').on('click', function() {
        $('#loading').show();
        
        $.ajax({
            type: 'GET',
            url: '{{ route("admin.getOldPendingPayments") }}',
            data: {
                days: 7
            },
            dataType: 'json',
            success: function(response) {
                $('#loading').hide();
                
                if (response.success && response.payments.length > 0) {
                    let tableHtml = '<table class="table table-bordered"><thead><tr>' +
                        '<th>Order ID</th><th>Registration</th><th>Mode</th><th>Amount</th>' +
                        '<th>Status</th><th>Days Old</th><th>Created Date</th></tr></thead><tbody>';
                    
                    response.payments.forEach(function(payment) {
                        tableHtml += '<tr>' +
                            '<td>' + payment.payment_order_id + '</td>' +
                            '<td>' + payment.registration_number + '</td>' +
                            '<td>' + payment.payment_mode + '</td>' +
                            '<td>$' + parseFloat(payment.amount).toFixed(2) + '</td>' +
                            '<td>' + payment.status_name + '</td>' +
                            '<td><span class="badge bg-danger">' + payment.days_old + ' days</span></td>' +
                            '<td>' + payment.created_at + '</td>' +
                            '</tr>';
                    });
                    
                    tableHtml += '</tbody></table>';
                    
                    Swal.fire({
                        title: 'Old Pending Payments',
                        html: '<div style="max-height: 500px; overflow-y: auto;">' + 
                              '<p><strong>Found ' + response.count + ' payments older than ' + response.days_threshold + ' days</strong></p>' +
                              tableHtml + '</div>',
                        width: '90%',
                        confirmButtonText: 'Close',
                        footer: '<button class="btn btn-warning" onclick="syncOldPayments()">Sync All Old Payments</button>'
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Old Pending Payments',
                        text: 'No pending payments found older than 7 days',
                        footer: ''
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load old pending payments',
                    footer: ''
                });
            }
        });
    });
    
    // Function to sync all old payments - now with dry run preview
    window.syncOldPayments = function() {
        Swal.fire({
            title: 'Sync All Old Payments?',
            text: 'This will first preview what changes would be made, then you can choose to apply.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Preview Changes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading').show();
                $('#syncPaymentsBtn').prop('disabled', true);
                
                // Dry run first
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.paymentRetrive") }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sync_all: true,
                        dry_run: true
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#loading').hide();
                        $('#syncPaymentsBtn').prop('disabled', false);
                        
                        if (response.success) {
                            let html = '<div style="text-align: left; max-height: 400px; overflow-y: auto;">';
                            html += '<p><strong>' + response.message + '</strong></p>';
                            if (response.changes && response.changes.length > 0) {
                                html += '<table class="table table-sm table-bordered" style="font-size: 12px;">';
                                html += '<thead><tr><th>Order ID</th><th>Old Status</th><th>New Status</th><th>Old Amount</th><th>New Amount</th></tr></thead><tbody>';
                                response.changes.forEach(function(c) {
                                    html += '<tr><td>' + c.order_id + '</td><td>' + (c.old_status || '-') + '</td><td>' + c.new_status + '</td><td>$' + (parseFloat(c.old_amount) || 0).toFixed(2) + '</td><td>$' + (parseFloat(c.new_amount) || 0).toFixed(2) + '</td></tr>';
                                });
                                html += '</tbody></table>';
                            } else {
                                html += '<p>No changes would be made.</p>';
                            }
                            html += '</div>';
                            
                            Swal.fire({
                                title: 'Sync Preview',
                                html: html,
                                width: '80%',
                                showCancelButton: response.changes && response.changes.length > 0,
                                confirmButtonText: response.changes && response.changes.length > 0 ? 'Apply Changes' : 'OK',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#d33',
                            }).then((applyResult) => {
                                if (applyResult.isConfirmed && response.changes && response.changes.length > 0) {
                                    $('#loading').show();
                                    $.ajax({
                                        type: 'POST',
                                        url: '{{ route("admin.paymentRetrive") }}',
                                        data: { _token: '{{ csrf_token() }}', sync_all: true, dry_run: false },
                                        dataType: 'json',
                                        success: function(syncResp) {
                                            $('#loading').hide();
                                            Swal.fire({ icon: 'success', title: 'Sync Completed', html: syncResp.message });
                                            $paymentTable.draw();
                                        },
                                        error: function(xhr) {
                                            $('#loading').hide();
                                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Sync failed' });
                                        }
                                    });
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#loading').hide();
                        $('#syncPaymentsBtn').prop('disabled', false);
                    }
                });
            }
        });
    };
    
    // Verify payments against gateway
    $('#verifyPaymentsBtn').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        
        Swal.fire({
            title: 'Verify Payments?',
            text: 'This will fetch all transactions from the payment gateway and compare with your database. This may take a moment.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, verify now',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
                $('#loading').show();
                
                $.ajax({
                    type: 'GET',
                    url: '{{ route("admin.verifyPaymentsAgainstGateway") }}',
                    dataType: 'json',
                    success: function(response) {
                        $('#loading').hide();
                        btn.prop('disabled', false).html(originalHtml);
                        
                        if (response.success) {
                            const summary = response.summary;
                            let html = '<div style="text-align: left; max-height: 500px; overflow-y: auto;">';
                            html += '<h5>Verification Summary</h5>';
                            html += '<ul>';
                            html += '<li><strong>Transactions in Gateway:</strong> ' + summary.gateway_transactions + '</li>';
                            html += '<li><strong>Payments in Database:</strong> ' + summary.database_payments + '</li>';
                            html += '<li><strong>Valid Payments:</strong> <span style="color: green;">' + summary.valid_payments + '</span></li>';
                            html += '<li><strong>Invalid Payments (not in gateway):</strong> <span style="color: red;">' + summary.invalid_payments + '</span></li>';
                            html += '<li><strong>Missing in Database:</strong> <span style="color: orange;">' + summary.missing_in_database + '</span></li>';
                            html += '</ul>';
                            
                            if (response.invalid_payments && response.invalid_payments.length > 0) {
                                html += '<hr><h6>Invalid Payments (Not Found in Gateway):</h6>';
                                html += '<table class="table table-sm table-bordered" style="font-size: 12px;">';
                                html += '<thead><tr><th>Order ID</th><th>Reg #</th><th>Amount</th><th>Status</th><th>Days Old</th><th>Action</th></tr></thead><tbody>';
                                
                                response.invalid_payments.slice(0, 20).forEach(function(payment) {
                                    html += '<tr>';
                                    html += '<td>' + payment.order_id + '</td>';
                                    html += '<td>' + payment.registration_number + '</td>';
                                    html += '<td>$' + parseFloat(payment.amount).toFixed(2) + '</td>';
                                    html += '<td>' + payment.status_name + '</td>';
                                    html += '<td>' + payment.days_old + ' days</td>';
                                    html += '<td><input type="checkbox" class="invalid-payment-check" value="' + payment.order_id + '" checked></td>';
                                    html += '</tr>';
                                });
                                
                                if (response.invalid_payments.length > 20) {
                                    html += '<tr><td colspan="6">... and ' + (response.invalid_payments.length - 20) + ' more</td></tr>';
                                }
                                
                                html += '</tbody></table>';
                                html += '<p><small>Check the payments you want to mark as failed, then click "Mark Selected as Failed"</small></p>';
                            }
                            
                            html += '</div>';
                            
                            Swal.fire({
                                title: 'Payment Verification Results',
                                html: html,
                                width: '90%',
                                confirmButtonText: response.invalid_payments && response.invalid_payments.length > 0 
                                    ? 'Mark Selected as Failed' 
                                    : 'Close',
                                showCancelButton: response.invalid_payments && response.invalid_payments.length > 0,
                                cancelButtonText: 'Cancel',
                                footer: ''
                            }).then((result) => {
                                if (result.isConfirmed && response.invalid_payments && response.invalid_payments.length > 0) {
                                    const selectedOrderIds = [];
                                    $('.invalid-payment-check:checked').each(function() {
                                        selectedOrderIds.push($(this).val());
                                    });
                                    
                                    if (selectedOrderIds.length > 0) {
                                        $('#loading').show();
                                        $.ajax({
                                            type: 'POST',
                                            url: '{{ route("admin.markInvalidPaymentsAsFailed") }}',
                                            data: {
                                                _token: '{{ csrf_token() }}',
                                                order_ids: selectedOrderIds
                                            },
                                            dataType: 'json',
                                            success: function(markResponse) {
                                                $('#loading').hide();
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Payments Updated',
                                                    text: markResponse.message,
                                                    footer: ''
                                                });
                                                $paymentTable.draw();
                                            },
                                            error: function() {
                                                $('#loading').hide();
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error',
                                                    text: 'Failed to mark payments as failed',
                                                    footer: ''
                                                });
                                            }
                                        });
                                    }
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Verification Failed',
                                text: response.message || 'An error occurred during verification',
                                footer: ''
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#loading').hide();
                        btn.prop('disabled', false).html(originalHtml);
                        
                        let errorMessage = 'An error occurred during verification';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Verification Failed',
                            text: errorMessage,
                            footer: ''
                        });
                    }
                });
            }
        });
    });
    
    // Clear corrupted transaction IDs
    $('#clearCorruptedIdsBtn').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        
        Swal.fire({
            title: 'Fix Corrupted Transaction IDs?',
            html: '<p>This will check all online payments with stored transaction IDs against the payment gateway.</p>' +
                  '<p>If a stored transaction ID returns a mismatched transaction (wrong amount or wrong order), it will be cleared.</p>' +
                  '<p><strong>This is a dry run first</strong> - you can review before applying.</p>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Preview (Dry Run)',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Checking...');
                $('#loading').show();
                
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.clearCorruptedTransactionIds") }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        dry_run: true
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#loading').hide();
                        btn.prop('disabled', false).html(originalHtml);
                        
                        if (response.success) {
                            let html = '<div style="text-align: left; max-height: 400px; overflow-y: auto;">';
                            html += '<p><strong>' + response.message + '</strong></p>';
                            
                            if (response.details && response.details.length > 0) {
                                html += '<table class="table table-sm table-bordered" style="font-size: 12px;">';
                                html += '<thead><tr><th>Order ID</th><th>Stored Transaction ID</th><th>Reason</th><th>Action</th></tr></thead><tbody>';
                                response.details.forEach(function(d) {
                                    html += '<tr class="table-warning"><td>' + d.order_id + '</td><td><code>' + d.transaction_id + '</code></td><td>' + d.reason + '</td><td>' + d.action + '</td></tr>';
                                });
                                html += '</tbody></table>';
                            } else {
                                html += '<p class="text-success">No corrupted transaction IDs found. All stored IDs are valid.</p>';
                            }
                            html += '</div>';
                            
                            Swal.fire({
                                title: 'Corrupted IDs Check',
                                html: html,
                                width: '80%',
                                showCancelButton: response.corrupted > 0,
                                confirmButtonText: response.corrupted > 0 ? 'Clear Corrupted IDs Now' : 'OK',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: response.corrupted > 0 ? '#d33' : '#3085d6',
                            }).then((applyResult) => {
                                if (applyResult.isConfirmed && response.corrupted > 0) {
                                    $('#loading').show();
                                    
                                    $.ajax({
                                        type: 'POST',
                                        url: '{{ route("admin.clearCorruptedTransactionIds") }}',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            dry_run: false
                                        },
                                        dataType: 'json',
                                        success: function(clearResponse) {
                                            $('#loading').hide();
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Corrupted IDs Cleared',
                                                text: clearResponse.message,
                                            });
                                            $paymentTable.draw();
                                        },
                                        error: function(xhr) {
                                            $('#loading').hide();
                                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Error clearing IDs' });
                                        }
                                    });
                                }
                            });
                        } else {
                            Swal.fire({ icon: 'info', title: 'Result', text: response.message });
                        }
                    },
                    error: function(xhr) {
                        $('#loading').hide();
                        btn.prop('disabled', false).html(originalHtml);
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Error checking IDs' });
                    }
                });
            }
        });
    });
    
    // Handle resend email button
    $(document).on('click', '.resend-email', function(e) {
        e.preventDefault();
        const paymentId = $(this).data('payment-id');
        const btn = $(this);
        const originalHtml = btn.html();
        
        Swal.fire({
            title: 'Resend Confirmation Email?',
            text: 'This will resend the payment confirmation email to the user.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, resend',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.resendPaymentEmail") }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        payment_id: paymentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        btn.prop('disabled', false).html(originalHtml);
                        
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Email Sent',
                                text: response.message,
                                footer: ''
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to resend email',
                                footer: ''
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(originalHtml);
                        
                        let errorMessage = 'Failed to resend email';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            footer: ''
                        });
                    }
                });
            }
        });
    });

});

</script>
    @endsection
