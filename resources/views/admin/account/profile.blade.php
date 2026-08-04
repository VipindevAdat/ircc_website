@extends('admin.layouts.app')

@section('page-title', 'Profile')

@section('header-action-button')
 
@endsection

@section('content')

    <div class="row flex-column-reverse flex-md-row">
        <div class="col-md-12">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="mb-4">
                        <div class="d-flex flex-column flex-md-row text-center text-md-start mb-3">
                            <figure class="me-4 flex-shrink-0">
                            <div class="avatar avatar-primary me-1">
                                            <span class="avatar-text rounded-circle">{{strtoupper(substr(auth()->user()->name, 0, 1))}}</span>
                                        </div>
                            </figure>
                            <div class="flex-fill">
                                <h5 class="mb-3">{{ auth()->user()->name }}</h5>
                                <!-- <button class="btn btn-primary me-2">Change Avatar</button>
                                <button class="btn btn-outline-danger btn-icon" data-bs-toggle="tooltip" title="Remove Avatar">
                                    <i class="bi bi-trash me-0"></i>
                                </button>
                                <p class="small text-muted mt-3">For best results, use an image at least
                                    256px by 256px in either .jpg or .png format</p> -->
                            </div>
                        </div>
                        <div class="card mb-4">
                            <form  method="POST" action="{{route('admin.account.update',auth()->user()->id)}}">
                                @csrf
                                {{  method_field('PUT')  }}
                                <div class="card-body">
                                    <h6 class="card-title mb-4">Basic Information</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}">
                                                @if ($errors->has('name'))
                                                  <div  class="invalid-feedback d-block">
                                                    {{ $errors->first('name') }}
                                                </div>
                                                @endif
                                           </div>
                                           <!-- <div class="mb-3">
                                                <label class="form-label">Notification Email</label>
                                                <input type="text" class="form-control" name="notification_email" value="{{ auth()->user()->notification_email }}">

                                                @if ($errors->has('notification_email'))
                                                  <div  class="invalid-feedback d-block">
                                                    {{ $errors->first('notification_email') }}
                                                </div>
                                                @endif
                                            </div> -->
                                        </div>
                                        <div class="col-md-6">
                                        <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="text" class="form-control" name="email" value="{{ auth()->user()->email }}">

                                                @if ($errors->has('email'))
                                                  <div  class="invalid-feedback d-block">
                                                    {{ $errors->first('email') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="mb-3">
                                            <button type="submit" class="btn btn-primary mb-3 pull-right">Update</button>
                                            </div>
                                        
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card  mb-4">
                        <form method="POST" action="{{route('admin.update-password')}}">
                            @csrf
                            <div class="card-body">
                                <h6 class="card-title mb-4">Change Password</h6>
                                <div class="mb-3">
                                    <label class="form-label">Old Password</label>
                                    <input type="password" name="current_password" class="form-control">
                                    @if ($errors->has('current_password'))
                                        <div  class="invalid-feedback d-block"> {{ $errors->first('current_password') }}</div>
                                    
                                    @endif
                                    @if(Session::has('error_message'))
                                    <div  class="invalid-feedback d-block">{{ Session::get('error_message') }}</div>
                                                        
                                        @endif
                                    
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="password">
                                    @if ($errors->has('password'))
                                        <div  class="invalid-feedback d-block">
                                        {{ $errors->first('password') }}
                                    </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password Confirmation</label>
                                    <input type="password" class="form-control" name="password_confirmation">
                                    @if ($errors->has('password_confirmation'))
                                        <div  class="invalid-feedback d-block">
                                        {{ $errors->first('password_confirmation') }}
                                    </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                <button type="submit" class="btn btn-primary mb-3 pull-right">Update</button>
                                </div>
                            
                            </div>
                        </form>
                    </div>


                    
                </div>
              
                
                
            </div>
        </div>
        <!-- <div class="col-md-4">
            <div class="card sticky-top mb-4 mb-md-0">
                <div class="card-body">
                    <ul class="nav nav-pills flex-column gap-2" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab"
                               aria-controls="profile" aria-selected="true">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab"
                               aria-controls="password" aria-selected="false">
                                <i class="bi bi-lock me-2"></i> Password
                            </a>
                        </li>
                   
                      
                    </ul>
                </div>
            </div>
        </div> -->
    </div>



@endsection
@section('script')
    <script>
    @if(Session::has('success_message'))
            $(document).ready( function () {
                Swal.fire({
                    icon: 'info',
                    title: '{{ Session::get('success_message') }}',
                    // text: 'we will notify through email once the adjustment is complete',
                    footer: ''
                })

    })
    @endif


    </script>
    @endsection