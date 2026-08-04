
@if($request->number>0)
@for($i=0;$i<$request->number;++$i)
@php $no=$i+1;@endphp
<label>Student {{$no}}</label> 
@if($s!=0 && $n!=0)
<div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group">

<input class="form-control-student" type="text" name="stud_first_name[]"  title="Please enter alphabets only"  placeholder="Student firstname*"  data-id="first_name{{$no}}">
<span class="form-validation" id="first_name{{$no}}" data></span>
</div>
<div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group" >

<input class="form-control-student" type="text" name="stud_last_name[]"  title="Please enter alphabets only"  placeholder="Student lastname*" data-id="last_name{{$no}}">
<span class="form-validation" id="last_name{{$no}}"></span>
</div>

<div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group" >

<select class="form-control-student" type="text" name="stud_class[]" data-id="stud_class{{$no}}" placeholder="" >
<option value="">Select Class</option>
    @for($k=$s;$k<=$n;++$k)
     <option value="{{$k}}">{{$k}}</option>
    @endfor
</select>
<span class="form-validation" id="stud_class{{$n}}"></span>
</div>
@else
<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">

<input class="form-control-student" type="text" name="stud_first_name[]"  title="Please enter alphabets only"  placeholder="Student firstname*"  data-id="first_name{{$no}}">
<span class="form-validation" id="first_name{{$no}}" data></span>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >

<input class="form-control-student" type="text" name="stud_last_name[]"  title="Please enter alphabets only"  placeholder="Student lastname*" data-id="last_name{{$no}}">
<span class="form-validation" id="last_name{{$no}}"></span>
</div>


@endif
@endfor
@endif