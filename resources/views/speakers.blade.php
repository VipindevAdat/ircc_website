@extends('layouts.app')
@section('head')
<style>
.volunteer_wrapper{
   z-index: 9;
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 8px 10px 24px rgba(0, 0, 0, 0.1);
    transition: var(--transition);
    margin-bottom: 30px;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.volunteer_img {
    width: 100%;
    height: 280px;
    overflow: hidden;
    position: relative;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.volunteer_img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.3s ease;
}

.volunteer_wrapper:hover .volunteer_img img {
    transform: scale(1.05);
}

.volunteer_text {
   padding: 20px 20px;
    text-align: center;
    min-height: 170px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    flex-grow: 1;
}

.volunteer_text h4 {
    margin: 0 0 10px 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
    line-height: 1.3;
}

.volunteer_text span {
    color: #787878;
    display: block;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 8px;
}

.volunteer_text p {
    color: #666;
    font-size: 13px;
    line-height: 1.4;
    margin: 0;
}

.volunteer_wrapper:hover {
    box-shadow: rgb(0 0 0 / 14%) 8px 20px 22px;
    transform: translateY(-5px);
}

.committee-members {
    margin-bottom: 40px;
    dashboard:695 https://ircc.world/accounts/dashboard
dashboard:696 /accounts/dashboard
givebutter.com/elements/api/v1/324164/elements/type/bubble:1  Failed to load resource: the server responded with a status of 404 ()
/cdn-cgi/challenge-platform/h/g/scripts/jsd/9914b343/main.js:1  Failed to load resource: the server responded with a status of 404 ()
VM67:32 Payment mode changed to: Online Transaction
VM67:55 Widget element created and added to DOM
VM67:59 Givebutter script is loaded, reinitializing...
latest.js:1 Givebutter() Unknown command: reinitialize Arguments(1)
d.error @ latest.js:1
VM67:63 Givebutter reinitialize called
dashboard:839 Widget element created after AJAX load
latest.js:1 Givebutter() Unknown command: reinitialize Arguments(1)
d.error @ latest.js:1
dashboard:845 Givebutter reinitialized after AJAX
VM67:32 Payment mode changed to: Online Transaction
VM67:55 Widget element created and added to DOM
VM67:59 Givebutter script is loaded, reinitializing...
latest.js:1 Givebutter() Unknown command: reinitialize Arguments(1)
d.error @ latest.js:1
VM67:63 Givebutter reinitialize called
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: flex-start;
}

.committee-members .col-lg-3 {
    margin-bottom: 30px;
}

/* Speaker Panel Styling */
.speaker-panel {
    margin-bottom: 60px;
}

.speaker-panel-title {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 3px solid #f15b43;
}

.speaker-panel-title h2 {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.speaker-panel-title .subtitle {
    font-size: 16px;
    color: #666;
    margin-top: 8px;
}

@media (max-width: 991px) {
    .volunteer_img {
        height: 250px;
    }
    
    .volunteer_text {
        min-height: 150px;
        padding: 15px;
    }
    
    .volunteer_text h4 {
        font-size: 16px;
    }
    
    .volunteer_text span {
        font-size: 13px;
    }
    
    .volunteer_text p {
        font-size: 12px;
    }
}

@media (max-width: 767px) {
    .volunteer_img {
        height: 220px;
    }
    
    .committee-members .col-lg-3 {
        margin-bottom: 25px;
    }
}
</style>
@endsection
@section('content')
<main id="content" class="site-main bg-light-grey">
	<!-- Inner Banner html start-->
	<section class="inner-banner-wrap">
		<div class="inner-baner-container" style="background-image: url(front/images/inner-banner-img-1.jpg);">
			<div class="container">
				<div class="inner-banner-content">
					<h1 class="inner-title">Speakers</h1>
				</div>
			</div>
		</div>
	</section>
	<!-- Inner Banner html end-->
	<!-- Team html start -->
	<div class="team-page-section">
		<div class="container">
			@php
				$hasDistinguished = isset($distinguishedSpeakers) && $distinguishedSpeakers->count() > 0;
				$hasKeynote = isset($keynoteSpeakers) && $keynoteSpeakers->count() > 0;
				$hasAnySpeakers = $hasDistinguished || $hasKeynote;
			@endphp
			
			@if(!$hasAnySpeakers)
			<div class="section-head text-center mt-4">
				<h2 class="section-title">Will announce soon..</h2>
			</div>
			@endif
			
			@if($hasAnySpeakers)
				<!-- Distinguished Speakers Panel -->
				@if($hasDistinguished)
				<div class="speaker-panel">
					<div class="speaker-panel-title">
						<h2>Distinguished Keynote Speakers</h2>
						<p class="subtitle">Renowned experts and thought leaders</p>
					</div>
					<div class="row committee-members">
						@foreach($distinguishedSpeakers as $speaker)
							@include('partials.speaker-card', ['speaker' => $speaker])
						@endforeach
					</div>
				</div>
				@endif
				
				<!-- Keynote Speakers Panel -->
				@if($hasKeynote)
				<div class="speaker-panel">
					<div class="speaker-panel-title">
						<h2>Keynote Speakers</h2>
						<p class="subtitle">Expert speakers sharing their insights</p>
					</div>
					<div class="row committee-members">
						@foreach($keynoteSpeakers as $speaker)
							@include('partials.speaker-card', ['speaker' => $speaker])
						@endforeach
					</div>
				</div>
				@endif
			@endif
		</div>
	</div>
	<!-- Team html end -->
</main>
@endsection