<!doctype html>
<html lang="en">
   
<!-- Mirrored from demo.bosathemes.com/html/unbound/ by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 08 Nov 2023 12:21:17 GMT -->
<head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <!-- favicon -->
      <link rel="icon" type="image/png" href="{{ url("front/images/favicon.png")}}">
      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="{{ url("front/vendors/bootstrap/css/bootstrap.min.css")}}" media="all">
      <!-- <link rel="stylesheet" href="{{ url("image/tagsinput/bootstrap-tagsinput.css")}}" type="text/css"> -->
      <!-- Fonts Awesome CSS -->
      <link rel="stylesheet" type="text/css" href="{{ url("front/vendors/fontawesome/css/all.min.css")}}">
      <!-- Elmentkit Icon CSS -->
      <link rel="stylesheet" type="text/css" href="{{ url("front/vendors/elementskit-icon-pack/assets/css/ekiticons.css")}}">
      <!-- jquery-ui css -->
      <link rel="stylesheet" type="text/css" href="{{ url("front/vendors/jquery-ui/jquery-ui.min.css")}}">
      <!-- modal video css -->
      <link rel="stylesheet" type="text/css" href="{{ url("front/vendors/modal-video/modal-video.min.css")}}">
      <!-- light box css -->
      <link rel="stylesheet" type="text/css" href="{{ url("front/vendors/fancybox/dist/jquery.fancybox.min.css")}}">
      <!-- slick slider css -->
      <link rel="stylesheet" type="text/css" href="{{ url("front/vendors/slick/slick.css")}}">
      <link rel="stylesheet" type="text/css" href="{{ url("front/vendors/slick/slick-theme.css")}}">
      <link rel="stylesheet" href="{{ url("front/js/preloader.css")}}">
      <!-- google fonts -->
      <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&amp;family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&amp;display=swap" rel="stylesheet">
      <!-- Custom CSS -->
      <link rel="stylesheet" type="text/css" href="{{ url("front/css/style.css")}}">
      <title>International Research Conference for Children</title>
      <!-- Givebutter Widget Script -->
      <script async src="https://widgets.givebutter.com/latest.umd.cjs?acct=URp4zkPv0ckx3N3w&p=other"></script>
   </head>
   <body class="home">
      <div id="siteLoader" class="site-loader">
         <div class="preloader-content">
            <img src="{{ url("front/images/loader1.html")}}" alt="">
         </div>
      </div>
      <div id="page" class="full-page">
        @include('components.header')
        @yield('head')
        @yield('content')
       </div>
       @include('components.footer')
         <a id="backTotop" href="#" class="to-top-icon">
            <i class="fas fa-chevron-up"></i>
         </a>
         <!-- custom search field html -->
            <div class="header-search-form">
               <div class="container">
                  <div class="header-search-container">
                     <form class="search-form" role="search" method="get" >
                        <input type="text" name="s" placeholder="Enter your text...">
                     </form>
                     <a href="#" class="search-close">
                        <i class="fas fa-times"></i>
                     </a>
                  </div>
               </div>
            </div>
         <!-- header html end -->
         <!-- <div class="preloader">
         <div class="preloader-inner">
            <div class="preloader-dot"></div>
            <div class="preloader-dot"></div>
            <div class="preloader-dot"></div>
            <div class="preloader-dot"></div>
         </div>
      </div> -->
      </div>

      <!-- JavaScript -->
     
     
      <script src="{{ url("front/vendors/bootstrap/js/bootstrap.min.js")}}"></script>
      <script src="{{ url("front/js/jquery.js")}}"></script>
      <script src="{{ url("front/vendors/jquery-ui/jquery-ui.min.js")}}"></script>
      <script src="{{ url("front/vendors/countdown-date-loop-counter/loopcounter.js")}}"></script>
      <script src="{{ url("front/js/jquery.counterup.js")}}"></script>
      <script src="{{ url("front/vendors/modal-video/jquery-modal-video.min.js")}}"></script>
      <script src="{{ url("front/vendors/masonry/masonry.pkgd.min.js")}}"></script>
      <script src="{{ url("front/vendors/fancybox/dist/jquery.fancybox.min.js")}}"></script>
      <script src="{{ url("front/vendors/slick/slick.min.js")}}"></script>
      <script src="{{ url("front/js/jquery.slicknav.js")}}"></script>
      <script src="{{ url("front/js/custom.min.js")}}"></script>
      <script src="{{ url("front/js/sweet-alert.js")}}"></script>
     
   <script>
   (function(){var js = "window['__CF$cv$params']={r:'822dc6073f57480e',t:'MTY5OTQ0NjA2My41NzgwMDA='};_cpo=document.createElement('script');_cpo.nonce='',_cpo.src='../../cdn-cgi/challenge-platform/h/g/scripts/jsd/9914b343/main.js',document.getElementsByTagName('head')[0].appendChild(_cpo);";var _0xh = document.createElement('iframe');_0xh.height = 1;_0xh.width = 1;_0xh.style.position = 'absolute';_0xh.style.top = 0;_0xh.style.left = 0;_0xh.style.border = 'none';_0xh.style.visibility = 'hidden';document.body.appendChild(_0xh);function handler() {var _0xi = _0xh.contentDocument || _0xh.contentWindow.document;if (_0xi) {var _0xj = _0xi.createElement('script');_0xj.innerHTML = js;_0xi.getElementsByTagName('head')[0].appendChild(_0xj);}}if (document.readyState !== 'loading') {handler();} else if (window.addEventListener) {document.addEventListener('DOMContentLoaded', handler);} else {var prev = document.onreadystatechange || function () {};document.onreadystatechange = function (e) {prev(e);if (document.readyState !== 'loading') {document.onreadystatechange = prev;handler();}};}})();</script></body>
   <script>
   
