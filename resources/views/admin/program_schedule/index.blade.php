@extends('admin.layouts.app')

@section('page-title', 'Program Schedule')

@section('head')
    <link rel="stylesheet" href="{{ url('libs/dataTable/datatables.min.css') }}" type="text/css">
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
                <li class="breadcrumb-item active" aria-current="page">Program Schedule</li>
            </ol>
        </nav>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h6 class="card-title mb-4">Page settings</h6>
            <form method="POST" action="{{ route('admin.program-schedule.settings') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Conference title</label>
                        <input type="text" name="conference_title" class="form-control"
                            value="{{ old('conference_title', $pageSettings->conference_title) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Venue</label>
                        <input type="text" name="venue" class="form-control"
                            value="{{ old('venue', $pageSettings->venue) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Event dates</label>
                        <input type="text" name="event_dates" class="form-control"
                            value="{{ old('event_dates', $pageSettings->event_dates) }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Disclaimer</label>
                        <textarea name="disclaimer" class="form-control" rows="4">{{ old('disclaimer', $pageSettings->disclaimer) }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Save page settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-md-flex gap-4 align-items-center mb-3">
                <div class="d-none d-md-flex">Schedule days</div>
                <div class="dropdown ms-auto">
                    <a href="{{ route('admin.program-schedule-days.create') }}" class="btn btn-info">
                        <i class="bi bi-plus"></i> New day
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-lg mb-0" id="schedule-days">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Items</th>
                            <th>Sort order</th>
                            <th>Visibility</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete-day-modal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete schedule day</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this day and all of its schedule items?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-danger btn-delete-day">Delete</button>
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
    var $table = $('#schedule-days').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('admin.program-schedule.index') }}',
        columns: [
            { data: 'title', name: 'title' },
            { data: 'items_count', name: 'items_count', orderable: false, searchable: false },
            { data: 'sort_order', name: 'sort_order' },
            { data: 'is_visible', name: 'is_visible' },
            { data: 'is_virtual', name: 'is_virtual' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'asc']]
    });

    $('#schedule-days_filter, #schedule-days_length').hide();

    var deleteHref = '';
    $('table').on('click', '.delete-day', function () {
        deleteHref = $(this).data('href');
    });

    $('.btn-delete-day').on('click', function () {
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'DELETE',
            url: deleteHref,
            success: function (response) {
                $('#delete-day-modal').modal('hide');
                $table.ajax.reload();
                Swal.fire({ icon: 'success', title: response.success });
            },
            error: function (error) {
                $('#delete-day-modal').modal('hide');
                Swal.fire({ icon: 'error', title: error.responseJSON?.error || 'Unable to delete day.' });
            }
        });
    });
});
</script>
@endsection
