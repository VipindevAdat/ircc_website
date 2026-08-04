<div class="donate-form form-inner" style="margin-bottom: 20px;!important;">
    <div class="row align-items-center justify-content-between">
        <div class="col-lg-12">
            <div class="event-content">

                <div class="row form-wrap">
                    <div class="col-lg-4">
                        <label>Registration Number</label>
                        <p>{{$registration->reg_id}}</p>
                    </div>
                    <div class="col-lg-4">
                        <label>Institute Name</label>
                        <p>{{$registration->institution_name}}</p>
                    </div>
                    <div class="col-lg-4">
                        <label>Abstract Status</label>
                        <p>{{ $abstractStatus }}</p>
                    </div>
                </div>
                <div class="row form-wrap">
                    <div class="col-lg-4">
                        <label>Correspondent Name</label>
                        <p>{{$registration->first_name}} {{$registration->last_name}}</p>
                    </div>
                    <div class="col-lg-4">
                        <label>Email</label>
                        <p>{{$registration->email}}</p>
                    </div>
                    <div class="col-lg-4">
                        <label>Phone Number</label>
                        <p>{{$registration->phone}}</p>
                    </div>
                </div>
                @if($registration->hear_about_us)
                <div class="row form-wrap">
                    <div class="col-lg-4">
                        <label>How did you hear about us?</label>
                        <p>{{$registration->hear_about_us}}</p>
                    </div>
                </div>
                @endif
                <div class="row form-wrap" >
                    <div class="col-lg-4">
                        <label>Category</label>
                        <p>{{ $abstract->category ?? 'No category selected' }}</p>
                    </div>
                    <div class="col-lg-4">
                        <label>No Students</label>
                        <p>{{$participants->count()}}</p>
                    </div>
                </div>
                <div class="row form-wrap">
                    <div class="col-lg-4">
                        <label>Total Amount Paid</label>
                        <p>${{ $totalAmountPaid }}</p>
                    </div>
                    <div class="col-lg-4">
                        <label>Pending Payment</label>
                        <p>${{ $amountPending }}</p>
                    </div>
                </div>

                <h5>Students Details</h5>
                @if($participants->count())
                <div class="row form-wrap mb-5">
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
                        @if(!empty($participants))
                        @foreach($participants as $participant)
                            <tr style="width: 100%">
                                <td>
                                    <div class="o_col o_col-4" style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif; margin-top: 0px; margin-bottom: 0px; font-size: 14px; line-height: 21px; text-align: left; padding-left: 8px; padding-right: 8px;">
                                            <p class="o_text-light" data-color="Light" style="margin-top: 0px; margin-bottom: 0px;">
                                                {{ $loop->iteration }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td> <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                                            <p class="o_text-light" data-color="Light" style="margin-top: 0px;margin-bottom: 0px;">{{$participant->first_name}}</p>
                                        </div>
                                    </div>
                                </td>
                                <td> <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                                            <p class="o_text-light" data-color="Light" style="margin-top: 0px;margin-bottom: 0px;">{{$participant->last_name}}</p>
                                        </div>
                                    </div>
                                </td>
                                <td> <div class="o_col o_col-4" style="display: inline-block;vertical-align: top;width: 100%;max-width: 300px;">
                                        <div class="o_px-xs o_sans o_text-xs o_left" data-size="Text XS" data-min="10" data-max="18" style="font-family: 'Roboto', sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;text-align: left;padding-left: 8px;padding-right: 8px;">
                                            <p class="o_text-light" data-color="Light" style="margin-top: 0px;margin-bottom: 0px;">{{$participant->class}}</p>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                @else
                <p>No participants added</p>
                @endif
                <h5>Payment History</h5>
            @if($payments->count())
                <!-- Detailed Payment Information -->
             
                <div class="row form-wrap">
                    <table width="100%" cellpadding="5" style="border: 1px solid #ddd; border-collapse: collapse;">
                        <tbody>
                        <tr style="background-color: #80c1c3; color: white;">
                            <td>
                                <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                    <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                        <p style="color: #FFF; margin: 0;">Payment Mode</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                    <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                        <p style="color: #FFF; margin: 0;">Payment Reference</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                    <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                        <p style="color: #FFF; margin: 0;">Payment Date</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                    <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                        <p style="color: #FFF; margin: 0;">Payment Amount</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                    <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                        <p style="color: #FFF; margin: 0;">Paid Participants</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                    <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                        <p style="color: #FFF; margin: 0;">Paid On</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>
                                    <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                        <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                            <p style="margin: 0;">{{ $payment->payment_mode }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                        <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                            <p style="margin: 0;">{{ $payment->payment_reference }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                        <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                            <p style="margin: 0;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                        <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                            <p style="margin: 0;">{{ $payment->amount }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                        <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                            <p style="margin: 0;">{{ $payment->participants }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: inline-block; vertical-align: top; width: 100%; max-width: 300px;">
                                        <div style="font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 21px; padding: 8px; text-align: left;">
                                            <p style="margin: 0;">{{ \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y') }}</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p>Payment not done</p>
                @endif
       
            </div>
        </div>
    </div>
</div>
