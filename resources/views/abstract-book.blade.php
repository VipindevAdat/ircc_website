@extends('layouts.app')
@section('head')
<style>
.event-icon{
    font-size:50px;
    color: #364F75;
}
.event-item:not(.event-item-bg):nth-child(2n+2) .event-icon {
   color: #F15B43;
}

   .event-item {
      flex-wrap: unset;
      align-items: center;
    
    justify-content: center;
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
               <h1 class="inner-title">Abstract Book</h1>
            </div>
         </div>
      </div>
   </section>
   <!-- Inner Banner html end-->
   <section class="event-page-section">
               <div class="container">
                  <div class="row align-items-center">
                    
                     <div class="col-lg-12">
                       @if($abstractBooks->count())
                       @foreach($abstractBooks as $abstractBook) 
                                       
                     <article class="event-item" style="align-items: center;/* display: flex; */justify-content: center;">
                           <div class="event-content">
                              <h3>{{$abstractBook->title}} </h3>
                              <div class="event-meta">
                                 <span class="time-meta">
                                   
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ Carbon\Carbon::parse($abstractBook->date_from)->format('M')}}  {{ Carbon\Carbon::parse($abstractBook->date_from)->format('d')}} - {{ Carbon\Carbon::parse($abstractBook->date_to)->format('d')}}, {{ Carbon\Carbon::parse($abstractBook->date_from)->format('Y')}}
                                 </span>
                                 <span>
                                    <i aria-hidden="true" class="icon icon-map-marker1"></i>
                                   {{$abstractBook->location}}
                                 </span>
                              </div>
                              <p>{{$abstractBook->description}}</p>
                           </div>
                           <a class="button-round abstract_book" data-url="{{$abstractBook->getMedia('abstractBook')->first()?$abstractBook->getMedia('abstractBook')->first()->getUrl():''}}"  data-bs-toggle="modal" href="#abstractBookModal"  data-backdrop="static" data-keyboard="false" style="    /* padding: 5px; */
    height: 100%;
    align-items: cer;
    width: fit-content;
    text-align: center;border-radius: 5px;">View Abstract</a>
                        </article>  
                    
                        @endforeach
                        @else
                        <div class="section-head text-center mt-4">
                        <h2 class="section-title">No published abstract book found</h2>
                        </div>
                        @endif
                     </div>
                  </div>
                
               </div>
            </section>
</main>
<div class="modal fade" id="abstractBookModal" tabindex="-1" role="dialog" aria-labelledby="abstractBookModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
               <iframe src="" id="iframe_abstract" style="width:100%; height:600px;" frameborder="0"></iframe>
            </div>
         </div>
      </div>
   </div>
@endsection
@section('script')
<script>
$('.abstract_book').click(function () {
    url=$(this).data('url');
    $('#iframe_abstract').attr('src',url);       
});  

</script>
@endsection