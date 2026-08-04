<div class="col-lg-12">
<div class="row">    
@if($number>0)
    @for($i=0;$i<$number;++$i)
        @php $n=$i+1;@endphp
    <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
        <label>Name</label>
        <input type="text" name="accompany_name[]" id="accompany_name" class="form-control-accompany"  data-id="accompany_name{{$n}}" >
        <span class="form-validation" id="accompany_name{{$n}}" data></span>
        
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
        <label>Email</label>
        <input type="email" name="accompany_email[]" id="accompany_email" class="form-control-accompany"  data-id="accompany_email{{$n}}">
        <span class="form-validation" id="accompany_email{{$n}}" data></span>
    </div>
   @endfor
@endif  
</div> 
</div>