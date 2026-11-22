@extends('layouts.app')
@section('head')
   <style>
      /* Ensure page scrolling works */
      html,
      body {
         overflow-x: hidden;
         overflow-y: auto !important;
         height: auto !important;
         position: relative !important;
      }

      #page.full-page {
         overflow: visible !important;
         height: auto !important;
      }

      .banner-date {
         font-size: 35px;
         color: #F15B43;
         font-weight: 600;
         line-height: 1.3;
         margin-bottom: 18px;
         text-transform: uppercase;
      }

      .last-date {
         font-size: 35px;
         color: #FFFFFF;
         font-weight: 500;
         line-height: 1.3;
         margin-bottom: 18px;
         /* text-transform: uppercase; */
      }

      .associates img {
         height: 135px;
         object-fit: contain;
      }

      .associates article {
         padding: 0px !important;
         box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.1);
         margin-bottom: 0px !important;
      }

      .associates .donate-inner .row {
         display: flex;
         flex-wrap: wrap;
      }

      .associates .donate-inner .row>[class*='col-'] {
         display: flex;
      }

      .associates .donate-content {
         display: flex;
         flex-direction: column;
         height: 100%;
         width: 100%;
         border-top: 4px solid #F15B43;
      }

      .associates .content-section {
         flex: 1;
         display: flex;
         flex-direction: column;
         justify-content: space-between;
      }

      .associates .donate-inner .donate-content p {
         margin-bottom: 0px;
      }

      .associates .donate-inner h3 {
         justify-content: center;
      }

      .third-bg {
         background-color: #009032;
      }

      .associates .image-section {
         border-radius: 25px 25px 0px 0px;
         background-color: #FFFFFF;
      }

      .blink-soft {
         animation: blinker 1s linear infinite;
      }

      /* Make both images the same size */
      .event-inner .event-item.event-item-bg .overlay {
         position: absolute;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
      }

      /* Ensure both sections align properly */
      .event-inner .row,
      .home-event .row {
         margin-left: 0;
         margin-right: 0;
      }

      /* Ensure containers align properly */
      .home-event .container {
         padding-left: 15px;
         padding-right: 15px;
      }

      /* Apply Research Areas card style to IRCC section */
      .ircc {
         background: #fff;
         border-radius: 12px;
         padding: 30px 25px;
         box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
         border-top: 4px solid #F15B43 !important;
         max-width: 1100px;
         margin: 0 auto 30px auto;
         text-align: left;
      }

      .ircc h2,
      .ircc h3 {
         text-align: center;
      }

      .ircc p,
      .ircc ul,
      .ircc li {
         text-align: left;
      }

      /* Apply Research Areas card style to Video section */
      .ircc-video {
         background: #fff;
         border-radius: 12px;
         padding: 30px 25px;
         box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
         border-top: 4px solid #F15B43 !important;
         max-width: 900px;
         margin: 0 auto;
      }

      /* Match both images to same height and width - override default styles */
      @media (min-width: 992px) {
         .event-inner .col-lg-7 .event-item.event-item-bg {
            height: 500px !important;
            min-height: 500px !important;
            width: 100% !important;
            max-width: 100% !important;
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            padding-bottom: 30px;
            box-sizing: border-box;
         }

         .home-about-left {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box;
         }

         .home-about-left .home-about-image {
            width: 110% !important;
            max-width: none !important;
            overflow: visible;
            box-sizing: border-box;
            display: block;
            margin-right: -10%;
         }

         .home-about-image img {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            max-height: 630px !important;
            object-fit: contain;
            display: block;
            box-sizing: border-box;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #F15B43;
            padding: 10px;
         }

         /* Apply Research Areas card style to Get Involved Now text */
         .event-item {
            height: auto !important;
            min-height: auto !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
            border-radius: 12px;
            padding: 30px 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #F15B43;
            box-sizing: border-box;
         }

         /* Make both columns in Get Involved Now section same height */
         .event-inner .row.align-items-start {
            align-items: stretch !important;
         }

         .event-inner .col-lg-5 {
            display: flex;
            flex-direction: column;
         }

         .event-inner .col-lg-5>* {
            flex: 0 0 auto;
         }

         .event-inner .col-lg-7 {
            display: flex;
            flex-direction: column;
         }

         .event-inner .col-lg-7 .event-item {
            flex: 1;
            height: 100% !important;
            min-height: 100% !important;
         }

         .event-inner .row.align-items-start>div {
            display: flex;
            flex-direction: column;
         }

         .event-inner .col-lg-7 {
            display: flex;
         }

         .event-inner .col-lg-7 .event-item {
            flex: 1;
            height: 100%;
         }
      }

      /* Reduce spacing between Get Involved Now and In Association With sections */
      section.home-event.bg-light-grey:not(.associates) {
         padding-bottom: 30px !important;
      }

      section.home-event.bg-light-grey.associates {
         padding-top: 30px !important;
         padding-bottom: 60px !important;
      }

      @media (max-width: 768px) {
         section.home-event.bg-light-grey:not(.associates) {
            padding-bottom: 20px !important;
         }

         section.home-event.bg-light-grey.associates {
            padding-top: 20px !important;
            padding-bottom: 40px !important;
         }

         .ircc {
            border-top: 4px solid #F15B43 !important;
            padding: 20px 15px;
         }

         .ircc-video {
            border-top: 4px solid #F15B43 !important;
            padding: 20px 15px;
         }

         .event-item {
            border-top: 4px solid #F15B43 !important;
         }

         .home-about-image img {
            border-top: 4px solid #F15B43 !important;
         }

         .domain-card {
            border-top: 4px solid #F15B43 !important;
         }
      }

      /* Floating Speakers Carousel */
      .floating-speakers-carousel {
         position: fixed;
         left: 20px;
         top: 50%;
         transform: translateY(-50%);
         width: 320px;
         background: #ffffff;
         border-radius: 12px;
         box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
         padding: 20px;
         z-index: 1000;
         max-height: 90vh;
         overflow-y: auto;
         border-top: 4px solid #F15B43;
      }

      .floating-speakers-carousel h3 {
         color: #F15B43;
         font-size: 20px;
         font-weight: 600;
         margin-bottom: 20px;
         text-align: center;
         padding-bottom: 15px;
         border-bottom: 2px solid #f0f0f0;
      }

      .floating-speakers-carousel .speakers-carousel-wrapper {
         position: relative;
      }

      .floating-speakers-carousel .speakers-carousel {
         overflow: hidden;
         position: relative;
         margin-bottom: 15px;
      }

      .floating-speakers-carousel .speakers-track {
         display: flex;
         transition: transform 0.5s ease-in-out;
      }

      .floating-speakers-carousel .speaker-slide {
         min-width: 100%;
         flex-shrink: 0;
         padding: 0 5px;
         box-sizing: border-box;
      }

      .floating-speakers-carousel .speaker-card {
         background: #f8f9fa;
         border-radius: 8px;
         padding: 20px 15px;
         text-align: center;
      }

      .floating-speakers-carousel .speaker-image-wrapper {
         width: 140px;
         height: 140px;
         margin: 0 auto 15px;
         border-radius: 50%;
         overflow: hidden;
         border: 3px solid #F15B43;
         background: #f0f0f0;
         display: flex;
         align-items: center;
         justify-content: center;
         position: relative;
      }

      .floating-speakers-carousel .speaker-image-wrapper img {
         min-width: 100%;
         min-height: 100%;
         width: auto;
         height: auto;
         max-width: 150%;
         max-height: 150%;
         object-fit: cover;
         object-position: center 25%;
         display: block;
      }

      .floating-speakers-carousel .speaker-card h4 {
         color: #333;
         font-size: 16px;
         font-weight: 600;
         margin-bottom: 8px;
         line-height: 1.3;
      }

      .floating-speakers-carousel .speaker-designation {
         color: #F15B43;
         font-size: 14px;
         font-weight: 500;
         margin-bottom: 5px;
      }

      .floating-speakers-carousel .speaker-organization {
         color: #666;
         font-size: 12px;
         margin-bottom: 0;
      }

      .floating-speakers-carousel .speakers-carousel-controls {
         display: flex;
         justify-content: center;
         align-items: center;
         gap: 15px;
         margin-top: 15px;
      }

      .floating-speakers-carousel .speaker-prev-btn,
      .floating-speakers-carousel .speaker-next-btn {
         background-color: #F15B43;
         color: white;
         border: none;
         width: 40px;
         height: 40px;
         border-radius: 50%;
         cursor: pointer;
         font-size: 16px;
         display: flex;
         align-items: center;
         justify-content: center;
         transition: all 0.3s;
         padding: 0;
      }

      .floating-speakers-carousel .speaker-prev-btn:hover,
      .floating-speakers-carousel .speaker-next-btn:hover {
         background-color: #d14a35;
         transform: scale(1.1);
      }

      .floating-speakers-carousel .speaker-prev-btn:disabled,
      .floating-speakers-carousel .speaker-next-btn:disabled {
         cursor: not-allowed;
         opacity: 0.5;
      }

      .floating-speakers-carousel .speaker-indicators {
         display: flex;
         gap: 8px;
      }

      .floating-speakers-carousel .speaker-indicator {
         width: 10px;
         height: 10px;
         border-radius: 50%;
         background-color: #ccc;
         cursor: pointer;
         transition: all 0.3s;
      }

      .floating-speakers-carousel .speaker-indicator:hover {
         transform: scale(1.2);
         background-color: #F15B43;
      }

      .floating-speakers-carousel .speaker-indicator.active {
         background-color: #F15B43;
         width: 12px;
         height: 12px;
      }

      .floating-speakers-carousel .speaker-counter {
         text-align: center;
         margin-top: 10px;
         color: #666;
         font-size: 12px;
      }

      @media (max-width: 1200px) {
         .floating-speakers-carousel {
            width: 280px;
            left: 15px;
         }
      }

      @media (max-width: 992px) {
         .floating-speakers-carousel {
            width: 260px;
            left: 10px;
         }
      }

      @media (max-width: 768px) {
         .floating-speakers-carousel {
            position: fixed;
            bottom: 20px;
            left: 10px;
            top: auto;
            transform: none;
            width: 200px;
            padding: 15px;
            z-index: 1000;
            max-height: 60vh;
         }

         .floating-speakers-carousel h3 {
            font-size: 16px;
            margin-bottom: 15px;
         }

         .floating-speakers-carousel .speaker-image-wrapper {
            width: 100px;
            height: 100px;
         }

         .floating-speakers-carousel .speaker-image-wrapper img {
            object-position: center 25%;
         }

         .floating-speakers-carousel .speaker-card h4 {
            font-size: 14px;
         }

         .floating-speakers-carousel .speaker-designation {
            font-size: 12px;
         }

         .floating-speakers-carousel .speaker-organization {
            font-size: 11px;
         }

         .floating-speakers-carousel .speaker-prev-btn,
         .floating-speakers-carousel .speaker-next-btn {
            width: 35px;
            height: 35px;
            font-size: 14px;
         }
      }

      /* Dates To Remember Table - Fixed Position */
      .dates-table {
         background: #fff;
         border-radius: 10px;
         box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
         padding: 12px 15px;
         margin-bottom: 0;
         border: 2px solid #F15B43;
         width: 100%;
         box-sizing: border-box;
      }

      /* Remove top margin from image container */
      .home-about-left {
         margin-top: 0;
      }

      .dates-table table {
         width: 100%;
         border-collapse: collapse;
         border-right: none;
      }

      .dates-table h3 {
         color: #F15B43;
         font-size: 20px;
         font-weight: 600;
         margin-bottom: 10px;
         text-align: center;
         border-bottom: 2px solid #F15B43;
         padding-bottom: 8px;
      }

      .dates-table table td {
         padding: 6px 5px;
         font-size: 16px;
         border-bottom: 1px solid #e0e0e0;
         border-right: none;
         text-align: center;
      }

      .dates-table table td span.label {
         font-weight: 600;
         color: #333;
         display: block;
         margin-bottom: 3px;
         font-size: 15px;
      }

      .dates-table table td span.date {
         color: #F15B43;
         font-weight: 500;
         display: block;
         font-size: 15px;
      }

      .dates-table table tr:last-child td {
         border-bottom: none;
      }

      /* Make image match dates table width exactly */
      .home-about-left {
         width: 100%;
         overflow: visible;
      }

      .home-about-image {
         display: block;
         width: 110%;
         margin-left: 0;
         margin-right: -10%;
         box-sizing: border-box;
      }

      /* Ensure column and row allow overflow for image extension */
      .event-inner .row {
         overflow: visible;
      }

      .event-inner .row>.col-lg-5:first-child {
         overflow: visible;
      }

      /* Responsive styles for mobile */
      @media (max-width: 768px) {
         .dates-table {
            padding: 15px;
            margin-bottom: 15px;
         }

         .dates-table h3 {
            font-size: 16px;
            margin-bottom: 10px;
         }

         .dates-table table td {
            padding: 8px 3px;
            font-size: 11px;
         }

         .dates-table table td span.label {
            font-size: 10px;
            margin-bottom: 3px;
         }

         .dates-table table td span.date {
            font-size: 10px;
         }
      }

      /* Research Domains Section */
      /* View Guidelines link styling */
      .research-domains-section a[href*="guidelines"] {
         font-size: 18px !important;
         font-weight: 700 !important;
         padding: 10px 20px !important;
         border-radius: 6px;
         transition: all 0.3s ease;
      }

      .research-domains-section a[href*="guidelines"]:hover {
         background-color: #F15B43;
         color: #fff !important;
         transform: scale(1.05);
         box-shadow: 0 4px 8px rgba(241, 91, 67, 0.3);
      }

      .research-domains-section {
         padding: 60px 0;
         background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
      }

      .domains-grid {
         display: grid;
         grid-template-columns: repeat(6, 1fr);
         gap: 25px;
         margin-top: 40px;
         max-width: 900px;
         margin-left: auto;
         margin-right: auto;
      }

      /* First 3 items span 2 columns each */
      .domains-grid .domain-card:nth-child(1),
      .domains-grid .domain-card:nth-child(2),
      .domains-grid .domain-card:nth-child(3) {
         grid-column: span 2;
      }

      /* Last 2 items centered: each spans 2 columns */
      .domains-grid .domain-card:nth-child(4) {
         grid-column: 2 / 4;
      }

      .domains-grid .domain-card:nth-child(5) {
         grid-column: 4 / 6;
      }

      @media (max-width: 992px) {
         .domains-grid {
            grid-template-columns: repeat(2, 1fr);
            max-width: 600px;
         }
      }

      @media (max-width: 768px) {
         .domains-grid {
            grid-template-columns: repeat(2, 1fr);
            max-width: 100%;
            gap: 15px;
            padding: 0 15px;
         }

         /* Reset all grid column spans on mobile */
         .domains-grid .domain-card:nth-child(1),
         .domains-grid .domain-card:nth-child(2),
         .domains-grid .domain-card:nth-child(3),
         .domains-grid .domain-card:nth-child(4),
         .domains-grid .domain-card:nth-child(5) {
            grid-column: span 1;
         }

         .domain-card {
            padding: 20px 15px;
         }

         .domain-card h4 {
            font-size: 16px;
            line-height: 1.3;
            word-wrap: break-word;
         }

         .domain-card .domain-code {
            font-size: 12px;
            margin-top: 8px;
         }
      }

      .domain-card {
         background: #fff;
         border-radius: 12px;
         padding: 30px 25px;
         text-align: center;
         box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
         transition: transform 0.3s ease, box-shadow 0.3s ease;
         border-top: 4px solid #F15B43;
      }

      .domain-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 8px 25px rgba(241, 91, 67, 0.2);
      }

      .domain-card h4 {
         color: #F15B43;
         font-size: 20px;
         font-weight: 600;
         margin-bottom: 10px;
      }

      .domain-card .domain-code {
         color: #666;
         font-size: 14px;
         font-weight: 500;
         font-style: italic;
      }

      .testimonial-content p {
         font-weight: 300;
      }

      .testimonial-content h3 {
         text-align: right;
      }

      .testimonial-content h5 {
         text-align: right;
      }

      .testimonial-icon-review {
         text-align: right;
      }

      @keyframes blinker {
         10% {
            opacity: 0;
         }
      }

      .truncate {
         overflow: hidden;
         text-overflow: ellipsis;
         display: -webkit-box;
         -webkit-line-clamp: 6;
         /* number of lines to show */
         line-clamp: 6;
         -webkit-box-orient: vertical;
      }

      /* Banner Speakers Carousel Styles */
      .banner-speakers-carousel {
         position: relative;
         z-index: 2;
      }

      .banner-speakers-carousel .speakers-carousel-wrapper {
         position: relative;
         width: 100%;
      }

      .banner-speakers-carousel .speakers-carousel {
         width: 100%;
      }

      .banner-speakers-carousel .speakers-track {
         width: 100%;
      }

      .banner-speakers-carousel .speaker-slide {
         width: 100%;
         flex-shrink: 0;
      }

      .banner-speakers-carousel .speaker-slide>div {
         backdrop-filter: blur(5px);
         width: 100%;
         max-width: 100%;
      }

      .banner-speakers-carousel .speaker-slide img {
         width: 100%;
         height: 100%;
         object-fit: cover;
         object-position: center 20%;
      }

      @media (max-width: 992px) {
         .banner-speakers-carousel {
            margin-top: 30px;
            padding: 15px !important;
         }

         .banner-speakers-carousel .speaker-slide>div>div:first-child {
            width: 140px !important;
            height: 140px !important;
         }
      }

      /* Speakers Carousel Styles */
      .speakers-carousel-wrapper {
         position: relative;
      }

      .speaker-prev-btn:hover,
      .speaker-next-btn:hover {
         background-color: #d14a35 !important;
         transform: scale(1.1);
      }

      .speaker-prev-btn:disabled,
      .speaker-next-btn:disabled {
         cursor: not-allowed;
         opacity: 0.5 !important;
      }

      .speaker-indicator:hover {
         transform: scale(1.2);
         background-color: #F15B43 !important;
      }

      .speaker-indicator.active {
         background-color: #F15B43 !important;
         width: 16px !important;
         height: 16px !important;
      }

      @media (max-width: 768px) {
         .speakers-carousel-wrapper {
            max-width: 100% !important;
            padding: 0 15px;
         }

         .speaker-slide>div {
            padding: 30px 20px !important;
         }

         .speaker-slide>div>div:first-child {
            width: 150px !important;
            height: 150px !important;
         }

         .speaker-slide h3 {
            font-size: 20px !important;
         }

         .speaker-slide p {
            font-size: 16px !important;
         }
      }
   </style>
