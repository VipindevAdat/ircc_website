@extends('layouts.app')
@section('head')
<style>
   .event-content .social-links li a {
      border: 1px solid #F15B43;
      -webkit-border-radius: 5px;
      -moz-border-radius: 5px;
      -ms-border-radius: 5px;
      border-radius: 5px;
      color: #F15B43;
      display: block;
      font-size: 14px;
      width: 35px;
      height: 35px;
      line-height: 35px;
      text-align: center;
      -webkit-transition: all 0.3s;
      -moz-transition: all 0.3s;
      -ms-transition: all 0.3s;
      -o-transition: all 0.3s;
      transition: all 0.3s;
   }
   .event-content .social-links li a:hover, .event-content .social-links li a:focus {
      background-color: #CB4B36;
      color: #ffffff;
      border-color: #CB4B36;
   }
   .client-section .client-item figure {
      display: flex;
      justify-content: center;
   }
   .client-section .client-item img {
      max-height: 100px !important;
   }
   .volunteer_wrapper{
   z-index: 9;
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 8px 10px 24px rgba(0, 0, 0, 0.1);
    transition: var(--transition);
    margin-bottom: 30px;
}

.volunteer_text {
   padding: 20px 20px;
    text-align: center;
    /* height: 160px; */
}
.sceintific_text {
   padding: 20px 20px;
    text-align: center;
    height: 160px;
}
   .volunteer_wrapper:hover {
    box-shadow: rgb(0 0 0 / 14%) 8px 20px 22px;
}
@media (min-width:1025px) {

.volunteer_wrapper .volunteer_text span {
    color: #787878;
    display: block;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.1;
    margin-bottom: 15px;
}

.committee-members h4{
    /* font-size: 20px; */
	font-size: 18px;
}
}

/* Apply card border style to about page sections */
.about-section .section-head,
.home-event .section-head {
   background: #fff;
   border-radius: 12px;
   padding: 30px 25px;
   box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
   border-top: 4px solid #F15B43;
   margin-bottom: 30px;
}

.home-event .testimonial-item {
   background: #fff;
   border-radius: 12px;
   padding: 30px 25px;
   box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
   border-top: 4px solid #F15B43;
}

.home-event .event-item:not(.event-item-bg) {
   background: #fff;
   border-radius: 12px;
   padding: 30px 25px;
   box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
   border-top: 4px solid #F15B43;
}

/* Remove space after Conference Convener's Message section */
section.home-event.about-page-section {
   padding-bottom: 0 !important;
   margin-bottom: 0 !important;
}

section.home-event.about-page-section .row {
   margin-bottom: 0 !important;
}

/* Convener vs GYRA President: swapped section bands (explicit; theme grey is easy to miss vs white on some screens) */
section.home-event.about-message-convener {
   background-color: #E8EEF6 !important;
}
section.home-event.about-message-president {
   background-color: #ffffff !important;
}

/* Convener message: same visual language as GYRA president card (orange, overlapping portrait) */
section.about-message-convener .testimonial-page-section {
   padding-top: 3.5rem;
}
section.about-message-convener .testimonial-img {
   width: 100px;
   height: 100px;
   margin-top: -50px !important;
   margin-bottom: 1.25rem;
   border: 3px solid #ffffff;
   box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}
section.about-message-convener .testimonial-img img {
   width: 100%;
   height: 100%;
   object-fit: cover;
   display: block;
}
section.about-message-convener .testimonial-content {
   padding: 2.75rem 2rem 2.5rem;
   border-radius: 14px;
}
@media (max-width: 575px) {
   section.about-message-convener .testimonial-content {
      padding: 2.25rem 1.25rem 2rem;
   }
}

/* Co-Chair message: same layout as GYRA president (overlapping circular portrait + headings); keep dark card */
section.about-message-cochair .testimonial-page-section {
   padding-top: 3.5rem;
}
section.about-message-cochair .testimonial-item .testimonial-content {
   background-color: #151515 !important;
   padding: 2.75rem 2rem 2.5rem;
   border-radius: 14px;
}
section.about-message-cochair .testimonial-img {
   width: 100px;
   height: 100px;
   margin-top: -50px !important;
   margin-bottom: 1.25rem;
   border: 3px solid #ffffff;
   box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}
