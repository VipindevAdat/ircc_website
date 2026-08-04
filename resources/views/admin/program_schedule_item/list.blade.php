@extends('admin.layouts.app')

@section('page-title', 'Program Schedule Items')

@section('head')
    <link rel="stylesheet" href="{{ url('libs/dataTable/datatables.min.css') }}" type="text/css">
@endsection

@section('content')
    <div class="mb-4">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.program-schedule.index') }}">Program Schedule</a></li>
                <li class="breadcrumb-item active">{{ $day->title }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-md-flex gap-4 align-items-center mb-3">
                <div>
                    <h6 class="mb-0">Items for: {{ $day->title }}</h6>
                    <small class="text-muted">{{ $day->items()->count() }} item(s) total — use pagination below if not all rows are visible</small>
                </div>
                <div class="dropdown ms-auto d-flex gap-2">
                    <a href="{{ route('admin.program-schedule.index') }}" class="btn btn-secondary">Back to days</a>
                    <a href="{{ route('admin.program-schedule-items.create', $day->id) }}" class="btn btn-info">
                        <i class="bi bi-plus"></i> New item
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-lg mb-0" id="schedule-items">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Activity</th>
                            <th>Venue</th>
                            <th>Sort order</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete-item-modal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete schedule item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this schedule item?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-danger btn-delete-item">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ url('libs/dataTable/datatables.min.js') }}"></script>
<script>
@if(Session::has('success_message'))
$(document).ready(function () {
    Swal.fire({ icon: 'success', title: '{{ Session::get('success_message') }}' });
});
@endif

$(document).ready(function () {
    var $table = $('#schedule-items').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 50,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'All']],
        ajax: '{{ route('admin.program-schedule-items.index', $day->id) }}',
        columns: [
            { data: 'time_slot', name: 'time_slot' },
            { data: 'title', name: 'title' },
            { data: 'venue', name: 'venue' },
            { data: 'sort_order', name: 'sort_order' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[3, 'asc']]
    });

    $('#schedule-items_filter').hide();

    var deleteHref = '';
    $('table').on('click', '.delete-item', function () {
        deleteHref = $(this).data('href');
    });

    $('.btn-delete-item').on('click', function () {
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'DELETE',
            url: deleteHref,
            success: function (response) {
                $('#delete-item-modal').modal('hide');
                $table.ajax.reload();
                Swal.fire({ icon: 'success', title: response.success });
            }
        });
    });
});
</script>
@endsection
