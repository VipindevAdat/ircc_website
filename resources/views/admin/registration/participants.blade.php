@extends('admin.layouts.app')

@section('page-title', 'Participants')

@section('content')
    <div class="mb-4">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-globe2 small me-2"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.registration.index') }}">Registration</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Participants</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <h5 class="mb-0">All Participants</h5>
                <div class="d-flex flex-wrap gap-2">
                    <form method="GET" action="{{ route('admin.registration.participants') }}" class="d-flex flex-wrap gap-2">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search participant or registration code"
                            value="{{ $search }}"
                        >
                        <select name="payment_status" class="form-select">
                            <option value="">All Payment Status</option>
                            <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                        <select name="hear_about_us" class="form-select">
                            <option value="">Display all</option>
                            @foreach($hearAboutOptions as $option)
                                <option value="{{ $option }}" {{ ($hearAboutFilter ?? '') === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Apply</button>
                        @if($search || $paymentStatus || ($hearAboutFilter ?? '') !== '')
                            <a href="{{ route('admin.registration.participants') }}" class="btn btn-outline-secondary">Clear</a>
                        @endif
                    </form>
                    <form method="POST" action="{{ route('admin.registration.participants.export') }}">
                        @csrf
                        <input type="hidden" name="search" value="{{ $search }}">
                        <input type="hidden" name="payment_status" value="{{ $paymentStatus }}">
                        <button type="submit" class="btn btn-info">
                            <i class="fa fa-upload"></i> Export XLS
                        </button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-lg mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Participant</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Age</th>
                            <th>Class</th>
                            <th>Registration Code</th>
                            <th>Registration Contact</th>
                            <th>Payment Status</th>
                            <th>How did you hear about us?</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($participants as $participant)
                            <tr>
                                <td>{{ $participants->firstItem() + $loop->index }}</td>
                                <td>{{ $participant->first_name }} {{ $participant->last_name }}</td>
                                <td>
                                    <span class="badge {{ $participant->role === 'adult' || $participant->type == 1 ? 'bg-info' : 'bg-primary' }}">
                                        {{ $participant->role === 'adult' || $participant->type == 1 ? 'Adult' : 'Student' }}
                                    </span>
                                </td>
                                <td>{{ $participant->email ?: '-' }}</td>
                                <td>{{ $participant->age ?: '-' }}</td>
                                <td>{{ $participant->class ?: '-' }}</td>
                                <td>{{ optional($participant->registration)->registration_number ?: '-' }}</td>
                                <td>
                                    {{ optional($participant->registration)->first_name }}
                                    {{ optional($participant->registration)->last_name }}
                                </td>
                                <td>
                                    <span class="badge {{ $participant->payment_status_label === 'Paid' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $participant->payment_status_label }}
                                    </span>
                                </td>
                                <td>
                                    {{ optional($participant->registration)->hear_about_us ?: '-' }}
                                </td>
                                <td>
                                    @if($participant->registration)
                                        <a href="{{ route('admin.registration.show', $participant->registration->id) }}" class="btn btn-sm btn-outline-primary">
                                            Open Registration
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">No participants found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-center">
                {{ $participants->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