$(document).ready(function() {
var url = [location.protocol, '//', location.host, location.pathname].join('');  
console.log(url);
console.log(location.pathname);
         $('.navigation.active').removeClass('current-menu-item');
         $('.navigation  a[href="' + url  + '"]').parent().addClass('current-menu-item');
         $(this).parent().addClass('active').siblings().removeClass('current-menu-item');


});
</script>
<script>
$(document).ready(function () {
  $('#recipeCarousel').carousel({
  interval: 3000
})

$('.carousel .carousel-item').each(function(){
    var minPerSlide = 6;
    var next = $(this).next();
    if (!next.length) {
    next = $(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo($(this));
    
    for (var i=0;i<minPerSlide;i++) {
        next=next.next();
        if (!next.length) {
        	next = $(this).siblings(':first');
      	}
        
        next.children(':first-child').clone().appendTo($(this));
      }
});

		
});


	
</script>
<script>
    $('#subscribe').submit(function (event) {
      event.preventDefault();
      var valid = 1
      var email = $('#subscribe_email').val();
      console.log(email);
      if (validateEmail(email)) {
   
      } else {
         valid = 0;
         $('#email').text('email not valid');
      }
      if (valid == 1) {
         var url = $(this).attr('action');
         $.ajax({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: url,
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
   
               if (response.error == 0) {
                  $('.form-validation').text('');
                  $('#subscribe')[0].reset();
                  $('#success_mess').show();
               } else {
                  $('#success_mess').text( response.message)
                  $('#success_mess').show();
                 
               }
            }
   
         });
      }else{
         $('#success_mess').text('email not valid');
         $('#success_mess').show();
      }
   });
   function validateEmail(email) {
   var pattern = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
   
   return $.trim(email).match(pattern) ? true : false;
   }  
</script>
<script>

 $(document).ready(function() {
     $(document).on('click', '#abstractBook', function() {
          $('#abstractBookModal').modal('show');
         // console.log('ffff');         
      });
      $(document).on('click', '.modal-close', function() {
          $('#abstractBookModal').modal('hide');
          console.log('ffff');           
      });

}); 

</script>
@yield('script')
@stack('scripts')
<!-- Mirrored from demo.bosathemes.com/html/unbound/ by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 08 Nov 2023 12:22:50 GMT -->
</html>