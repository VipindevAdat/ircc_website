@extends('admin.layouts.app')

@section('page-title', 'Admin Users')

@section('head')
    <link rel="stylesheet" href="{{ url('libs/dataTable/datatables.min.css') }}" type="text/css">
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
            background-color: #F15B43;
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
                <li class="breadcrumb-item active" aria-current="page">Admin Users</li>
            </ol>
        </nav>
    </div>

    <div class="content">
        <div class="">
            <div class="card">
                <div class="card-body">
                    <div class="d-md-flex gap-4 align-items-center">
                        <div class="d-none d-md-flex">All Admin Users</div>
                        <div class="dropdown ms-auto">
                            <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary" style="background-color: #F15B43; border-color: #F15B43;">
                                <i class="bi bi-plus"></i>
                                Add Admin User
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-lg mb-0" id="admin-users-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Admin User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deleteHref">
                    <p>Are you sure you want to delete this admin user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-danger btn_delete_admin_user" style="background-color: #F15B43; border-color: #F15B43;">Delete</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{ url('libs/dataTable/datatables.min.js') }}"></script>
    <script type="text/javascript">
        @if(Session::has('success_message'))
        $(document).ready(function () {
            Swal.fire({
                icon: 'success',
                title: '{{ Session::get('success_message') }}',
                footer: ''
            });
        });
        @endif

        $(document).ready(function () {
            var adminUsersTable = $('#admin-users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.admin-users.index') }}'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'status_label', name: 'status_label', orderable: false, searchable: false },
                    { data: 'created', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                columnDefs: [{
                    'defaultContent': '--',
                    'targets': '_all'
                }]
            });

            // Delete button handler
            var deleteHref = '';
            $(document).on('click', '.delete-admin-user', function () {
                deleteHref = $(this).data('href');
            });

            $('.btn_delete_admin_user').on('click', function () {
                if (!deleteHref) return;
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'DELETE',
                    url: deleteHref,
                    dataType: 'JSON',
                    success: function (response) {
                        $('#delete-modal').modal('hide');
                        adminUsersTable.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: response.success || 'Admin user deleted successfully.',
                            footer: ''
                        });
                        deleteHref = '';
                    },
                    error: function (xhr) {
                        $('#delete-modal').modal('hide');
                        var msg = xhr.responseJSON ? (xhr.responseJSON.error || 'An error occurred.') : 'An error occurred.';
                        Swal.fire({
                            icon: 'error',
                            title: msg,
                            footer: ''
                        });
                        deleteHref = '';
                    }
                });
            });
        });
    </script>
@endsection