@endsection
@section('content')
   <main id="content" class="site-main">
      <!-- full width banner image between navbar and hero section -->
      <div class="top-banner-image-wrapper" style="width: 100%; overflow: hidden; background: #000;">
         <img src="{{ optional($landingPage)->banner_image ? url('uploads/' . $landingPage->banner_image) : url('front/images/ircc27-banner.jpg') }}" alt="IRCC {{ $year }} Banner"
            style="width: 100%; height: auto; display: block; object-fit: contain;">
      </div>
      <!-- Announcement Banner Section -->
      <div class="ircc-announcement-banner" style="background: linear-gradient(135deg, #0d1527 0%, #151e32 50%, #1e2942 100%); border-top: 3px solid #F15B43; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 35px 20px; text-align: center; box-shadow: inset 0 2px 10px rgba(0,0,0,0.3);">
         <div class="container">
            <h2 style="color: #ffffff; font-size: clamp(24px, 4vw, 38px); font-weight: 800; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.3; font-family: 'Montserrat', sans-serif;">
               Thank You for Attending <span style="color: #F15B43;">IRCC {{ $year }}!</span>
            </h2>
            @if(!empty($nextYearLandingActive))
            <p style="color: #e2e8f0; font-size: clamp(16px, 2.5vw, 22px); font-weight: 500; margin: 0; letter-spacing: 0.3px; font-family: 'Montserrat', sans-serif;">
               Stay tuned for more updates on <a href="{{ route('year.landing', $nextYear) }}" style="color: #ff9d00; font-weight: 700; text-decoration: underline;">IRCC {{ $nextYear }}!</a>
            </p>
            @endif
         </div>
      </div>
      <!-- home banner section html start -->
      <section class="home-banner" style="background-image: url({{ url("front/images/banner.jpg")}}">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-lg-7">
                  <div class="banner-content">

                     <h2 class="banner-title">International Research Conference<br>for Children {{ $year }}</h2>
                     <!-- <div class="sub-title">DONATE TO CONTRIBUTE</div>
                        <h2 class="banner-title">Let's build the better world together</h2> -->
                     <div class="banner-text">
                        <p style="margin-bottom:20px;font-size: 25px;"><strong>Venue: </strong>525 Los Coches St, Milpitas,
                           CA 95035.</p>
                        @php
                           $eventDate = \Carbon\Carbon::parse($year.'-06-06');
                           $now = \Carbon\Carbon::now();
                        @endphp
                        @if($eventDate->format('Y-m-d') >= $now->format('Y-m-d'))
                           <p style="margin-bottom:5px;font-size: 25px;" class="last-date"><span>Conference Date: Saturday &
                                 Sunday, June 6-7, {{ $year }} (Two-Day Event) </span> <span id="days"
                                 class="blink-soft banner-date days-span"
                                 style="font-size:26px;padding-left:10px;font-weight: bold;display:none;color:#ffffff;"></span><span
                                 class="blink-soft days-span"
                                 style="font-size:26px;padding-left:5px;color:#ffffff;font-weight: bold;display:none">Days
                                 Left</span></p>
                           <p style="margin-bottom:15px;font-size: 25px;" class="last-date"><span>June 6th (Sat): 8:00 AM –
                                 7:00 PM</span> &nbsp;|&nbsp; <span>June 7th (Sun): 8:30 AM – 5:00 PM</span></p>
                        @endif
                        @if(optional($settings->where('code', 'registration_status')->first())->value == 1)
                        @else
                           <!-- <p style="margin-bottom:20px;font-size: 23px;" class="blink-soft"><strong>Registration Closed</strong></p> -->
                        @endif
                        @if(optional($settings->where('code', 'abstract_status')->first())->value == 1)
                           @if(\Carbon\Carbon::parse($settings->where('code', 'abstract_last_date')->first()->value) >= \Carbon\Carbon::now())
                              <p style="margin-bottom:20px;font-size: 25px;" class="last-date"><span>The Deadline for Abstract
                                    Submission is
                                    {{Carbon\Carbon::parse($settings->where('code', 'abstract_last_date')->first()->value)->isoFormat('Do MMMM, Y')}}</span>
                              </p>
                           @endif
                        @endif
                        <!-- <p style="margin-bottom:20px;" class="blink-soft"><strong>Venue is 750 meters from the Metro station (CUSAT)</strong></p> -->

                     </div>

                     <div class="banner-button">
                        @if(optional($settings->where('code', 'registration_status')->first())->value == 1)
                           @if(!auth('customer')->check())
                              <a href="{{route('login')}}" class="button-round" style="margin-right: 10px;">Login</a>
                              <a href="{{route('register')}}" class="button-round">Register Now</a>
                           @endif
                           <!-- <a href="{{route('register.abstract')}}" class="outline-round outline-round-white">Submit Abstract</a> -->
                        @elseif(optional($settings->where('code', 'abstract_status')->first())->value == 1)
                           <!-- <a href="{{route('register.abstract')}}" class="button-round">Submit Abstract</a> -->
                        @elseif(optional($settings->where('code', 'login_status')->first())->value == 1)
                           <a href="{{route('login')}}" class="button-round">Login</a>
                        @endif
                        <a href="{{route('guidelines')}}" class="button-round">Guidelines</a>
                        <a href="{{route('awards')}}" class="button-round">Awards</a>
                        {{-- <a href="{{route('venue-and-accommodation')}}" class="button-round">How to Reach Venue </a>
                        --}}
                     </div>

                  </div>

               </div>
               @if(isset($speakers) && $speakers->count() > 0)
                  <div class="col-lg-5">
                     <div class="banner-speakers-carousel" style="position: relative; padding: 20px; z-index: 2;">
                        <div class="speakers-carousel-wrapper banner-carousel-wrapper" style="position: relative;">
                           <div class="speakers-carousel banner-carousel" style="overflow: hidden; position: relative;">
                              <div class="speakers-track banner-track"
                                 style="display: flex; transition: transform 0.5s ease-in-out;">
                                 @foreach($speakers as $index => $speaker)
                                    @php
                                       $speakerName = $speaker->name;
                                       $speakerImageUrl = null;
                                       $normalizedName = preg_replace('/^(dr|mr|mrs|ms|prof)\.?\s*/i', '', $speakerName);
                                       $normalizedName = trim($normalizedName);
                                       $possibleNames = [
                                          strtolower(str_replace([' ', '-', '.', ','], ['', '', '', ''], $normalizedName)),
                                          strtolower(str_replace([' ', '-'], ['_', '_'], $normalizedName)),
                                          strtolower(str_replace(' ', '', $normalizedName)),
                                          strtolower($normalizedName),
                                          strtolower(str_replace([' ', '-', '.', ','], ['', '', '', ''], $speakerName)),
                                          strtolower(str_replace([' ', '-'], ['_', '_'], $speakerName)),
                                          strtolower(str_replace(' ', '', $speakerName)),
                                          strtolower($speakerName),
                                       ];
                                       $possibleNames = array_unique($possibleNames);
                                       $extensions = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];
                                       $speakersDir = public_path('front/images/speakers/');
                                       if (is_dir($speakersDir)) {
                                          foreach ($possibleNames as $name) {
                                             foreach ($extensions as $ext) {
                                                $imagePath = 'front/images/speakers/' . $name . '.' . $ext;
                                                if (file_exists(public_path($imagePath))) {
                                                   $speakerImageUrl = asset($imagePath);
                                                   break 2;
                                                }
                                             }
                                          }
                                       }
                                       if (!$speakerImageUrl) {
                                          $media = $speaker->getMedia('team')->first();
                                          $speakerImageUrl = $media ? \App\Models\Team::publicUrlForMedia($media) : null;
                                       }
                                       $imageUrl = $speakerImageUrl ?: asset('front/images/user-img.png');
                                    @endphp
                                    <div class="speaker-slide" data-index="{{ $index }}"
                                       style="min-width: 100%; flex-shrink: 0; padding: 0 15px; box-sizing: border-box;">
                                       <div
                                          style="background: rgba(255,255,255,0.98); border-radius: 12px; padding: 25px 20px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.25); border-top: 4px solid #F15B43;">
                                          <div
                                             style="width: 180px; height: 180px; margin: 0 auto 20px; border-radius: 50%; overflow: hidden; border: 4px solid #F15B43; background: #f8f9fa; position: relative;">
                                             <img src="{{ $imageUrl }}" alt="{{ $speaker->name }}"
                                                style="width: 100%; height: 100%; object-fit: cover; object-position: center 20%; display: block;"
                                                onerror="this.src='{{ asset('front/images/user-img.png') }}'">
                                          </div>
                                          <h3
                                             style="color: #333; font-size: 22px; font-weight: 600; margin-bottom: 10px; line-height: 1.3;">
                                             {{ $speaker->name }}</h3>
                                          <p
                                             style="color: #F15B43; font-size: 17px; font-weight: 500; margin-bottom: 8px; line-height: 1.4;">
                                             {{ $speaker->designation }}</p>
                                          @if($speaker->organization)
                                             <p style="color: #666; font-size: 15px; margin-bottom: 0; line-height: 1.4;">
                                                {{ $speaker->organization }}</p>
                                          @endif
                                       </div>
                                    </div>
                                 @endforeach
                              </div>
                           </div>
                           <button class="speaker-prev-btn banner-prev-btn"
                              style="position: absolute; top: 50%; left: -15px; transform: translateY(-50%); background: #F15B43; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
                              <i class="fas fa-chevron-left"></i>
                           </button>
                           <button class="speaker-next-btn banner-next-btn"
                              style="position: absolute; top: 50%; right: -15px; transform: translateY(-50%); background: #F15B43; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
                              <i class="fas fa-chevron-right"></i>
                           </button>
                           <div class="speaker-indicators banner-indicators" style="text-align: center; margin-top: 15px;">
                              @foreach($speakers as $index => $speaker)
                                 <span class="speaker-indicator banner-indicator {{ $index === 0 ? 'active' : '' }}"
                                    data-index="{{ $index }}"
                                    style="display: inline-block; width: 8px; height: 8px; background: #ccc; border-radius: 50%; margin: 0 4px; cursor: pointer;"></span>
                              @endforeach
                           </div>
                           <div class="speaker-counter banner-counter"
                              style="text-align: center; margin-top: 8px; color: #fff; font-size: 14px; font-weight: 500;">
                              <span class="current-speaker banner-current">1</span> / <span
                                 class="total-speakers banner-total">{{ $speakers->count() }}</span>
                           </div>
                        </div>
                     </div>
                  </div>
               @endif
            </div>

            <!-- Powered By & Gold Sponsor Section (side by side) -->
            <div class="row mt-3" style="margin-top: 15px;">
               <div class="col-lg-10 offset-lg-2">
                  <div style="display: flex; gap: 20px; justify-content: flex-end; align-items: stretch; flex-wrap: wrap;">
                     <!-- Powered By - Lifology -->
                     <div
                        style="background: rgba(255,255,255,0.96); border-radius: 12px; padding: 18px 24px; box-shadow: 0 6px 20px rgba(0,0,0,0.35); width: 340px; max-width: 100%; text-align: center; display: flex; flex-direction: column; justify-content: center;">
                        <h3 style="color: #222; font-size: 22px; font-weight: 600; margin-bottom: 12px;">Powered By</h3>
                        <a href="https://www.lifology.us" target="_blank" rel="noopener noreferrer"
                           style="display: flex; justify-content: center; align-items: center; text-decoration: none;">
                           <img src="{{ asset('front/images/lifology-logo.png') }}" alt="Powered By - Lifology"
                              style="max-width: 280px; height: auto;">
                        </a>
                        <a href="https://www.lifology.us" target="_blank" rel="noopener noreferrer"
                           style="display: block; margin-top: 8px; color: #F15B43; font-size: 14px; font-weight: 500; text-decoration: none;">www.lifology.us</a>
                     </div>
                     <!-- Gold Sponsor - Growing Stars -->
                     <div
                        style="background: rgba(255,255,255,0.96); border-radius: 12px; padding: 18px 24px; box-shadow: 0 6px 20px rgba(0,0,0,0.35); width: 340px; max-width: 100%; text-align: center; display: flex; flex-direction: column; justify-content: center;">
                        <h3 style="color: #DAA520; font-size: 22px; font-weight: 600; margin-bottom: 12px;">Gold Sponsor
                        </h3>
                        <a href="https://www.growingstars.com" target="_blank" rel="noopener noreferrer"
                           style="display: flex; justify-content: center; align-items: center; text-decoration: none;">
                           <img src="{{ asset('front/images/growing-stars-logo.jpeg') }}"
                              alt="Gold Sponsor - Growing Stars" style="max-width: 280px; height: auto;">
                        </a>
                     </div>
                  </div>
               </div>
            </div>

         </div>
         <div class="overlay"></div>
      </section>
      <!-- home banner section html end -->


      <!-- home about section html start -->
      <style>
         @media (max-width: 992px) {
            .bay-area-sponsor {
               position: static !important;
               width: 100% !important;
               margin-top: 30px;
               text-align: center !important;
            }
         }
      </style>
      <section class="home-event bg-light-grey" style="padding: 40px 0; position: relative;">
         <div class="container">
            <div class="ircc-video text-center mb-4" style="max-width: 100%;">
               <video controls preload="metadata" playsinline
                  @if(optional($landingPage)->video_thumbnail) poster="{{ url('uploads/' . $landingPage->video_thumbnail) }}" @endif
                  style="width: 100%; max-width: 100%; border-radius: 8px; display: block; background: #000;">
                  <source src="{{ optional($landingPage)->video ? url('uploads/' . $landingPage->video) : url('uploads/videos/ircc-all-speakers-full.mp4') }}" type="video/mp4">
                  Your browser does not support the video tag.
               </video>
            </div>
            <div class="ircc">
               <h2>IRCC: A Global Platform for Young Researchers</h2>

               <h3><strong>Empowering Young Minds on a Global Stage</strong></h3>

               <p>
                  <strong>IRCC is an international platform that brings together school-level student researchers,
                     eminent scientists, teachers, parents, administrators, and stakeholders from around the
                     world.</strong>
               </p>

               <p>
                  <strong>The conference will feature keynote sessions, plenaries, and special lectures by internationally
                     established scientists, along with talks, poster sessions, and presentations by school
                     students.</strong>
               </p>

               <h3><strong>Academic Value:</strong></h3>

               <ul>
                  <li><strong>Category-wise Best Presentation and Poster Awards <a href="{{route('awards')}}"
                           style="color: #F15B43; text-decoration: underline;">(View Awards)</a></strong></li>
                  <li><strong>Abstract Publication in Official Proceedings</strong></li>
                  <li><strong>Expert Judges from Stanford, MIT & Harvard</strong></li>
                  <li><strong>Authentic Research Presentation Experience</strong></li>
                  <li><strong>International Certificate of Participation</strong></li>
                  <li><strong>STEM and non-STEM Leadership Platform</strong></li>
               </ul>

               <h3 style="margin-top: 30px;"><strong>College Impact Advantage:</strong></h3>

               <ul>
                  <li><strong>International Research Presentation Experience</strong></li>
                  <li><strong>Interact with Nobel Laureates & top Scientists</strong></li>
                  <li><strong>Global Academic Networking</strong></li>
                  <li><strong>Distinctive Edge in University Admissions</strong></li>
               </ul>

               <p style="margin-top: 20px; font-weight: 500; color: #F15B43;">
                  <strong>Note: Submitting a paper is not necessary to attend the conference.</strong>
               </p>
            </div>

            <!-- Event Sponsor: Bay Area Champions (Uses free space on the right) -->
            <div class="bay-area-sponsor"
               style="position: absolute; top: 120px; right: 15px; text-align: center; width: 280px;">
               <div style="display: inline-block; text-align: center;">
                  <img src="{{ url('front/images/bayareachampions%20.jpeg') }}" alt="Bay Area Champions Logo"
                     style="max-width: 200px; height: auto; margin-bottom: 10px;">
                  <p style="margin-bottom: 10px; font-weight: 600; color: #333; font-size: 16px;">
                     Bay Area Champions - Event Sponsor
                  </p>
                  <p style="margin-top: 10px; font-weight: 600; color: #333; font-size: 16px;">
                     Nurturing Minds. Creating Champions
                  </p>
               </div>
            </div>

            <!-- Video and Research Areas Section - Side by Side -->
            <div class="container mt-4 mb-4">
               <div class="row align-items-center">
                  <!-- Video Section - Left Side -->
                  <div class="col-lg-8">
                     <div class="ircc-video text-center"
                        style="display: flex; align-items: center; justify-content: center; height: 100%; min-height: 580px;">
                        <video controls
                           style="width: 100%; max-width: 100%; border-radius: 8px; height: 100%; min-height: 530px; object-fit: cover;">
                           <source src="{{ url('uploads/videos/ircc-video.mp4') }}" type="video/mp4">
                           <source src="{{ url('uploads/videos/ircc-video.webm') }}" type="video/webm">
                           Your browser does not support the video tag.
                        </video>
                     </div>
                  </div>

                  <!-- Research Domains Section - Right Side -->
                  <div class="col-lg-4">
                     <div class="research-domains-section"
                        style="display: flex; flex-direction: column; justify-content: center; height: 100%; min-height: 400px;">
                        <div class="section-head text-center" style="margin-bottom: 0px;">
                           <div class="sub-title">RESEARCH AREAS</div>
                        </div>
                        <div class="dates-table" style="max-width: 100%; margin: 0px auto;">
                           <table>
                              <tr>
                                 <td colspan="2"
                                    style="padding: 6px 5px; font-size: 13px; color: #666; text-align: left; border-bottom: 1px solid #e0e0e0; margin-bottom: 6px;">
                                    IRCC welcomes research abstracts across diverse domains, offering a comprehensive
                                    platform for young researchers to explore and present their work. Participants can also
                                    transform their school or science fair project into a research paper and present their
                                    innovation.
                                 </td>
                              </tr>
                              <tr>
                                 <td style="padding: 2px 5px; font-size: 16px;"><span class="label"
                                       style="font-size: 15px;">Environment and Earth Science</span> <span class="date"
                                       style="font-size: 15px;">(EES)</span></td>
                              </tr>
                              <tr>
                                 <td style="padding: 2px 5px; font-size: 16px;"><span class="label"
                                       style="font-size: 15px;">Engineering and Technology</span> <span class="date"
                                       style="font-size: 15px;">(ET)</span></td>
                              </tr>
                              <tr>
                                 <td style="padding: 2px 5px; font-size: 16px;"><span class="label"
                                       style="font-size: 15px;">Life and Basic Science</span> <span class="date"
                                       style="font-size: 15px;">(LBS)</span></td>
                              </tr>
                              <tr>
                                 <td style="padding: 2px 5px; font-size: 16px;"><span class="label"
                                       style="font-size: 15px;">Social Science</span> <span class="date"
                                       style="font-size: 15px;">(SS)</span></td>
                              </tr>
                              <tr>
                                 <td style="padding: 2px 5px; font-size: 16px;"><span class="label"
                                       style="font-size: 15px;">Cultural Innovation</span> <span class="date"
                                       style="font-size: 15px;">(CI)</span></td>
                              </tr>
                              <tr>
                                 <td
                                    style="padding: 10px 5px; font-size: 16px; text-align: center; border-top: 1px solid #e0e0e0; margin-top: 6px;">
                                    <a href="{{ route('guidelines') }}"
                                       style="color: #F15B43; font-weight: 700; text-decoration: none; font-size: 18px; display: inline-block; padding: 8px 15px; transition: all 0.3s ease;">
                                       <i class="fas fa-file-alt" style="margin-right: 8px;"></i>View Guidelines
                                    </a>
                                 </td>
                              </tr>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Conference Highlights Section -->
            <div class="container mt-5 mb-5">
               <div class="section-head text-center">
                  <h2 class="section-title" style="margin-bottom: 40px;">Conference Highlights</h2>
               </div>

               <!-- Statistics Cards -->
               <div class="row mb-5">
                  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                     <div class="highlight-stat-card"
                        style="background: #fff; border-radius: 12px; padding: 30px 25px; text-align: center; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border-top: 4px solid #F15B43; height: 100%;">
                        <div style="font-size: 48px; font-weight: bold; color: #F15B43; margin-bottom: 10px;">400+</div>
                        <div style="font-size: 16px; color: #333; font-weight: 600;">Student Researchers</div>
                     </div>
                  </div>
                  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                     <div class="highlight-stat-card"
                        style="background: #fff; border-radius: 12px; padding: 30px 25px; text-align: center; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border-top: 4px solid #F15B43; height: 100%;">
                        <div style="font-size: 48px; font-weight: bold; color: #F15B43; margin-bottom: 10px;">10+</div>
                        <div style="font-size: 16px; color: #333; font-weight: 600;">Distinguished Speakers</div>
                     </div>
                  </div>
                  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                     <div class="highlight-stat-card"
                        style="background: #fff; border-radius: 12px; padding: 30px 25px; text-align: center; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border-top: 4px solid #F15B43; height: 100%;">
                        <div style="font-size: 48px; font-weight: bold; color: #F15B43; margin-bottom: 10px;">15+</div>
                        <div style="font-size: 16px; color: #333; font-weight: 600;">Scientists & Experts</div>
                     </div>
                  </div>
                  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                     <div class="highlight-stat-card"
                        style="background: #fff; border-radius: 12px; padding: 30px 25px; text-align: center; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border-top: 4px solid #F15B43; height: 100%;">
                        <div style="font-size: 48px; font-weight: bold; color: #F15B43; margin-bottom: 10px;">100+</div>
                        <div style="font-size: 16px; color: #333; font-weight: 600;">Educators & Parents</div>
                     </div>
                  </div>
               </div>

               <!-- Highlight Content Cards -->
               <div class="row mb-4">
                  <div class="col-lg-6 mb-4">
                     <div class="highlight-content-card"
                        style="background: #fff; border-radius: 12px; padding: 30px 25px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border-top: 4px solid #F15B43; height: 100%;">
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                           <i class="fas fa-trophy" style="font-size: 24px; color: #FFD700; margin-right: 12px;"></i>
                           <h4 style="color: #F15B43; font-weight: 600; margin: 0; font-size: 20px;">Nobel Laureate Keynote
                           </h4>
                        </div>
                        <p style="color: #666; font-size: 15px; line-height: 1.6; margin: 0;">
                           Students will be inspired by a prestigious keynote address from a Nobel Laureate, alongside
                           lectures by renowned scientists from diverse STEM and non-STEM disciplines.
                        </p>
                     </div>
                  </div>
                  <div class="col-lg-6 mb-4">
                     <div class="highlight-content-card"
                        style="background: #fff; border-radius: 12px; padding: 30px 25px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border-top: 4px solid #F15B43; height: 100%;">
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                           <i class="fas fa-chart-bar" style="font-size: 24px; color: #F15B43; margin-right: 12px;"></i>
                           <h4 style="color: #F15B43; font-weight: 600; margin: 0; font-size: 20px;">Student Presentations
                           </h4>
                        </div>
                        <p style="color: #666; font-size: 15px; line-height: 1.6; margin: 0;">
                           Featuring 50+ platform presentations and 100+ student presentations, where young researchers
                           showcase their original work and innovative ideas.
                        </p>
                     </div>
                  </div>
               </div>

               <!-- Additional Highlight Cards -->
               <div class="row">
                  <div class="col-lg-6 mb-4">
                     <div class="highlight-content-card"
                        style="background: #fff; border-radius: 12px; padding: 30px 25px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border-top: 4px solid #F15B43; height: 100%;">
                        <div style="display: flex; align-items: center; margin-bottom: 15px;">
                           <i class="fas fa-shield-alt" style="font-size: 24px; color: #F15B43; margin-right: 12px;"></i>
                           <h4 style="color: #F15B43; font-weight: 600; margin: 0; font-size: 20px;">Safe & Supportive
                              Environment</h4>
                        </div>
                        <p style="color: #666; font-size: 15px; line-height: 1.6; margin: 0;">
                           Carefully structured to be safe, supportive, and intellectually stimulating, empowering children
                           to be competent on an international stage.
                        </p>
                     </div>
                  </div>
                  <div class="col-lg-6 mb-4">
                     <div class="highlight-content-card"
                        style="background: #fff; border-radius: 12px; padding: 30px 25px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); border-top: 4px solid #F15B43; height: 100%;">
                        <p
                           style="color: #F15B43; font-size: 18px; font-weight: 600; margin-bottom: 12px; line-height: 1.5;">
                           Students are not only participants but contributors
                        </p>
                        <p style="color: #666; font-size: 15px; line-height: 1.6; margin: 0;">
                           IRCC celebrates their role as active knowledge creators, fostering the next generation of
                           researchers and innovators in a nurturing, world-class environment.
                        </p>
                     </div>
                  </div>
               </div>
            </div>
      </section>

      <div class="section-head text-center">
         <h2 class="section-title">Get Involved Now</h2>
      </div>
      <div class="container">
         <div class="event-inner">
            <div class="row align-items-start">
               <div class="col-lg-5">
                  <!-- Dates To Remember Table -->
                  <div class="dates-table">
                     <h3>Dates To Remember</h3>
                     <table>
                        <tr>
                           <td><span class="label">Registration opens on</span> <span class="date">February 7th,
                                 {{ $year }}</span></td>
                        </tr>
                        <tr>
                           <td><span class="label">Early bird registration deadline</span> <span class="date">March 31st,
                                 {{ $year }}</span></td>
                        </tr>
                        <tr>
                           <td>
                              <span class="label">Abstract submission deadline</span>
                              <span class="date">
                                 <del>April 15th, {{ $year }}</del>
                                 &nbsp;&rarr;&nbsp;
                                 <strong>April 30th, {{ $year }}</strong>
                              </span>
                           </td>
                        </tr>
                        <tr>
                           <td><span class="label">Regular registration starts on</span> <span class="date">April 1st,
                                 {{ $year }}</span></td>
                        </tr>
                        <tr>
                           <td><span class="label">Conference date</span> <span class="date">Saturday & Sunday, June 6-7,
                                 {{ $year }} (Two-Day Event)</span><br>
                              <span class="date">June 6th (Sat): 8:00 AM – 7:00 PM &nbsp;|&nbsp; June 7th (Sun): 8:30 AM –
                                 5:00 PM</span>
                           </td>
                        </tr>
                     </table>
                  </div>
                  <div class="home-about-left">
                     <div class="home-about-image">
                        <img src="{{url("front/images/unbound-img3.png")}}" alt="">
                     </div>
                  </div>
               </div>
               <div class="col-lg-7">
                  <article class="event-item" style="display: flex; align-items: center; height: 100%;">
                     <div class="">
                        <p>The International Research Conference for Children {{ $year }} (IRCC'{{ substr($year, -2) }}), organized by <b>MANCA,
                              STEM4Girls, and the Global Young Researchers' Academy</b> (GYRA), will take place as a
                           <b>two-day event on Saturday & Sunday, June 6-7, {{ $year }}, at the India Community Center (ICC),
                              Milpitas, California.</b>
                        <p>This event invites students from elementary to high school around the globe to participate.</p>
                        <p>The conference will feature keynotes, plenary talks, and lectures by internationally established
                           scientists, as well as talks, poster sessions, and presentations by school students.</p>
                        <p>IRCC emphasizes inclusivity, welcoming students with disabilities to present their research
                           papers. Their participation adds immense value to the conference, as their perseverance and
                           dedication highlight the boundless potential of every individual.</p>
                        <p>Students and teachers are encouraged to collaborate in moderating sessions alongside scientists,
                           fostering a supportive international community of young researchers.</p>
                        <p>IRCC serves as an international platform, bringing together student researchers and renowned
                           scientists to share their research and provides a unique opportunity of interaction between the
                           scientists of today and tomorrow.</p>
                        <p> <b>We welcome you all to this celebration of barrier-free research!</b></p>
                     </div>
                  </article>
               </div>
            </div>
         </div>
      </div>
      </section>


      {{-- <section class="home-testimonial" style="background-image: url(front/images/review.png);">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-lg-6">
                  <div class="sub-title">Abstract Download</div>
                  <h2 class="section-title">Download IRCC'26 Abstract</h2>
                  <p>Scan the QR Code to instantly access the official abstract of IRCC 2025 and explore groundbreaking
                     research and insights.</p>
                  <!-- <a href="{{route('abstract-book')}}" class="outline-round outline-round-white">More Abstract</a> -->
               </div>
               <div class="col-lg-6">
                  <div class="testimonial-inner">
                     <div class="row">

                        <div class="col-sm-12" style="text-align: center;">
                           <img src="{{url('front/images/ircc-abstract-qrcode.jpg')}}" style="max-width:65%;" alt="">
                        </div>
                     </div>

                  </div>
               </div>

            </div>
         </div>
         <div class="overlay"></div>
      </section>
      <section class="home-event" style="background-color: #f5f5f5;">
         <div class="container">
            <div class="section-head text-center">
               <div class="sub-title">Message</div>
               <h2 class="section-title">GYRA</h2>
            </div>
            <div class="testimonial-page-section pb-0">
               <div class="row">
                  <div class="col-md-12">
                     <div class="testimonial-item text-center mb-0">
                        <div class="testimonial-content">
                           <div class="testimonial-icon">
                              <i aria-hidden="true" class="icon icon-quote"></i>
                           </div>
                           <p>Global Young Researchers' Academy (GYRA) is a distinguished international non-profit
                              organization headquartered in Oregon, United States of America, committed to nurturing
                              research skills among school children and preparing them for future careers in science. The
                              primary mission of GYRA revolves around inspiring and guiding middle school students towards
                              becoming proficient researchers.</p>
                           <p>One of GYRA's hallmark initiatives is the International Research Conference for Children, a
                              pivotal event that provides young scholars with an unparalleled platform to engage directly
                              with scientists across diverse fields of study. This conference not only facilitates
                              knowledge exchange but also serves as a significant opportunity for students to showcase
                              their inherent curiosity and passion for exploration.</p>
                           <p>At GYRA, we believe in cultivating a research-oriented mindset from a young age, empowering
                              students to delve into scientific inquiry and contribute meaningfully to their communities
                              and beyond. Through our programs and conferences, we aim to instill confidence, critical
                              thinking, and a lifelong love for learning in the next generation of scientists and
                              innovators.</p>
                           <p>Join us in our mission to unlock the potential of young minds and foster a future where
                              curiosity leads to groundbreaking discoveries. Together, let's inspire and empower the
                              scientists of tomorrow through this distinctive conference serves as a special platform for
                              all participants including special students to foster research skills among school students.
                           <p>As the President of GYRA-India, I warmly invite all participants to seize this opportunity.
                           </p>
                        </div>
                        <figure class="testimonial-img">
                           <img src="{{url(" front/images/committee/drrenjujoseph.png")}}" alt="">
                        </figure>
                        <h3 class="mb-2"> Dr.Renju Joseph</h3>
                        <h5 class="mb-1">President, GYRA-India</h5>

                     </div>
                  </div>
               </div>

            </div>
         </div>
      </section> --}}
      <!-- <section class="home-event" >
            <div class="container">
               <div class="row">
                  <div class="col-lg-7">
                     <div class="home-about-left">
                        <div class="home-about-image">
                           <img src="{{url("front/images/unbound-img3.png")}}" alt="">
                        </div>

                     </div>
                  </div>
                  <div class="col-lg-5">
                     <div class="home-about-right">
                        <div class="home-about-content">
                          <div class="sub-title">ABOUT US</div> 
                           <h2 class="section-title" style="font-size: 42px !important;">International Research Conference<br>for Children {{ $year }}</h2>
                           <p style="text-align:">In collaboration with the Global Young Researchers’ Academy (GYRA), Malayalee Association of Northern California(MANCA) and STEM4Girls, an International Research Conference for Children (IRCC’26) has been organized at India Community Center
   from 6th to 7th June {{ $year }} in  Milpitas, California</p>

                           <a href="{{route('about')}}" class="button-round mt-2">Learn More</a>
                        </div>
                     </div>
                  </div>
               </div>

            </div>
         </section>-->



      <section class="home-event bg-light-grey associates">
         <div class="container">
            <div class="section-head text-center">
               <div class="sub-title">Who we are</div>
               <h2 class="section-title">In Association With</h2>
            </div>
            <div class="donate-inner">
               <div class="row">
                  <div class="col-sm-6 col-lg-4">
                     <article class="donate-content primary-bg text-center">
                        <a href="https://thinkgyra.org/" target="_blank">
                           <div class="image-section p-5 pb-4">
                              <img href="https://thinkgyra.org/" src="{{url('front/images/logo-gyra-inverse.png')}}"
                                 alt="">
                           </div>
                        </a>
                        <div class="content-section p-5 pt-2">
                           <h3 class="mt-3 mb-4">GYRA</h3>
                           <p>Global Young Researchers' Academy (GYRA) is an international non-profit organization (based
                              on Oregon, United States of America)</p>
                        </div>
                     </article>
                  </div>
                  <div class="col-sm-6 col-lg-4">
                     <article class="donate-content bg-transparent-img text-center"
                        style="background-image: url(front/images/unbound-img2.jpg);">
                        <a href="https://www.mancaonline.org/" target="_blank">
                           <div class="image-section p-5 pb-4">
                              <img href="https://www.mancaonline.org/" src="{{url('front/images/manca_no_bg.png')}}"
                                 alt="">
                           </div>
                        </a>
                        <div class="content-section p-5 pt-2">
                           <h3 class="mt-3 mb-4">MANCA</h3>
                           <p>MANCA is a non-profit organization envisions itself as a leading social and cultural
                              organization for the Malayalee community of Northern California.</p>
                           <p></p>
                        </div>
                     </article>
                  </div>
                  <div class="col-sm-6 col-lg-4">
                     <article class="donate-content secondary-bg text-center">
                        <a href="https://stem4girls.org" target="_blank" rel="noopener noreferrer">
                           <div class="image-section p-5 pb-4">
                              <img src="{{url('front/images/logo-stem4girls.png')}}" alt="STEM4Girls">
                           </div>
                        </a>
                        <div class="content-section p-5 pt-2">
                           <h3 class="mt-3 mb-4">STEM4Girls</h3>
                           <p>STEM4Girls is a non-profit organization aiming to encourage the pursuit of Science,
                              Technology, Engineering, and Math, collectively termed STEM fields.</p>
                        </div>
                     </article>


                  </div>

               </div>
            </div>
         </div>
      </section>
      @if($reviews->count())
         <section class="home-testimonial" style="background-image: url(front/images/review.png);">
            <div class="container">
               <div class="row align-items-center">
                  <div class="col-lg-6">
                     <div class="testimonial-inner testimonial-slider">
                        @if(!empty($reviews))
                           @foreach($reviews as $review)
                              <div class="testimonial-item text-center">
                                 <div class="testimonial-content">
                                    <div class="testimonial-icon testimonial-icon-review">
                                       <i aria-hidden="true" class="icon icon-quote"></i>
                                    </div>
                                    <div class="truncate">
                                       <p>{!!$review->message!!}</p>
                                    </div>
                                    <p></p>
                                    <h5>{{$review->author}}</h5>
                                 </div>
                              </div>
                           @endforeach
                        @endif

                     </div>
                  </div>
                  <div class="col-lg-6">
                     <div class="sub-title">OUR TESTIMONIALS</div>
                     <h2 class="section-title">What our happy participants say !!</h2>
                     <p>Our participants' experiences speak for themselves. From overcoming challenges to achieving personal
                        and professional growth, their stories highlight the transformative power of our program. Each
                        testimonial reflects the positive impact we’ve had on their journey and how our support helped them
                        reach their goals</p>
                     <a href="{{route('reviews')}}" class="outline-round outline-round-white">More Reviews</a>
                  </div>
               </div>
            </div>
            <div class="overlay"></div>
         </section>
      @endif

      <section class="home-event" style="display: none;">
         <div class="container">
            <div class="section-head text-center">
               <div class="sub-title">COMMITTEE</div>
               <h2 class="section-title">Behind Our Success</h2>
               @if($committee->count() == 0 && $patrons->count() == 0)
                  <h3>Will announce soon</h3>
               @endif
            </div>
            <div class="team-inner">
               @if($patrons->count())
                  <div class="row justify-content-center">
                     @foreach($patrons as $patron)
                        <div class="col-md-6">
                           <article class="team-item">
                              <figure class="team-img">
                                 <img src="{{ $patron->portraitUrl('front/images/team1.png') }}" alt="">
                              </figure>
                              <div class="team-content" style="padding-left:25px">
                                 <h4>{{$patron->name}}</h4>
                                 <span>{{$patron->designation}}</span>
                                 <p>{{$patron->organization}}</p>

                              </div>
                           </article>
                        </div>
                     @endforeach
               @endif
                  @if($committee->count())
                     <div class="row">

                        @foreach($committee as $team)
                           <div class="col-lg-6">
                              <article class="team-item">
                                 <figure class="team-img">
                                    <img src="{{ $team->portraitUrl('front/images/team1.png') }}" alt="">
                                 </figure>
                                 <div class="team-content" style="padding-left:25px">
                                    <h4>{{$team->name}}</h4>
                                    <span>{{$team->designation}}</span>
                                    <p> {{$team->organization}}</p>
                                    <div class="social-links">
                                       <ul>
                                       </ul>
                                    </div>
                                 </div>
                              </article>
                           </div>
                        @endforeach
                     </div>
                  @endif
                  <!-- <div class="row">
               <div class="col-lg-6">
                  <article class="team-item">
                     <figure class="team-img">
                        <img src="{{url("front/images/committee/ProfChandrabhasNarayana.jpg")}}" alt="">
                     </figure>
                     <div class="team-content" style="padding-left:25px">
                        <h4>Prof. Chandrabhas Narayana</h4>
                        <span>Our Patron</span>
                        <p> Director Rajiv Gandhi Centre for Biotechnology</p>
                        <div class="social-links">
                           <ul>
                           </ul>
                        </div>
                     </div>
                  </article>
               </div>
               <div class="col-lg-6">
                  <article class="team-item">
                     <figure class="team-img">
                        <img src="{{url("front/images/committee/DrPradeepkumar.jpg")}}" alt="">
                     </figure>
                     <div class="team-content" style="padding-left:25px">
                        <h4>Dr Pradeepkumar.T</h4>
                        <span>Our Patron</span>
                        <p>Vice Chancellor of KUFOS</p>
                        <div class="social-links">
                           <ul>
                           </ul>
                        </div>
                     </div>
                  </article>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-6">
                  <article class="team-item">
                     <figure class="team-img">
                        <img src="{{url("front/images/committee/drrenjujoseph.png")}}" alt="">
                     </figure>
                     <div class="team-content" style="padding-left:25px">
                        <h4>Dr. Renju Joseph</h4>
                        <span>Conference Chair</span>
                        <p>GYRA, India.</p>
                     </div>
                  </article>
               </div>
               <div class="col-lg-6">
                  <article class="team-item">
                     <figure class="team-img">
                        <img src="{{url("front/images/committee/drmathews.png")}}" alt="">
                     </figure>
                     <div class="team-content" style="padding-left:25px">
                        <h4>Dr. Mathews Irimpan</h4>
                        <span>Conference Co Chair</span>
                        <p>GYRA, USA.</p>
                     </div>
                  </article>
               </div>
               <div class="col-lg-6">
                  <article class="team-item">
                     <figure class="team-img">
                        <img src="{{url("front/images/committee/raina.png")}}" alt="">
                     </figure>
                     <div class="team-content" style="padding-left:25px">
                        <h4>Ms. Raina Raphy</h4>
                        <span>Co-organizer</span>
                        <p>STEM4Girls, USA.</p>
                     </div>
                  </article>
               </div>
               <div class="col-lg-6">
                  <article class="team-item">
                     <figure class="team-img">
                        <img src="{{url("front/images/committee/drfinosh.png")}}" alt="">
                     </figure>
                     <div class="team-content" style="padding-left:25px">
                        <h4>Dr. Finosh Thankam</h4>
                        <span>Scientific Committee Chair</span>
                        <p>GYRA and STEM4Girls, USA.</p>
                     </div>
                  </article>
               </div>
            </div> -->
                  <!-- <div class="list-more-btn text-center">
               <a href="{{route('committee')}}" class="button-round">View All Member</a>
            </div> -->
               </div>
            </div>
      </section>

      <section class="callback-inner-page" style="background-image: url(front/images/unbound-img23.jpg);">
         <div class="container">
            <div class="callback-content text-center">

               <h2 class="section-title">International Research Conference for Children (IRCC)</h2>

               <div class="button-wrap">
                  <a class="button-round" href="{{route('abstract-book')}}">View Abstract</a>
               </div>

            </div>
         </div>
         <div class="overlay"></div>
      </section>




      <!-- home callback section html start -->

      <!-- home callback section html end -->
   </main>
   <div class="modal fade" id="abstractBookModal" tabindex="-1" role="dialog" aria-labelledby="abstractBookModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close"> <span
                     aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
               <iframe src="{{url('front/pdf/abstract-book.pdf')}}" style="width:100%; height:600px;"
                  frameborder="0"></iframe>
            </div>
         </div>
      </div>
   </div>