section.about-message-cochair .testimonial-img img {
   width: 100%;
   height: 100%;
   object-fit: cover;
   display: block;
}
@media (max-width: 575px) {
   section.about-message-cochair .testimonial-item .testimonial-content {
      padding: 2.25rem 1.25rem 2rem;
   }
}
section.about-message-cochair blockquote.about-cochair-quote {
   margin: 1.25rem 0;
   padding: 15px 20px;
   border-left: 4px solid #F15B43;
   background: rgba(255, 255, 255, 0.1);
   text-align: center;
}
section.about-message-cochair blockquote.about-cochair-quote p {
   margin: 0;
   font-style: italic;
}

.single-content-wrap {
   background: #fff;
   border-radius: 12px;
   padding: 30px 25px;
   box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
   border-top: 4px solid #F15B43;
   margin-bottom: 30px;
}

@media (max-width: 768px) {
   .about-section .section-head,
   .home-event .section-head,
   .home-event .testimonial-item,
   .home-event .event-item:not(.event-item-bg),
   .single-content-wrap {
      padding: 20px 15px;
      border-top: 4px solid #F15B43 !important;
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
					<h1 class="inner-title">About us</h1>
				</div>
			</div>
		</div>
	</section>
	<!-- Inner Banner html end-->
	<!-- About page html start -->
	<section class="about-section about-page-section" style="padding-bottom: 100px;">
		<div class="container">
			<div class="section-head">
				<div class="row align-items-end">
					<div class="col-lg-6">
						<div class="sub-title">Who We Are</div>
						<h2 style="font-size: 44px;"> International Research Conference<br>for Children 2026</h2>
					</div>
					<div class="col-lg-6">
						<div class="section-disc">
							<!-- <p style="text-align: justify;">In collaboration with the Global Young Researchers’ Academy (GYRA), along with Stem4Girls is joining hands with MANCA in organising the International Research Conference for Children 2026 (IRCC'26) atIndia Community Center (ICC), Milpitas,California, USA. </p> -->
                     <p>The Global Young Researchers' Academy (GYRA) along with Stem4Girls is joining hands with MANCA in organising the International Research Conference for Children 2026 (IRCC'26) at India Community Center (ICC), Milpitas, California, USA. </p>
							<p>The conference serves as an international platform, bringing together student  researchers at the school level, teachers, parents, administrators and wel wishers worldwide.</p>
						</div>
					</div>
				</div>
			</div>
         <div class="row">
            <div class="donate-inner">
               <div class="row">
                  <div class="col-sm-6 col-lg-4">
                     <article class="donate-content secondary-bg" style="height:580px;">
                        <h3><i aria-hidden="true" class="icon icon-user1"></i>Vision</h3>
                        <p>To cultivate a research-friendly environment for the school students to explore the interests, engage in meaningful research, and make valuable contributions to their academic disciplines.<br> The conference envisions providing a platform where students can collaborate, learn from the peers, and develop a passion for lifelong learning and discovery.</p>
                        <!-- <a href="volunteer.html" class="button-round">Join Us Now</a> -->
                     </article>
                  </div>
                  <div class="col-sm-6 col-lg-4">
                     <article class="donate-content primary-bg" style="height:580px;">
                        <h3><i aria-hidden="true" class="icon icon-save-money"></i>Mission</h3>
                        <p>Creating a platform for the school students to showcase their research work, exchange ideas, and foster a culture of academic curiosity and innovation.</p>
                        <!-- <a href="donate.html" class="outline-round outline-round-white">Donate Now</a> -->
                     </article>
                  </div>
                  <div class="col-sm-6 col-lg-4">
                     <article class="donate-content bg-transparent-img" style="height:580px;background-image: url({{ url("front/images/unbound-img2.jpg")}};">
                     <h3><i aria-hidden="true" class="icon icon-hand_shake"></i>Objective</h3>
                     <p>An international platform for young researchers to showcase their findings.<br>A platform for young researchers to interact with scientists across the globe.<br>An arena for networking and promote research interest among children and encourage them to pursue scientific endeavors.<br>Promote inclusion and diversity by encouraging participation from children of all backgrounds and abilities. 
                       </p>
                     <!-- <a href="about.html" class="button-round">Learn More</a> -->
                     </article>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- <section class="home-event bg-light-grey">
      <div class="container">
         <div class="section-head text-center">
            <div class="sub-title">COMMITTEE</div>
            <h2 class="section-title">Patron</h2>
         </div>
         <div class="row">
				<div class="col-md-6">
					<article class="team-item">
						<figure class="team-img">
							<img src="{{url("front/images/committee/ProfChandrabhasNarayana.jpg")}}" alt="">
						</figure>
						<div class="team-content" style="padding-left:25px">
							<h4>Prof. Chandrabhas Narayana</h4>
							<span>Our Patron</span>
							<p>Director Rajiv Gandhi Centre for Biotechnology</p>
							
						</div>
					</article>
				</div>
				<div class="col-md-6">
					<article class="team-item">
						<figure class="team-img">
							<img src="{{url("front/images/committee/DrPradeepkumar.jpg")}}" alt="">
						</figure>
						<div class="team-content" style="padding-left:25px">
							<h4>Dr Pradeepkumar.T</h4>
							<span>Our Patron</span>
							<p>Vice Chancellor of KUFOS</p>
						</div>
					</article>
				</div>
            
			</div>
       
      </div>
   </section> -->
   <section class="home-event about-page-section about-message-convener">
      <div class="container">
         <div class="section-head text-center">
            <div class="sub-title">Message</div>
            <h2 class="section-title">Conference Convener's Message</h2>
         </div>
         <div class="testimonial-page-section pb-0 pt-5">
            <div class="row gx-5">
               <div class="col-md-12">
                  <div class="testimonial-item text-center pb-0 mb-0">
                     <div class="testimonial-content">
                        <figure class="testimonial-img">
                           <img src="{{ $convenerPortraitUrl }}" alt="Devi Gireesh, Convener" width="100" height="100" loading="eager" decoding="async">
                        </figure>
                        <h3>Devi Gireesh</h3>
                        <h5 class="mb-1">Convener</h5>
                        <h5 class="mb-5">International Research Conference for Children (IRCC)</h5>
                        <h5 class="mb-3">Welcome to IRCC'26</h5>
                        <p class="mb-3">I am excited to welcome you all to the International Research Conference for Children-2026 (IRCC-26) at India Community Center Milpitas, CA, USA. The partnership of STEM4Girls and MANCA with GYRA in organizing IRCC-26 has added extra momentum making this event a remarkable accomplishment, first time in the United States. The collaboration of STEM4Girls and MANCA with GYRA in organizing IRCC-26 has been instrumental in launching IRCC in the United States, after three successful editions.</p>
                        <p class="mb-3">IRCC emerged as an international platform that brings together school-level student researchers, scientists, educators, parents, administrators, and stakeholders from around the world. Importantly, the conference features a distinguished keynote address by a Nobel laureate, keynote lectures by renowned scientists, plenary sessions, and featured talks by internationally established researchers.</p>
                        <p class="mb-3">IRCC is a multidisciplinary platform that unfastens disciplinary barriers, enabling free exchange of ideas across all areas of research. Simply, no barriers to your research. IRCC provides student researchers with immense opportunities to showcase their research findings and innovative ideas through platform presentations and posters along with competing for various prestigious awards.</p>
                        <p class="mb-3">Importantly, IRCC welcomes participation from students at the elementary, middle school, and high school levels. Also, student researchers gain invaluable opportunities to interact with reputed scientists and to be motivated by a vibrant, collaborative peer network.</p>
                        <p class="mb-3">Aligning with IRCC mission, this two-day international conference creates a unique forum with more than 500 student researchers and 10 internationally reputed speakers. I, being the convener of IRCC, find this opportunity to cordially welcome all the next-gen scientists to explore with keen enthusiasm and deep scientific-temper to witness advanced scientific knowledge from global experts and to create collaborative network with your peers.</p>
                        <p class="mb-0">I am delighted to welcome you all to IRCC.</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <section class="home-event bg-light-grey about-message-cochair">
      <div class="container">
         <div class="section-head text-center">
            <div class="sub-title">Message</div>
            <h2 class="section-title">Conference Co-Chair's Message</h2>
         </div>
         <div class="testimonial-page-section pb-0 pt-5">
            <div class="row gx-5">
               <div class="col-md-12">
                  <div class="testimonial-item text-center pb-0 mb-0">
                     <div class="testimonial-content">
                        <figure class="testimonial-img">
                           <img src="{{ $coChairPortraitUrl }}" alt="Padmapriya Palottu, Conference Co-Chair" width="100" height="100" loading="lazy" decoding="async">
                        </figure>
                        <h3>Padmapriya Palottu</h3>
                        <h5 class="mb-1">Co-Chair</h5>
                        <h5 class="mb-5">International Research Conference for Children (IRCC)</h5>
                        <h5 class="mb-3">Dear Budding Researchers,</h5>
                        <p class="mb-3">This conference aims to celebrate your dedication to research and foster a spirit of curiosity and innovation among your young minds. I am optimistic that your presence and presentations will add multiple dimensions to this premier event. This is a wonderful opportunity to present your research projects, share your findings, and engage in enlightening discussions with your peers and reputed global researchers. The conference program includes oral presentations, poster sessions, interactive workshops, panel discussions, and inspiring keynote speeches delivered by renowned experts in various disciplines.</p>
                        <p class="mb-3">I am sure that you will gain invaluable insights, tremendous expertise and experience, advanced knowledge, and feedback from established international researchers. This conference will further enhance your understanding of the research process and ignite a lifelong passion for exploration and discovery.</p>
                        <p class="mb-3">I would like to extend my sincere gratitude to our esteemed faculties, mentors, and school administration for their continuous support in nurturing your research endeavors. Their commitment to providing you with a conducive learning environment sets the stage for your success.</p>
                        <blockquote class="about-cochair-quote">
                           <p class="mb-0">I would like to emphasize that this conference is not merely a one-time event but rather the beginning of a journey.</p>
                        </blockquote>
                        <p class="mb-3">I strongly encourage you to keep pursuing your research interests and utilize the networks to build collaborations and grow as future researchers.</p>
                        <p class="mb-3">Finally, I want to express my heartfelt appreciation to you, the student researchers, for your enthusiasm, hard work, and dedication to advancing knowledge and making a positive impact. Your contributions are a testament to your potential and the bright future you hold.</p>
                        <p class="mb-0">I eagerly look forward to witnessing your presentations, hearing your insights, and cheering you on as you embark on this exciting academic journey at IRCC'26.</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <section class="home-event about-page-section about-message-president">
      <div class="container">
         <div class="section-head text-center">
            <div class="sub-title">Message</div>
            <h2 class="section-title">GYRA Global President's message</h2>
         </div>
         <div class="testimonial-page-section pb-0 pt-5">
            <div class="row gx-5">
               <div class="col-md-12">
                  <div class="testimonial-item text-center pb-0 mb-0">
                     <div class="testimonial-content">
                        <figure class="testimonial-img" style="margin-top: -63px !important;">
                           <img src="{{url("front/images/committee/drmathews.png")}}" alt="">
                        </figure>
                        <h3 >Dr. Irimpan Mathews </h3>
                        <h5 class="mb-1">Global President</h5>
                        <h5 class="mb-5">Global Young Researchers’ Academy (GYRA), Oregon, USA.</h5>

                        <h5 style="margin-bottom:20px;">Greetings from the GYRA Global and IRCC organizing team!</h5>
                        <p>I am deeply privileged and honored to welcome you all to the International Research Conference for Children 2026 (IRCC'26) on behalf of the organizing committee. The partnership of GYRA with STEM4girls, in organizing IRCC'26 has added extra momentum making this event a remarkable accomplishment. Also, I appreciate the invaluable support and efforts by the MANCA team ,California  for the success of IRCC'26.</p>
                        <p>IRCC serves as an international platform that brings together the children who are passionate about research at the school level. It provides an opportunity for students to showcase their research findings and engage in meaningful discussions with experts in the field. We are proud to offer a diverse program, which includes specific keynote, plenary, and featured lectures by internationally established scientists. In addition, the presentations by the school students give immense opportunities to share their research with a wider audience. Importantly, IRCC provides an ideal scientific platform for the children scientists to interact with reputed scientists and to establish collaborative network among their peer groups.</p>
                        <p>Aligning with GYRA mission, this two day international conference creates a unique forum for the expansion of knowledge and experience.<h5> The conference comprises of 5 Keynote Presentations, 12 Plenary Talks, 3 Featured Talks and more than 100 Student Presentations and 2 Panel Discussions. The keynote and plenary talks would be accessible to all delegates at Venue 1.</h5> </p>
                        <p>I, being the Global President of GYRA, find this opportunity to cordially welcome all the next-gen scientists to explore with keen enthusiasm and deep scientific-temper to witness advanced scientific knowledge from global experts and to create collaborative network with your peers. </p>
                        <p>Looking forward to seeing you at IRCC'26. </p>
                     </div>
                  </div>
               </div>
            </div>

         </div>
      </div>
   </section>
   <section class="home-event bg-light-grey">
      <div class="container">
         <div class="section-head text-center">
            <div class="sub-title">Message</div>
            <h2 class="section-title">Grand Sponsor Message</h2>
         </div>
         <div class="testimonial-page-section pb-0 pt-5">
            <div class="row gx-5">
               <div class="col-md-12">
                  <div class="testimonial-item text-center pb-0 mb-0">
                     <div class="testimonial-content">
                        <!--<figure class="testimonial-img" style="margin-top: -63px !important;">
                           <img src="{{ asset('front/images/lifology-logo.png') }}" alt="Lifology Logo" style="max-width: 200px; height: auto;"> 
                        </figure>-->
                        <h3>Lifology</h3>
                        <p>Lifology is a Life Design platform that helps high-school students discover the right career, choose the best courses, and find colleges where they'll thrive. Every student's journey is unique, and Lifology's strength lies in personalized mentorship—experienced educators guide students through 1:1 sessions, research-based assessments, and structured planning to help them gain clarity and direction.</p>
                        <p>Technology enhances—but doesn't replace—this guidance, providing insights that make mentoring more precise. Lifology also values experiences like the International Research Conference for Children (IRCC), which foster curiosity, critical thinking, and academic confidence.</p>
                        <p>By combining compassionate mentorship, structured planning, and smart technology, Lifology helps students turn potential into purposeful progress with confidence and conviction.</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <section class="home-event bg-light-grey">
      <div class="container">
         <div class="section-head text-center">
            <div class="sub-title">Who We Are</div>
            <h2 class="section-title">In Association With</h2>
         </div>
         
         <hr class="hr bg-secondary" />
         <div class="event-inner">
            <div class="row align-items-center">
               <div class="col-lg-4">
                  <article class="event-item event-item-bg" style="background-image: url(front/images/unbound-img5.jpg);min-height: 350px;">
                     <div class="event-content w-100 text-center m-auto">
                        <img src="{{url('front/images/logo-gyra.png')}}" alt="" style="height: 150px;">
                        <div class="social-links mt-5">
                           <ul>
                              @if(!empty($socialLinks['facebook']))
                              <li>
                                 <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
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
                           </ul>
                        </div>
                     </div>
                     <div class="overlay"></div>
                  </article>
               </div>
               <div class="col-lg-8">
                  <article class="event-item">
                     <div class="event-content w-100">
                        <h3>GYRA</h3>
                        <div class="event-meta">
                           <span class="time-meta">
                              <i aria-hidden="true" class="icon icon-globe"></i>
                              <a href="https://thinkgyra.org/" target="_blank" >www.thinkgyra.org</a>
                           </span>
                           <span>
                              <i aria-hidden="true" class="icon icon-map-marker1"></i>
                              United States America & India
                           </span>
                        </div>
                        <p>Global Young Researchers' Academy (GYRA) is an international non-profit organization based in Oregon, United States of America, with a mission to foster research aptitude in school children, paving the way to create the next generation of researchers. GYRA envisions addressing the increasing global demand for committed researchers by initiating and igniting middle school students to engage in research within their niche, following advanced-level standard scientific research methodology. GYRA provides opportunities to collaborate, facilitate, and offers a new and innovative model of research-oriented education and training in the 21st century. Visit our website at thinkgyra.org.</p>
                     </div>
                  </article>
               </div>
            </div>
         </div>
         <hr class="hr bg-secondary" />
         <div class="event-inner">
            <div class="row align-items-center">
               <div class="col-lg-4">
                  <article class="event-item event-item-bg" style="background-image: url(front/images/unbound-img5.jpg);min-height: 350px;">
                     <div class="event-content w-100 text-center m-auto">
                        <img src="{{url('front/images/manca_no_bg.png')}}" alt="" style="height: 150px;object-fit: contain;">
                        <div class="social-links mt-5">
                           <ul>
                              @if(!empty($socialLinks['facebook']))
                              <li>
                                 <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
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
                           </ul>
                        </div>
                     </div>
                     <div class="overlay"></div>
                  </article>
               </div>
               <div class="col-lg-8">
                  <article class="event-item">
                     <div class="event-content w-100">
                        <h3>MANCA</h3>
                        <div class="event-meta">
                           <span class="time-meta">
                              <i aria-hidden="true" class="icon icon-globe"></i>
                              <a href="https://mancaonline.org/" target="_blank" >www.mancaonline.org</a>
                           </span>
                           <span>
                              <i aria-hidden="true" class="icon icon-map-marker1"></i>
                              California
                           </span>
                        </div>
                        <p>MANCA envisions itself as a leading social and cultural organization for the Malayalee community of Northern California. We aim to promote and foster relationships and goodwill within the Malayalee diaspora, with a special emphasis on promoting the rich culture of Kerala and the Malayalee community. Our vision extends to creating a platform where our community can come together, build connections, support each other, and celebrate events that are an integral part of our culture and heritage.</p>
                        <!-- <p style="text-align: justify;">MANCA's mission is to facilitate, organize, and conduct events and opportunities that promote social, cultural, and charitable causes. We aim to create cultural awareness and help build a socially committed Malayalee community.</p> -->
                     </div>
                  </article>
               </div>
            </div>
         </div>
         <div class="event-inner">
            <div class="row align-items-center">
               <div class="col-lg-4 h-auto">
                  <article class="event-item event-item-bg" style="background-image: url(front/images/unbound-img5.jpg); min-height: 350px;">
                     <div class="event-content w-100 text-center m-auto">
                        <img src="{{ asset('front/images/logo-stem4girls-inverse.png') }}" alt="" style="height:150px"  class="mb-2">
                        <div class="social-links mt-5">
                           <ul>
                              @if(!empty($socialLinks['facebook_stem4g']))
                              <li>
                                 <a href="{{ $socialLinks['facebook_stem4g'] }}" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
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
                           </ul>
                        </div>
                     </div>
                     <div class="overlay"></div>
                  </article>
               </div>
               <div class="col-lg-8 h-auto">
                  <article class="event-item">
                     <div class="w-100">
                        <h3>STEM4Girls</h3>
                        <div class="event-meta">
                           <span class="time-meta">
                              <i aria-hidden="true" class="icon icon-globe"></i>
                              <a href="https://stem4girls.org" target="_blank" rel="noopener noreferrer">stem4girls.org</a>
                           </span>
                           <span>
                              <i aria-hidden="true" class="icon icon-map-marker1"></i>
                              United States of America & India
                           </span>
                        </div>
                        <p>STEM4Girls is a non-profit organization aiming to encourage the pursuit of Science, Technology, Engineering, and Math, collectively termed STEM fields.</p>
                        <p>STEM4Girls places particular emphasis on the encouragement and leadership of girls and other underrepresented groups in STEM fields, breaking down barriers.</p>
                        <p>STEM4Girls works to bring fun, educational workshops, and camps to garner the interest of young minds, with the hope that it will help them throughout their lives.</p>
                     </div>
                  </article>
               </div>
            </div>
         </div>
      </div>
   </section>
   <!-- client section html start -->
   <section class="client-section pt-5 pb-5">
      <div class="container">
         <div class="client-wrap client-slider text-center">
            <div class="client-item">
               <figure>
                <a href="https://thinkgyra.org" >
                  <img src="{{url('front/images/logo-gyra-inverse.png')}}" alt="">
                </a>
               </figure>
            </div>
            <div class="client-item">
               <figure>
                <a href="https://mancaonline.org" >
                  <img src="{{url('front/images/manca_no_bg.png')}}"  alt="" >
                </a>
               </figure>
            </div>
            <div class="client-item">
               <figure>
                  <a href="https://stem4girls.org" target="_blank" rel="noopener noreferrer">
                  <img src="{{url('front/images/logo-stem4girls.png')}}" alt="STEM4Girls">
                  </a>
               </figure>
            </div>
            
         </div>
      </div>
   </section>
   <!-- client html end -->

   <!-- home callback section html start -->
   @if(optional($settings->where('code','registration_status')->first())->value==1) 
   <section class="callback-inner-page" style="background-image: url(front/images/unbound-img23.jpg);">
      <div class="container">
         <div class="callback-content text-center">
            <div class="sub-title">REGISTER NOW</div>
            <h2 class="section-title">Prepare Your Application Letter for Admission</h2>
            <div class="button-wrap">
               <a href="{{route('register')}}" class="button-round">Register Now</a>
            </div>
         </div>
      </div>
      <div class="overlay"></div>
   </section>
   @endif  
   <!-- home callback section html end -->
   <!-- About page html end -->
</main>
@endsection
@section('script')
<script>
</script>
@endsection
