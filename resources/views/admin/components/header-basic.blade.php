<div class="header">
    <div class="menu-toggle-btn"> <!-- Menu close button for mobile devices -->
        <a href="#">
            <i class="bi bi-list"></i>
        </a>
    </div>
    <!-- Logo -->
    <a href="{{ route('admin.dashboard') }}" class="logo">
        <img width="100" src="{{ url('assets/images/ircc.png') }}" alt="logo">
    </a>
    <!-- ./ Logo -->
    <div class="page-title">@yield('page-title')</div>
    <div class="search-form">
        <div class="input-group {{request()->is('admin/orders/create')||request()->is('admin/')||request()->is('admin/settings')||request()->is('admin/account')?'d-none':''}}" >
            <button class="btn btn-outline-light" type="button" id="button-addon1">
                <i class="bi bi-search"></i>
            </button>
            <input type="text" class="form-control searchInput" placeholder="Search..." aria-label="" aria-describedby="button-addon1">
            <a href="#" class="btn btn-outline-light close-header-search-bar">
                <i class="bi bi-x"></i>
            </a>

        </div> 
    </div>
   
    
    <div class="header-bar ms-auto">
        <ul class="navbar-nav justify-content-end">
            {{-- <li class="nav-item" style="background: #e3e3e3;border-radius: 7px;padding: 7px 20px;padding-top: 10px;">

            </li> --}}
            <!-- <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-link-notify" data-count="5" data-sidebar-target="#notifications" id="notificationBadge">
                    <i class="bi bi-bell icon-lg"></i>
                </a>
                
            </li> -->
          
            <li class="nav-item">
                <a href="#" class="d-flex align-items-center" data-bs-toggle="dropdown">
                    <div class="avatar me-3">
                        <div class="avatar avatar-primary me-1">
                            <span class="avatar-text rounded-circle">{{strtoupper(substr(auth()->user()->name, 0, 1))}}</span>
                        </div>
                    </div>
                    <div style="width: 90%;">
                        <div class="fw-bold">{{ auth()->user()->name }}</div>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                    {{-- <div class="">
                        <i class="bi bi-gear"></i> 
                    </div> --}}
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{route('admin.account.index')}}" class="dropdown-item d-flex align-items-center">
                        <i class="bi bi-person dropdown-item-icon"></i> Profile
                    </a>
                    <a href="javascript:;" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="dropdown-item d-flex align-items-center text-danger">
                        <i class="bi bi-box-arrow-right dropdown-item-icon"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
          
        </ul>
    </div>
    <!-- Header mobile buttons -->
    <div class="header-mobile-buttons">
        <a href="#" class="search-bar-btn">
            <i class="bi bi-search"></i>
        </a>
        <a href="#" class="actions-btn">
            <i class="bi bi-three-dots"></i>
        </a>
    </div>
    <!-- ./ Header mobile buttons -->
</div>

<div class="modal fade show" id="confirm-sync" tabindex="-1" aria-labelledby="confirm-sync-title"  aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirm-sync-title">Confirm Stock Refresh?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>This process will take time to refresh. Please wait.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close
                </button>
                <button type="button" class="btn btn-primary" id="confirm-sync-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')

<script>
    $(document).ready( function () {
        $('#sync-btn').on('click',function(){
            $('#confirm-sync').modal('show')
        });
        $('#confirm-sync-btn').on('click',function(){
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                type: 'GET',
                dataType : 'JSON', 
                // url : '',
                success: function(response){ 
                    $('#confirm-sync').modal('hide');
                    var html = '<div class=""><p style="margin-top: 0; margin-bottom: 0.5rem; line-height: 1.2;">Last Refresh at </p><p style="margin-top: 0;margin-bottom: 0.5rem;font-weight: 500;line-height: 1.2;color: #117dc0;">Refresh in Progress</p></div>';
                    $('#sync-container').html(html);
                    $('#adjust-selected').prop("disabled", true);
                    $('#adjust-by-sku').prop("disabled", true);
                    $('#sync-btn').prop("disabled", true);
                    $('#sync-btn').find('i').addClass('fa-spin');
                    // $('#sync-prograss').show('slow');
                    Swal.fire({
                        icon: 'success',
                        title: 'Inventory refresh started',
                        text: 'We will notify you through email once the refresh is complete',
                        footer: ''
                    });
                }
            });
        })
    });


    
</script>

@endpush