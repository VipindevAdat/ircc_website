@extends('layouts.app')
@section('head')
<style>

</style>
@endsection
@section('content')
<main class="site-main">
            <!-- Inner Banner html start-->
   <section class="inner-banner-wrap">
      <div class="inner-baner-container" style="background-image: url(front/images/inner-banner-img-1.jpg);">
         <div class="container">
            <div class="inner-banner-content">
               <h1 class="inner-title">How to Reach Venue</h1>
            </div>
         </div>
      </div>
   </section>

   <!-- Inner Banner html end-->
   <div class="career-detail-section">
      <div class="career-detail-inner">
         <div class="container">
         <div class="section-head text-center ">
            <!-- <h2 class="section-title">How to Reach Venue</h2> -->
				<h2 class="section-title">For outstation participants</h2>
                <h3 class="section-title">Venue: India Community Center (ICC),
                                        525 Los Coches St, Milpitas, CA 95035, USA</h3>
            <!-- <div class="sub-title mb-4">For outstation participants</div> -->
			</div>
            <div class="row">
               <div class="col-lg-10 primary right-sidebar">
               <div class="event-detail-container">
               <div class="row">  
               <div class="col-lg-6" > 
               <h3>Transit/</h3>
               </div>
               <div class="col-lg-6" style="text-align: right;">
          
               </div>
               </div>
               <div class="card-body">
                    <ul>
                        <li><span style="font-weight: 500;">Milpitas BART Station → India Community Center
                                    Milpitas BART to ICC (525 Los Coches St) via S Milpitas Blvd and Los Coches St – approx. 10 minutes (≈3 km / 2 mi) by taxi/ride share.</li>
                        <li><span style="font-weight: 500;">San Jose Diridon Station → India Community Center
                                                        San Jose Diridon (Caltrain/Amtrak) to ICC via I-880 N – approx. 18–20 minutes (≈8–10 mi / 13–16 km) by car.</li>
                    </ul>
                    </div>
                    <h3>Airports </h3> 
                    <div class="card-body">
                        
                        <ul>
                        <li><span style="font-weight: 500;">San Jose International Airport (SJC) → India Community Center
                                                            Via CA-87 N and I-880 N – approx. 15–20 minutes (≈9 mi / 14 km) by car/ride share.</li>
                        <li><span style="font-weight: 500;">Oakland International Airport (OAK) → India Community Center
Via I-880 S – approx. 35–40 minutes (≈30 mi / 48 km) by car/ride share. </li>
<li><span style="font-weight: 500;">San Francisco International Airport (SFO) → India Community Center
Via US-101 S + CA-237 E or I-280 S + I-880 S – approx. 40–45 minutes (≈34–35 mi / 55 km) by car/ride share.</li>
                            <!-- <li><span style="font-weight: 500;">AIWA RESIDENCY: </span> Mr. Benny-9446718594,871494947 <span> - 35 rooms and Two Dormitories </span></li> -->
                        </ul>
                    </div>
                    
                  </div>
               </div>
               <div class="col-lg-4 secondary">
             
              
            </div>
         
         </div>
      </div>
     
   </div>
   @if(optional($settings->where('code','registration_status')->first())->value==1) 
   <section class="home-callback" style="background-image: url({{ url("front/images/unbound-img2.jpg")}}">
               <div class="container">
                  <div class="row align-items-center justify-content-between">
                     <div class="col-lg-8">
                        <div class="callback-content">
                           <h2 class="section-title">Application Letter for Admission</h2>
                           <p>Please be aware that IRCC participation is limited to 1000 participants.</p>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="button-wrap">
                           <!-- <a href="{{route('register')}}" class="button-round">Register Now</a> -->
                        </div>
                     </div>
                  </div>
               </div>
               <div class="overlay"></div>
            </section>
   @endif
</main>

@endsection
@section('script')
<script>

</script>
@endsection