@endsection
@section('script')
   <script>
      function makeTimer() {
         // Countdown to conference date: June 6th, {{ $year }}
         var endDate = '6 June {{ $year }} 00:00:00 GMT+01:00';

         console.log(endDate);
         var endTime = new Date(endDate);
         endTime = (Date.parse(endTime) / 1000);
         var now = new Date();
         now = (Date.parse(now) / 1000);
         var timeLeft = endTime - now;
         var days = Math.floor(timeLeft / 86400);
         var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
         var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600)) / 60);
         var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));
         if (hours < "10") { hours = "0" + hours; }
         if (minutes < "10") { minutes = "0" + minutes; }
         if (seconds < "10") { seconds = "0" + seconds; }

         if (days > 0) {
            $('.days-span').show();
            $("#days").html(days + "");
            $("#hours").html(hours + "<span>Hours</span>");
            $("#minutes").html(minutes + "<span>Minutes</span>");
            $("#seconds").html(seconds + "<span>Seconds</span>");
         }
         else {
            $('.days-span').hide();
         }

      }
      setInterval(function () { makeTimer(); }, 1000); 
   </script>

   @if(isset($speakers) && $speakers->count() > 0)
      <script>
         $(document).ready(function () {
            let currentSpeakerIndex = 0;
            const totalSpeakers = {{ $speakers->count() }};
            const $track = $('.banner-track');
            const $indicators = $('.banner-indicator');
            const $currentCounter = $('.banner-current');
            let autoScrollInterval = null;
            let isPaused = false;

            if ($track.length === 0) {
               console.error('Banner track not found');
               return;
            }

            function updateCarousel() {
               const translateX = -currentSpeakerIndex * 100;
               $track.css('transform', `translateX(${translateX}%)`);

               // Update indicators
               $indicators.removeClass('active').eq(currentSpeakerIndex).addClass('active');
               $indicators.each(function () {
                  $(this).css('background-color', $(this).hasClass('active') ? '#F15B43' : '#ccc');
               });

               // Update counter
               $currentCounter.text(currentSpeakerIndex + 1);

               // Update button states
               $('.banner-prev-btn').css('opacity', currentSpeakerIndex === 0 ? '0.5' : '1').prop('disabled', currentSpeakerIndex === 0);
               $('.banner-next-btn').css('opacity', currentSpeakerIndex === totalSpeakers - 1 ? '0.5' : '1').prop('disabled', currentSpeakerIndex === totalSpeakers - 1);
            }

            function startAutoScroll() {
               if (autoScrollInterval) {
                  clearInterval(autoScrollInterval);
               }
               autoScrollInterval = setInterval(function () {
                  if (!isPaused && $track.length > 0) {
                     if (currentSpeakerIndex < totalSpeakers - 1) {
                        currentSpeakerIndex++;
                     } else {
                        currentSpeakerIndex = 0; // Loop back to first
                     }
                     updateCarousel();
                  }
               }, 3000); // Auto-scroll every 3 seconds
            }

            function pauseAutoScroll() {
               isPaused = true;
               setTimeout(function () {
                  isPaused = false;
               }, 10000); // Resume after 10 seconds of inactivity
            }

            // Next button
            $('.banner-next-btn').on('click', function () {
               pauseAutoScroll();
               if (currentSpeakerIndex < totalSpeakers - 1) {
                  currentSpeakerIndex++;
                  updateCarousel();
               }
            });

            // Previous button
            $('.banner-prev-btn').on('click', function () {
               pauseAutoScroll();
               if (currentSpeakerIndex > 0) {
                  currentSpeakerIndex--;
                  updateCarousel();
               }
            });

            // Indicator clicks
            $indicators.on('click', function () {
               pauseAutoScroll();
               currentSpeakerIndex = parseInt($(this).data('index'));
               updateCarousel();
            });

            // Pause on hover
            $('.banner-carousel-wrapper').hover(
               function () {
                  isPaused = true;
               },
               function () {
                  isPaused = false;
               }
            );

            // Initialize
            updateCarousel();
            startAutoScroll();
         });
      </script>
   @endif

@endsection