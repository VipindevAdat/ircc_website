@extends('layouts.app')
@section('head')
<style>
  .testimonial-content p {
      font-weight: 300;
   }
   .testimonial-content h5 {
      text-align: right;
   }
   .testimonial-icon{
      text-align: right;   
   }
   .single-gallery{
    width:50% !important;
   }
   @media screen and (max-width: 749px) {
    .single-gallery{
    width:100% !important;
   }
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

<div class="gallery-section testimonial-page-section">
        <div class="container">
      
            <div class="gallery-outer-wrap">
                <div class="gallery-inner-wrap gallery-container grid">
                <div class="row gx-5">
                @if(!empty($reviews))
                
                @foreach($reviews as $review)
                <div class="col-md-6">
                <div class="single-gallery grid-item px-3">
                
                        <div class="testimonial-item text-center">
                           <div class="testimonial-content">
                              <div class="testimonial-icon">
                                 <i aria-hidden="true" class="icon icon-quote"></i>
                              </div>
                              <p>{!!$review->message!!}</p>
                              <h5>{{$review->author}}</h5>
                           </div>
                        </div>
                </div>
                </div>
                @endforeach
                @endif
                   
                </div>
            </div>
        </div>
    </div>

</main>
@endsection
@section('script')

@endsection