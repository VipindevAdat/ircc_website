<div class="menu">
    <div class="menu-header">
        <a href="{{ route('admin.dashboard') }}" class="menu-header-logo">
            <img src="{{ url('assets/images/ircc.png') }}" alt="logo">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm menu-close-btn">
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
                <a href="{{ route('admin.account.index') }}" class="dropdown-item d-flex align-items-center">
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
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

        <ul>
            <li>
                <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.dashboard')) class="active" @endif href="{{ route('admin.dashboard') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.registration.index')) class="active" @endif href="{{ route('admin.registration.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-person"></i>
                    </span>
                    <span>Registrations</span>
                </a>
            </li>
            <li>
                <a @if (request()->fullUrl()=== route('admin.registration.participants')) class="active" @endif href="{{ route('admin.registration.participants') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-people"></i>
                    </span>
                    <span>Participants</span>
                </a>
            </li>
            <li>
                <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.abstract.index')) class="active" @endif href="{{ route('admin.abstract.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Abstract</span>
                </a>
            </li>
            <li>
                <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.payment.index')) class="active" @endif href="{{ route('admin.payment.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-credit-card"></i>
                    </span>
                    <span>Payments</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                    <i class="fa fa-users"></i>
                    </span>
                    <span>Teams</span>
                </a>
                <ul>
                <li><a href="{{ route('admin.team-category.index') }}" @if(request()->fullUrl() == route('admin.team-category.index')) class="active" @endif>Category</a></li>
                <li><a href="{{ route('admin.team.index') }}" @if(request()->fullUrl() == route('admin.team.index')) class="active" @endif>Team</a></li>

                </ul>
            </li>
            <li>
                <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.reviewer.index')) class="active" @endif href="{{ route('admin.reviewer.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <span>Reviewers</span>
                </a>
            </li>
            <li>
                <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.domain.index')) class="active" @endif href="{{ route('admin.domain.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-window"></i>
                    </span>
                    <span>Abstract Domain</span>
                </a>
            </li>
            <li>
                <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.contact-us.index')) class="active" @endif href="{{ route('admin.contact-us.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-chat"></i>
                    </span>
                    <span>Contact us</span>
                </a>
            </li>
            <!-- <li>
                <a @if (!request()->segment(1) || request()->is('faq')) class="active" @endif href="{{ route('admin.faq.index') }}">
                    <span class="nav-link-icon">
                    <i class="bi bi-question-circle"></i>
                    </span>
                    <span>Faq</span>
                </a>
            </li> -->
            <li>
                <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.subscription.index')) class="active" @endif href="{{ route('admin.subscription.index') }}">
                    <span class="nav-link-icon">
                    <i class="bi bi-envelope-open"></i>
                    </span>
                    <span>Subscriptions</span>
                </a>
            </li>
            <li>
                <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.review.index')) class="active" @endif href="{{ route('admin.review.index') }}">
                    <span class="nav-link-icon">
                    <i class="bi bi-chat-left-dots"></i>
                    </span>
                    <span>Reviews</span>
                </a>
            </li>
            <li>
                <a @if(request()->fullUrl() == route('admin.gallery.index')) class="active" @endif href="{{ route('admin.gallery.index') }}">
                <span class="nav-link-icon">
                <i class="fa fa-picture-o" aria-hidden="true"></i>

                </span>
                <span>Gallery</span>
                </a>
            </li>
            <li>
                <a @if(request()->routeIs('admin.award-graphics.*')) class="active" @endif href="{{ route('admin.award-graphics.index') }}">
                    <span class="nav-link-icon"><i class="bi bi-trophy"></i></span>
                    <span>Awards page</span>
                </a>
            </li>
            <li>
                <a @if(request()->routeIs('admin.program-schedule*') || request()->routeIs('admin.program-schedule-days*') || request()->routeIs('admin.program-schedule-items*')) class="active" @endif href="{{ route('admin.program-schedule.index') }}">
                    <span class="nav-link-icon"><i class="bi bi-calendar-event"></i></span>
                    <span>Program Schedule</span>
                </a>
            </li>
            <li>
                <a @if(request()->fullUrl() == route('admin.abstract-book.index')) class="active" @endif href="{{ route('admin.abstract-book.index') }}">
                <span class="nav-link-icon">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>

                </span>
                <span>Abstract Book</span>
                </a>
            </li>

            <li> <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.mailTemplate.index')) class="active" @endif href="{{ route('admin.mailTemplate.index') }}">
                    <span class="nav-link-icon">
                    <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                    </span>
                    <span>Mail Template</span>
                </a></li>
                <li><a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.sendMail')) class="active" @endif href="{{ route('admin.sendMail') }}">
                    <span class="nav-link-icon">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                    </span>
                    <span>Send mail</span>
                </a></li>

            <li>
                <a @if (!request()->segment(1) || request()->fullUrl()=== route('admin.leads.index')) class="active" @endif href="{{ route('admin.leads.index') }}">
                    <span class="nav-link-icon">
                    <i class="fa fa-envelope" aria-hidden="true"></i>

                    </span>
                    <span>Leads</span>
                </a>
            </li>

            <li>
                <a @if (request()->is('admin/admin-users*')) class="active" @endif href="{{ route('admin.admin-users.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-people"></i>
                    </span>
                    <span>Admin Users</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="nav-link-icon">
                    <i class="fa fa-cog" aria-hidden="true"></i>
                    </span>
                    <span>Settings</span>
                </a>
                <ul>
                <li><a  @if (!request()->segment(1) || request()->fullUrl()=== route('admin.category.index')) class="active" @endif href="{{ route('admin.category.index') }}">Category</a></li>
                <li><a  @if (!request()->segment(1) || request()->fullUrl()=== route('admin.category-class.index')) class="active" @endif href="{{ route('admin.category-class.index') }}">Category Class</a></li>
                <li><a  @if (!request()->segment(1) || request()->fullUrl()=== route('admin.settings.index')) class="active" @endif href="{{ route('admin.settings.index') }}">General</a></li>
                 <li><a  @if (request()->routeIs('admin.landing-pages.*')) class="active" @endif href="{{ route('admin.landing-pages.index') }}">Landing Pages</a></li>
                <li><a  @if (request()->routeIs('admin.social-media.index')) class="active" @endif href="{{ route('admin.social-media.index') }}">Social media</a></li>

            </ul>
            </li>

        </ul>
    </div>
</div>