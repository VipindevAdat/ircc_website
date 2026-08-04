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

.sceintific_text {
   padding: 20px 20px;
    text-align: center;
    min-height: 160px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.volunteer_wrapper:hover {
    box-shadow: rgb(0 0 0 / 14%) 8px 20px 22px;
    transform: translateY(-5px);
}

.committee-members {
    margin-bottom: 40px;
}

.committee-members .col-lg-3 {
    margin-bottom: 30px;
}

.team-img {
    width: 100%;
    height: 300px;
    overflow: hidden;
    position: relative;
    background: #f5f5f5;
    border-radius: 10px;
}

.team-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

@media (max-width: 991px) {
    .volunteer_img {
        height: 250px;
    }
    
    .team-img {
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
    
    .team-img {
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
		<div class="inner-baner-container" style="background-image: url('{{ asset('front/images/inner-banner-img-1.jpg') }}');">
			<div class="container">
				<div class="inner-banner-content">
					<h1 class="inner-title">Committee</h1>
				</div>
			</div>
		</div>
	</section>
	<!-- Inner Banner html end-->
	<!-- Team html start -->
	<div class="team-page-section">
		<div class="container">
			@if((!$patrons->count()) && (!$committees->count()))
			<div class="section-head text-center mt-4">
				<h2 class="section-title">Will announce soon..</h2>
			</div>
			@endif
			@if($patrons->count())
			<div class="row justify-content-center">
				@foreach($patrons as $patron)
				<div class="col-md-6">
					<article class="team-item">
						<figure class="team-img">
							<img src="{{ $patron->portraitUrl('front/images/user-img.png', true) }}" alt="{{ $patron->name }}" onerror="this.onerror=null;this.src='{{ asset('front/images/user-img.png') }}'">
						</figure>
						<div class="team-content" style="padding-left:25px">
							<h4>{{$patron->name}}</h4>
							<span>{{$patron->designation}}</span>
							<p>{{$patron->organization}}</p>
							
						</div>
					</article>
				</div>
				@endforeach
			</div>
			@endif
			@foreach($committeeLeadershipBlocks as $block)
			<div class="section-head text-center mt-4">
				<h3 class="section-title">{{ $block['title'] }}</h3>
			</div>
			<div class="row committee-members justify-content-center">
				@foreach($block['members'] as $member)
				<div class="col-lg-3 col-md-4 col-sm-6">
					<div class="volunteer_wrapper">
						<div class="volunteer_img">
							<img src="{{ $member['img'] }}" alt="{{ $member['name'] }}" onerror="this.onerror=null;this.src='{{ asset('front/images/user-img.png') }}'">
						</div>
						<div class="volunteer_text">
							<h4>{{ $member['name'] }}</h4>
							<span>{{ $member['designation'] }}</span>
							@if(!empty($member['organization']))
							<p>{{ $member['organization'] }}</p>
							@endif
						</div>
					</div>
				</div>
				@endforeach
			</div>
			@endforeach
			@if($committees->count())
			@foreach($committees as $committee)
			@if($committee->first())
			@php
				$categoryName = optional($committee->first()->category)->name;
				$centeredCategories = ['Scientific Steering Committee Chairs', 'Scientific Steering Committee', 'Student Ambassador'];
				$categoryLower = mb_strtolower(trim((string) $categoryName));
				$centered = collect($centeredCategories)->contains(fn ($t) => mb_strtolower($t) === $categoryLower);
				$rowClasses = $centered
					? 'row committee-members justify-content-center'
					: 'row committee-members';
			@endphp
			<div class="section-head text-center mt-4">
				<h3 class="section-title">{{ $categoryName }}</h3>
			</div>
			@endif
			@if($committee->count())
			<div class="{{ $rowClasses }}">
			@foreach($committee as $team)
				<div class="col-lg-3">
					<div class="volunteer_wrapper">
						<div class="volunteer_img">
							<img src="{{ $team->portraitUrl('front/images/user-img.png', true) }}" alt="{{ $team->name }}" onerror="this.onerror=null;this.src='{{ asset('front/images/user-img.png') }}'">
						</div>
						<div class="volunteer_text">
							<h4>{{$team->name}}</h4>
							<span>{{$team->designation}}</span>
							<p>{{$team->organization}}</p>
						</div>
					</div>
				</div>
			@endforeach
			</div>
			@endif
			@endforeach
			@endif

	</div>

</main>
@endsection