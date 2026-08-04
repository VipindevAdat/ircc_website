<div class="menu">
    <div class="menu-header">
        <a href="{{ route('reviewer.dashboard') }}" class="menu-header-logo">
            <img src="{{ url('assets/images/ircc.png') }}" alt="logo">
        </a>
        <a href="{{ route('reviewer.dashboard') }}" class="btn btn-sm menu-close-btn">
            <i class="bi bi-x"></i>
        </a>
    </div>
    <div class="menu-body">

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center" data-bs-toggle="dropdown">
                <div class="avatar me-3">
                    <div class="avatar avatar-primary me-1">
                        <span
                            class="avatar-text rounded-circle">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                </div>
                <div style="width: 90%;">
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                </div>
                <div class="">
                    <i class="bi bi-gear"></i>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a href="{{ route('reviewer.account.index') }}" class="dropdown-item d-flex align-items-center">
                    <i class="bi bi-person dropdown-item-icon"></i> Profile
                </a>
                <!-- <a href="#" class="dropdown-item d-flex align-items-center">
                    <i class="bi bi-envelope dropdown-item-icon"></i> Inbox
                </a> -->
                <!-- <a href="#" class="dropdown-item d-flex align-items-center" data-sidebar-target="#settings">
                    <i class="bi bi-gear dropdown-item-icon"></i> Settings
                </a> -->
                <a href="javascript:;" onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                    class="dropdown-item d-flex align-items-center text-danger">
                    <i class="bi bi-box-arrow-right dropdown-item-icon"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('reviewer.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

        <ul>
            <li>
                <a @if (!request()->segment(1) || request()->is('dashboard')) class="active" @endif href="{{ route('reviewer.dashboard') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>

 
            <li>
                <a @if (!request()->segment(1) || request()->is('abstracts')) class="active" @endif href="{{ route('reviewer.abstracts') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Abstract</span>
                </a>
            </li>
        </ul>
    </div>
</div>
