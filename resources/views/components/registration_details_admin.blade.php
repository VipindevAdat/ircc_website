<div class="donate-form form-inner" style="margin-bottom: 20px;!important;">
            <div class="row align-items-center justify-content-between">
            <div class="col-lg-12">
           <input type="hidden" id="registration_id"  name="registration_id" value="{{$registration->id}}">
                <div class="event-content">
                 
                    <div class="row form-wrap">
                        <div class="col-lg-4">
                            <label>Registration Id</label>
                            <p>{{$registration->registration_number}}</p>
                        </div>
                        <div class="col-lg-4">
                            <label>School Name</label>
                            <p>{{$registration->school_name}}</p>
                        </div>
                    </div>
                    <div class="row form-wrap">
                        <div class="col-lg-4">
                            <label>Mentor</label>
                            <p>{{$registration->teacher_name}}</p>
                        </div>
                        <div class="col-lg-4">
                            <label>Email</label>
                            <p>{{$registration->email}}</p>
                        </div>
                        <div class="col-lg-4">
                            <label>Phone</label>
                            <p>{{$registration->phone}}</p>
                        </div>
                       
                    </div>
                    <div class="row form-wrap" >
                    <div class="col-lg-4">
                            <label>Participation</label>
                            <p>{{$registration->participation==1?'Participation only':'Participation and presentation'}}</p>
                        </div>
                       
                        <div class="col-lg-4">
                            <label>Category</label>
                            <p>{{$registration->category}}</p>
                        </div>
                        <div class="col-lg-4">
                            <label>No Students</label>
                            <p>{{$registration->members}}</p>
                        </div>
                    </div>
                    @if(!empty($registration->accompany_payment))
                    <div class="row form-wrap">
                    <div class="col-lg-4">
                        <label>No of Accompany Person</label>
                        <p>{{$registration->no_accompany}}</p>
                    </div>
                    <div class="col-lg-4">
                        <label>Payment Reference</label>
                        <p>{{$registration->accompany_payment->payment_reference}}</p>
                    </div>
                    <div class="col-lg-4">
                        <label>Payment Date</label>
                        <p>{{$registration->accompany_payment->payment_date}}</p>
                    </div>
                  </div> 
                  @endif
                    @if($type=='register')
                    <h5>Students Details</h5>
                    <div class="row form-wrap">
                    <table width="100%" cellpadding="5"  style=" border: 1px solid #ddd;border-collapse: collapse;">
                     <tbody>
                    <tr style="background-color: #80c1c3;color: white;width: 100%">
                        <td>
                        <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                            <p class="o_text-light" data-color="Light" style="color: #FFF;margin-top: 0px;margin-bottom: 0px;"></p>
                            </div>
                        </div>
                        </td>
                        <td>
                        <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                            <p class="o_text-light" data-color="Light" style="color:#FFF;margin-top: 0px;margin-bottom: 0px;">First Name</p>
                            </div>
                        </div>
                        </td>
                        <td>
                        <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                            <p class="o_text-light" data-color="Light" style="color: #FFF;margin-top: 0px;margin-bottom: 0px;">Last Name</p>
                            </div>
                        </div>
                        </td>
                        <td>
                        <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                            <p class="o_text-light" data-color="Light" style="color:#FFF;margin-top: 0px;margin-bottom: 0px;">Class</p>
                            </div>
                        </div>
                        </td>
                    </tr>
                    @foreach($registration->teams as $key=>$team)
                    <tr style="width: 100%">
                        <td> 
                        <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                            <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                            <p class="o_text-light" data-color="Light" style="margin-top: 0px;margin-bottom: 0px;">{{($key+1)}}</p>
                            </div>
                        </div>
                        </td>
                            <td> <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                                <p class="o_text-light" data-color="Light" style="margin-top: 0px;margin-bottom: 0px;">{{$team->first_name}}</p>
                                </div>
                            </div>
                        </td>
                        <td> <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                                <p class="o_text-light" data-color="Light" style="margin-top: 0px;margin-bottom: 0px;">{{$team->last_name}}</p>
                                </div>
                            </div>
                        </td>
                        <td> <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                                <p class="o_text-light" data-color="Light" style="margin-top: 0px;margin-bottom: 0px;">{{$team->class}}</p>
                                </div>
                            </div>
                        </td>
                    
                    </tr>
                    @endforeach
                    
                    
                </tbody>
                </table>
                 </div>
                    @endif
                @if(!empty($registration->accompany_payment))
                <h5>Accompany Person Details</h5>  
                  <div class="row form-wrap">
                    <table width="100%" cellpadding="5"  style=" border: 1px solid #ddd;border-collapse: collapse;">
                <tbody>
                    <tr style="background-color: #80c1c3;color: white;width: 100%">
                        <td>
                        <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                            <p class="o_text-light" data-color="Light" style="color: #fff;margin-top: 0px;margin-bottom: 0px;"></p>
                            </div>
                        </div>
                        </td>
                        <td>
                        <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                            <p class="o_text-light" data-color="Light" style="color: #fff;margin-top: 0px;margin-bottom: 0px;">Name </p>
                            </div>
                        </div>
                        </td>
                        <td>
                        <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                            <p class="o_text-light" data-color="Light" style="color: #fff;margin-top: 0px;margin-bottom: 0px;">Email</p>
                            </div>
                        </div>
                        </td>
                   
                    </tr>
                    @foreach($registration->accompany as $key=>$team)
                    <tr style="width: 100%">
                        <td> 
                        <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                            <p class="o_text-light" data-color="Light" style="margin-top: 0px;margin-bottom: 0px;">{{($key+1)}}</p>
                            </div>
                        </div>
                        </td>
                            <td>
                            <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                                <p class="o_text-light" data-color="Light" style="margin-top: 0px;margin-bottom: 0px;">{{$team->name}}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                        <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                                <p class="o_text-light" data-color="Light" style="margin-top: 0px;margin-bottom: 0px;">{{$team->email}}</p>
                                </div>
                            </div>
                        </td>
                       
                    
                    </tr>
                    @endforeach
                    
                    
                    </tbody>
                    </table>
                    </div> 
                @endif
                </div>
            </div>                                   
                </div>
            
            </div>