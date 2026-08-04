<footer id="colophon" class="site-footer footer-primary">
<div class="top-footer">
<div class="container">
    <div class="upper-footer">
        <div class="row">
        <div class="col-lg-3 col-md-6">
            <aside class="widget widget_text">
                <div class="footer-logo">
                    <a href="{{route('/')}}"><img src="{{ url("front/images/unbound-logo.png")}}" alt=""></a>
                </div>
                <div class="textwidget widget-text">
                A scientific arena for young researchers
                </div>
            </aside>
        </div>
        <div class="col-lg-3 col-md-6">
            <aside class="widget widget_text">
                <h3 class="widget-title">Contact Information</h3>
                <div class="textwidget widget-text">
                    <ul>
                    <li>
                        <i aria-hidden="true" class="icon icon-map-marker1"></i>
                        {!!optional($settings->where('code','address')->first())->value!!}
                    </li>
                    <li>
                        <a href="tel:+1 6172014330">
                            <i aria-hidden="true" class="icon icon-phone1"></i>
                            {{optional($settings->where('code','phone')->first())->value}}

                        </a>
                    </li>
                    <li>
                        <a href="mailto: {{optional($settings->where('code','email')->first())->value}} ">
                            <i aria-hidden="true" class="icon icon-envelope1"></i>
                            <span class="__cf_email__" data-cfemail="4a23242c250a2e25272b232464292527">  {{optional($settings->where('code','email')->first())->value}} </span>
                        </a>
                    </li>
                    </ul>
                </div>
            </aside>
        </div>
       <div class="col-lg-3 col-md-6">
            <aside class="widget widget_map_hotspots">
                <!-- <h3 class="widget-title">Quick Link</h3>
                <ul>
                @if(optional($settings->where('code','registration_status')->first())->value==1)
                    <li>
                    <a href="{{route('login')}}"><span class="semi-bold">Registration</span></a>
                    </li>
                    @endif
                    @if(optional($settings->where('code','abstract_status')->first())->value==1)
                    <li>
                    <a href="{{route('accounts.dashboard')}}"><span class="semi-bold">Abstract</span></a>
                    </li>
                    @endif
                    <li>
                    <a href="{{route('awards')}}"><span class="semi-bold">Awards</span></a>
                    </li>
                    <li>
                    <a href="{{route('reviews')}}"><span class="semi-bold">Reviews</span></a>
                    </li>
                    <li>
                    <a href="{{route('gallery')}}"><span class="semi-bold">Gallery</span></a>
                    </li>
                    <li>
                    <a href="{{route('contact-us')}}"><span class="semi-bold">Contact Us</span></a>
                    </li> 
                      <li>
                    <a href="{{route('venue-and-accommodation')}}"><span class="semi-bold">Venue & Accommodation</span></a>
                    </li> 
                    <li>
                    <a href="{{route('privacy-policy')}}"><span class="semi-bold">Privacy Policy</span></a>
                    </li> 
                </ul>
                -->
            </aside>
        </div>
       
        <div class="col-lg-3 col-md-6">
            <aside class="widget">
                <h3 class="widget-title">Support</h3>
                <ul>
                    <li>
                    <a href="{{route('contact-us')}}"><span class="semi-bold">Contact Us</span></a>
                    </li>
                    <li>
                    <a href="{{route('guidelines')}}"><span class="semi-bold">Guidelines</span></a>
                    </li>
                    <li>
                    <a href="{{route('about')}}"><span class="semi-bold">About Us</span></a>
                    </li>
                    <!-- <li>
                    <a href="{{route('privacy-policy')}}"><span class="semi-bold">Privacy Policy</span></a>
                    </li> -->
                </ul>
            </aside>
        </div>
        </div>
    </div>
    <div class="lower-footer">
        <div class="row align-items-center">
        <div class="col-lg-6">
            <div class="footer-newsletter">
                <h5>Subscribe us for more updates & news !!</h5>
               
                <form class="subscribe"  method="post" action="{{route('subscribe-submit')}}" id="subscribe">
                    <input type="email" id="subscribe_email"  class="email-form-control" name="email" placeholder="Enter Your Email" style="color:#ffff" required>
                    <button type="submit" class="button-round">Subscribe</button>
                </form>
                <h5 id="success_mess" style="display:none;">Submitted successfully</h5>
            </div>
        </div>
        <div class="col-lg-6 text-right">
            <div class="social-links">
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
                    <i class="fab fa-whatsapp" ></i>
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
            <div class="footer-menu">
                <ul>
                    <li>
                    <a href="{{route('privacy-policy')}}">Privacy Policy</a>
                    </li>
                    <!-- <li>
                    <a href="{{route('term-condition')}}">Term & Condition</a>
                    </li>
                    <li>
                    <a href="{{route('faq')}}">FAQ</a>
                    </li> -->
                </ul>
            </div>
        </div>
        </div>
    </div>
</div>
</div>
<div class="bottom-footer">
<div class="container">
    <div class="copy-right text-center">Copyright &copy; {{date('Y')}} IRCC All rights reserved.</div>
</div>
</div>
</footer>

