@extends('layouts.app')
@section('head')
<link rel="stylesheet" type="text/css" href="{{ url("front/css/fresco.css")}}" />
<style>
   .gallery-inner-wrap .gallery-img a:hover .play-vd {
    opacity: 1;
}
.gallery-inner-wrap .gallery-img .play-vd {
    font-size: 22px;
    position: absolute;
    left: 50%;
    top: 50%;
    margin-bottom: 0;
    -webkit-transform: translate(-50%, -50%);
    -moz-transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    -o-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    /* width: 100%; */
    width: 55px !important;
    text-align: center;
    z-index: 1;
    color: #ffffff;
    opacity: 0;
    -webkit-transition: all 0.4s;
    -moz-transition: all 0.4s;
    -ms-transition: all 0.4s;
    -o-transition: all 0.4s;
    transition: all 0.4s;
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
            <h1 class="inner-title">Gallery</h1>
            </div>
        </div>
    </div>
</section>
<!-- Inner Banner html end-->
<!-- gallery section html start -->
<div class="gallery-section">
    <div class="container">
    @if($galleries->count())
        <div class="gallery-outer-wrap">
            <div class="gallery-inner-wrap gallery-container grid">
            @if($galleries->count())
            @foreach($galleries as $gallery)
            <div class="single-gallery grid-item" >
                <figure class="gallery-img">
                    @if($gallery->gallery_type==1)
                        @if($gallery->getMedia('gallery')->first())
                            @php 
                            $featued=$gallery->getMedia('gallery')->first()->getUrl();
                            @endphp
                            <a href="{{$featued}}" class="fresco" data-fresco-group="{{$gallery->id}}" data-fresco-caption="{{$gallery->name}}">
                                <div class="img gallery"><img class="gallery-imgs" src="{{$featued}}" alt=""></div>
                                <div class="info">
                                </div>
                                <h4>{{$gallery->name}}</h4>
                            </a>
                            @foreach($gallery->getMedia('gallery') as $image)
                            @if($image->getUrl()<>$featued)
                            <a href="{{$image->getUrl()}}" class="fresco" data-fresco-group="{{$gallery->id}}" data-fresco-caption="{{$gallery->name}}"></a>
                            @endif
                            @endforeach
                        @endif
                    @else
                        @php
                        $videoURL = $gallery->video_link;
                        $video_id = explode("?v=", $videoURL);
                        $video_id = $video_id[1];
                        $urlArr = explode("/",$videoURL);
                        $urlArrNum = count($urlArr);
                        $youtubeVideoId = $urlArr[$urlArrNum - 1];
                        $thumbURL = 'http://img.youtube.com/vi/'.$video_id.'/0.jpg';
                        @endphp
                        <a href="{{$gallery->video_link}}?feature=shared" class="fresco"  data-fresco-caption="{{$gallery->name}}">
                        <div class="img gallery"><img class="gallery-imgs" src="{{$thumbURL}}" alt=""></div>
                       
                      <img class="play-vd" src="{{asset('front/images/play-button.png')}}">
                        <h4>{{$gallery->name}}</h4>
                        </a>
                    @endif
                </figure>
            </div>
           
            @endforeach
           @endif
        </div>
        </div>
    @else
    <div class="section-head text-center mt-4">
        <h2 class="section-title">No images found</h2>
    </div>
    @endif
</div>
</main>
@endsection
@section('script')
<script type="text/javascript" src="{{ url("front/js/fresco.js")}}"></script>

@endsection