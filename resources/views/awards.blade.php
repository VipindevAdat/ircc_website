@extends('layouts.app')
@section('head')
<style>
/* Eye-catching heading styles */
.inner-title {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 3.5rem !important;
    font-weight: 700 !important;
    color: #FFD700 !important;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3), 0 0 20px rgba(255,215,0,0.5);
    letter-spacing: 2px;
    text-transform: uppercase;
}
.section-title {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 2.8rem !important;
    font-weight: 700 !important;
    color: #F15B43 !important;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    letter-spacing: 1.5px;
    margin-bottom: 2rem !important;
}

.award-image-container {
    width: 100%;
    height: 750px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: relative;
}
.award-image-container img {
    width: 100%;
    height: 750px;
    object-fit: cover;
    object-position: center;
    border-radius: 8px;
    display: block;
    min-width: 100%;
    min-height: 750px;
}
.award-image-container-left {
    height: 650px;
}
.award-image-container-left img {
    object-fit: contain;
    height: 650px;
    min-height: 650px;
}
.award-image-container-right {
    height: 650px;
}
.award-image-container-right img {
    object-fit: contain;
    height: 650px;
    min-height: 650px;
}
</style>
@endsection
@section('content')
<main id="content" class="site-main">
   <!-- Inner Banner html start-->
   <section class="inner-banner-wrap">
      <div class="inner-baner-container" style="background-image: url('{{ asset('front/images/inner-banner-img-1.jpg') }}');">
         <div class="container">
            <div class="inner-banner-content">
               <h1 class="inner-title">{{ optional($settings->where('code', 'awards_banner_title')->first())->value ?: 'Awards' }}</h1>
            </div>
         </div>
      </div>
   </section>
   <!-- Inner Banner html end-->
   <div class="carrer-page-section">
      <div class="container">
         <div class="row">
            <div class="col-lg-12">
               <div class="section-heading text-center mb-5">
                  <h2 class="section-title">{{ optional($settings->where('code', 'awards_section_heading')->first())->value ?: 'GYRA Awards' }}</h2>
               </div>
            </div>
         </div>

         @php
            $rows = isset($awardGraphics) ? $awardGraphics->values() : collect();
            $first = $rows->get(0);
            $second = $rows->get(1);
            $rest = $rows->slice(2);
            $firstUrl = $first?->imageUrl();
            $secondUrl = $second?->imageUrl();
         @endphp

         @if($firstUrl || $secondUrl)
         <div class="row mb-5">
            @if($firstUrl)
            <div class="col-lg-7 mb-4">
               <div class="award-image-container award-image-container-left">
                  <img src="{{ $firstUrl }}" alt="{{ $first->alt_text ?: 'Awards' }}">
               </div>
            </div>
            @endif
            @if($secondUrl)
            <div class="col-lg-5 mb-4">
               <div class="award-image-container award-image-container-right">
                  <img src="{{ $secondUrl }}" alt="{{ $second->alt_text ?: 'Awards' }}">
               </div>
            </div>
            @endif
         </div>
         @foreach($rest as $extra)
            @if($extra->imageUrl())
            <div class="row mb-5">
               <div class="col-12">
                  <div class="award-image-container">
                     <img src="{{ $extra->imageUrl() }}" alt="{{ $extra->alt_text ?: 'Awards' }}">
                  </div>
               </div>
            </div>
            @endif
         @endforeach
         @else
         <!-- Fallback when no CMS images exist -->
         <div class="row mb-5">
            <div class="col-lg-7 mb-4">
               <div class="award-image-container award-image-container-left">
                  <img src="{{ asset('front/images/awards-ircc26.PNG') }}"
                       alt="IRCC'26 Awards - Award Categories and Prizes">
               </div>
            </div>
            <div class="col-lg-5 mb-4">
               <div class="award-image-container award-image-container-right">
                  <img src="{{ asset('front/images/awards-26.PNG') }}"
                       alt="IRCC'26 Special Awards - Agriculture and Literature Categories">
               </div>
            </div>
         </div>
         @endif
      </div>
   </div>
</main>
@endsection