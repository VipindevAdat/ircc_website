@extends('layouts.app')
@section('head')
<style>
  .testimonial-content p {
      font-weight: 300;
      text-align: justify;
   }
   .testimonial-content h5 {
      text-align: right;
   }
   .testimonial-icon{
      text-align: right;   
   }
   
</style>
@endsection
@section('content')
<main id="content" class="site-main">
<!-- Inner Banner html start-->
<section class="inner-banner-wrap">
    <div class="inner-baner-container" style="background-image: url(front/images/inner-banner-img-1.jpg);">
        <div class="container">
            <div class="inner-banner-content">
            <h1 class="inner-title">Reviews</h1>
            </div>
        </div>
    </div>
</section>

<div class="testimonial-page-section">
    <div class="container">
    <div class="row" >
        @if(!empty($reviews))
        @foreach($reviews as $review)
            <div class="col-md-6">
            <div class="testimonial-item text-center">
                <div class="testimonial-content">
                    <div class="testimonial-icon">
                        <i aria-hidden="true" class="icon icon-quote"></i>
                    </div>
                    <p>{!!$review->message!!}</p>
                    <h5>{{$review->author}}</h5>
                </div>
                <!-- <figure class="testimonial-img">
                    <img src="assets/images/unbound-img11.jpg" alt="">
                </figure>
                <h3>{{$review->author}}</h3> -->
            </div>
            </div>
            
            @endforeach
            @endif
                  
        </div>
    </div>
</div>

</main>
@endsection
@section('script')

@endsection