<header id="masthead" class="site-header site-header-transparent">
   <!-- header html start -->
   <div class="top-header">
      <div class="container">
         <div class="row">
            <div class="col-xl-8 d-none d-xl-block">
               <div class="header-contact-info">
                  <ul>
                     <li>
                        <a href="tel: {{optional($settings->where('code', 'phone')->first())->value}}"><i
                              class="fas fa-phone-alt"></i>
                           {{optional($settings->where('code', 'phone')->first())->value}}</a>
                     </li>
                     <li>
                        <a href="mailto: {{optional($settings->where('code', 'phone')->first())->value}}"><i
                              class="fas fa-envelope"></i> <span>
                              {{optional($settings->where('code', 'email')->first())->value}}</span></a>
                     </li>

                  </ul>
               </div>
            </div>
            <div class="col-xl-4 d-flex justify-content-xl-end justify-content-between">
               <div class="header-social social-links">
                  <ul>
                     @if(!empty($socialLinks['facebook']))
                        <li>
                           <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener noreferrer">
                              <i class="fab fa-facebook-f" aria-hidden="true"></i>
                           </a>
                        </li>
                     @endif
                     @if(!empty($socialLinks['whatsapp']))
                        <li>
                           <a href="{{ $socialLinks['whatsapp'] }}" target="_blank" rel="noopener noreferrer">
                              <i class="fab fa-whatsapp"></i>
                           </a>
                        </li>
                     @endif
                     @if(!empty($socialLinks['youtube']))
                        <li>
                           <a href="{{ $socialLinks['youtube'] }}" target="_blank" rel="noopener noreferrer">
                              <i class="fab fa-youtube" aria-hidden="true"></i>
                           </a>
                        </li>
                     @endif
                     @if(!empty($socialLinks['instagram']))
                        <li>
                           <a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener noreferrer">
                              <i class="fab fa-instagram" aria-hidden="true"></i>
                           </a>
                        </li>
                     @endif
                     @if(!empty($socialLinks['linkedin']))
                        <li>
                           <a href="{{ $socialLinks['linkedin'] }}" target="_blank" rel="noopener noreferrer">
                              <i class="fab fa-linkedin" aria-hidden="true"></i>
                           </a>
                        </li>
                     @endif
                     @if(!empty($socialLinks['twitter']))
                        <li>
                           <a href="{{ $socialLinks['twitter'] }}" target="_blank" rel="noopener noreferrer">
                              <i class="fab fa-twitter" aria-hidden="true"></i>
                           </a>
                        </li>
                     @endif
                  </ul>
               </div>

            </div>
         </div>
      </div>
   </div>
   <div class="bottom-header">
      <div class="container d-flex justify-content-between align-items-center">
         <div class="site-identity">
            <h1 class="site-title">
               <a href="{{route('/')}}">
                  <img class="white-logo" src="{{ url("front/images/unbound-logo.png")}}" alt="logo">
                  <img class="black-logo" src="{{ url("front/images/unbound-logo1.png")}}" alt="logo">
               </a>
            </h1>
         </div>
         <div class="main-navigation">
            <nav id="navigation" class="navigation d-none d-xl-inline-block">
               <ul>
                  <!-- About (Standalone) -->
                  <li class="nav-sidebar">
                     <a href="{{route('about')}}">About</a>
                  </li>
                  <!-- Guidelines (Standalone) -->
                  <li class="nav-sidebar">
                     <a href="{{route('guidelines')}}">Guidelines</a>
                  </li>
                  <!-- Conference Dropdown Group: Speakers, Program Schedule, Committee -->
                  <li class="menu-item-has-children">
                     <a href="#">Conference</a>
                     <ul>
                        <li>
                           <a href="{{route('speakers')}}">Speakers</a>
                        </li>
                        <li>
                           <a href="{{route('program-schedule')}}">Program Schedule</a>
                        </li>
                        <li>
                           <a href="{{route('committee')}}">Committee</a>
                        </li>
                        <li>
                           <a href="{{route('donation')}}">Donation</a>
                        </li>
                     </ul>
                  </li>
                  <!-- Media Dropdown Group: Awards, Gallery -->
                  <li class="menu-item-has-children">
                     <a href="#">Media</a>
                     <ul>
                        <li>
                           <a href="{{route('awards')}}">Awards</a>
                        </li>
                        <li>
                           <a href="{{route('gallery')}}">Gallery</a>
                        </li>
                     </ul>
                  </li>
                  <!-- Contact (Standalone) -->
                  <li class="nav-sidebar">
                     <a href="{{route('contact-us')}}">Contact</a>
                  </li>
                  @if(auth('customer')->check())
                     <li class="menu-item-has-children">
                        <a href="#" style="color:#F15B43;">My Account</a>
                        <ul>
                           <li>
                              <a href="{{route('accounts.dashboard')}}">Dashboard</a>
                           </li>
                           <li>
                              <a href="javascript:;"
                                 onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                           </li>
                        </ul>
                     </li>
                     <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                     </form>
                  @endif
               </ul>
            </nav>
            <div class="header-btn d-inline-block">
               @if(optional($settings->where('code', 'registration_status')->first())->value == 1)
                  @if(!auth('customer')->check())
                     <a href="{{route('login')}}" class="button-round" style="margin-right: 10px;">Login</a>
                     <a href="{{route('register')}}" class="button-round">Register</a>
                  @endif
               @elseif(optional($settings->where('code', 'abstract_status')->first())->value == 1)
                  @if(!auth('customer')->check())
                     <a href="{{route('login')}}" class="button-round">ABSTRACT</a>
                  @endif
               @elseif(optional($settings->where('code', 'login_status')->first())->value == 1)
                  @if(!auth('customer')->check())
                     <a href="{{route('login')}}" class="button-round">Login</a>
                  @endif
               @endif

            </div>
         </div>
      </div>
   </div>
   <div class="mobile-menu-container"></div>
</header>