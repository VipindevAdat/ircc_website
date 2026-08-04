<html lang="en" class="no-js">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>International Research Conference for Children </title>
      <link rel="shortcut icon" type="image/png" href="../images/fav-icon.png">
      <meta name="description" content="">
      <meta name="keywords" content=" ">
      <meta name="author" content="">
      <meta property="og:image" content="">
      <meta property="og:site_name" content="">
      <link rel="stylesheet" href="{{ asset('front/css/mail.css') }}">
      <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
   </head>
   <body style="margin: 0px;">
      <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
         <tr>
            <td align="center">
               <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                     <td class="header">
                        <a href="{{ route('accounts.dashboard') }}" style="display: inline-block;">
                           <img src="{{ asset('assets/images/ircc.png') }}" class="logo" alt="IRCC Logo" width="50%">
                        </a>
                     </td>
                  </tr>
                  <tr>
                     <td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
                        <table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                           <tr>
                              <td class="content-cell">
                                 <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="left" class="MainContainer">
                                    <tbody>
                                       <tr>
                                          <td>
                                             <table border="0" width="100%" style="border: none;">
                                                <tbody>
                                                   <tr>
                                                      <td>
                                                         <table border="0" style="width: 100%; text-align: left; margin: 0;">
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        @if($recipientType === 'admin')
                                                                           <h1>Hello Admin</h1>
                                                                           <p>Below are the payment details of <strong>{{ $registration->first_name }} {{ $registration->last_name }}</strong></p>
                                                                        @else
                                                                           <h1>Hello {{ $registration->first_name }} {{ $registration->last_name }}</h1>
                                                                           <p>Greetings from International Research Conference for Children!</p>
                                                                           <p>Your payment has been successfully completed.</p>
                                                                           <p></p>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                         </table>
                                                      </td>
                                                   </tr>
                                                </tbody>
                                             </table>
                                          </td>
                                       </tr>
                                       <tr>
                                          <td>
                                             <h4 style="margin-top: 10px">Participant Details</h4>
                                             <table width="100%" cellpadding="5" border="0" style="border: none; border-collapse: collapse;">
                                                <thead>
                                                    <tr>
                                                        <th style="font-size: 14px; color: #82899a; text-align: left;">Name</th>
                                                        <th style="font-size: 14px; color: #82899a; text-align: left;">Guest</th>
                                                        <th style="font-size: 14px; color: #82899a; text-align: left;">Class</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($participants as $participant)
                                                        <tr>
                                                            <td style="font-size: 15px; line-height: 30px; font-weight: 400;">{{$participant->participant->first_name}} {{$participant->participant->last_name}}</td>
                                                            <td style="font-size: 15px; line-height: 30px; font-weight: 400;">
                                                                {{ $participant->participant->type == 0 ? 'No' : 'Yes' }}
                                                            </td>
                                                            <td style="font-size: 15px; line-height: 30px; font-weight: 400;">
                                                               {{ $participant->participant->class ? $participant->participant->class : '--' }}
                                                           </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            <h4 style="margin-top: 20px">Payment Details</h4>
                                            <div id="payment">
                                            @php
                                                $studentCount = $paymentSummary['studentCount'] ?? 0;
                                                $adultCount = $paymentSummary['adultCount'] ?? 0;
                                                $studentPrice = (float)str_replace(',', '', $paymentSummary['studentPrice'] ?? '150.00');
                                                $adultPrice = (float)str_replace(',', '', $paymentSummary['adultPrice'] ?? '50.00');
                                                $studentSubtotal = (float)str_replace(',', '', $paymentSummary['studentSubtotal'] ?? '0.00');
                                                $adultSubtotal = (float)str_replace(',', '', $paymentSummary['adultSubtotal'] ?? '0.00');
                                                $secEarly = (int)($paymentSummary['studentCountEarly'] ?? 0);
                                                $secReg = (int)($paymentSummary['studentCountRegular'] ?? 0);
                                                $priceEarly = (float)str_replace(',', '', $paymentSummary['studentPriceEarly'] ?? '150.00');
                                                $priceReg = (float)str_replace(',', '', $paymentSummary['studentPriceRegular'] ?? '200.00');
                                                $subEarly = (float)str_replace(',', '', $paymentSummary['studentSubtotalEarly'] ?? '0.00');
                                                $subReg = (float)str_replace(',', '', $paymentSummary['studentSubtotalRegular'] ?? '0.00');
                                                $pendEarly = (int)($paymentSummary['pendingStudentCountEarly'] ?? 0);
                                                $pendReg = (int)($paymentSummary['pendingStudentCountRegular'] ?? 0);
                                                $pendAdult = (int)($paymentSummary['pendingAdultCount'] ?? 0);
                                                $pendSubEarly = (float)str_replace(',', '', $paymentSummary['pendingStudentSubtotalEarly'] ?? '0.00');
                                                $pendSubReg = (float)str_replace(',', '', $paymentSummary['pendingStudentSubtotalRegular'] ?? '0.00');
                                                $pendSubAdult = (float)str_replace(',', '', $paymentSummary['pendingAdultSubtotal'] ?? '0.00');
                                            @endphp
                                            <p style="font-size: 13px; color: #666; margin: 0 0 10px 0;">Student rates reflect when each participant was added (early-bird vs regular). Already-paid participant slots are not charged again.</p>
                                            <table width="100%" cellpadding="5" border="0" style="border: none;">
                                            <tbody>
                                               @if($studentCount > 0 || $adultCount > 0)
                                                   @if($secEarly > 0 || $secReg > 0)
                                                       @if($secEarly > 0)
                                                       <tr>
                                                          <td><span>{{ $secEarly }} Student{{ $secEarly > 1 ? 's' : '' }} (early rate) × ${{ number_format($priceEarly, 2) }}:</span></td>
                                                          <td width="25%"><span class="float-end">${{ number_format($subEarly, 2) }}</span></td>
                                                       </tr>
                                                       @endif
                                                       @if($secReg > 0)
                                                       <tr>
                                                          <td><span>{{ $secReg }} Student{{ $secReg > 1 ? 's' : '' }} (regular rate) × ${{ number_format($priceReg, 2) }}:</span></td>
                                                          <td width="25%"><span class="float-end">${{ number_format($subReg, 2) }}</span></td>
                                                       </tr>
                                                       @endif
                                                   @elseif($studentCount > 0)
                                                   <tr>
                                                      <td><span>{{ $studentCount }} Student{{ $studentCount > 1 ? 's' : '' }} × ${{ number_format($studentPrice, 2) }}:</span></td>
                                                      <td width="25%"><span class="float-end">${{ number_format($studentSubtotal, 2) }}</span></td>
                                                   </tr>
                                                   @endif
                                                   @if($adultCount > 0)
                                                   <tr>
                                                      <td><span>{{ $adultCount }} Adult{{ $adultCount > 1 ? 's' : '' }} × ${{ number_format($adultPrice, 2) }}:</span></td>
                                                      <td width="25%"><span class="float-end">${{ number_format($adultSubtotal, 2) }}</span></td>
                                                   </tr>
                                                   @endif
                                               @else
                                                   <tr>
                                                      <td><span>Amount Per Participant:</span></td>
                                                      <td width="25%"><span class="float-end">${{ number_format($amountPerHead, 2) }}</span></td>
                                                   </tr>
                                               @endif
                                               <tr>
                                                  <td><span>Participants Covered by This Payment:</span></td>
                                                  <td width="25%"><span class="float-end"><strong>
                                                      @if(isset($isPartialPayment) && $isPartialPayment)
                                                          0 (Partial Payment)
                                                      @else
                                                          {{ $participantCount }}
                                                      @endif
                                                  </strong></span></td>
                                               </tr>
                                               @if(isset($totalParticipantsInRegistration) && $totalParticipantsInRegistration)
                                               <tr>
                                                  <td><span>Total Participants in Registration:</span></td>
                                                  <td width="25%"><span class="float-end">{{ $totalParticipantsInRegistration }}</span></td>
                                               </tr>
                                               @endif
                                            </tbody>
                                            </table>

                                            <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                                            <table width="100%" cellpadding="5" border="0" style="border: none;">
                                               <tbody>
                                                  <tr>
                                                     <td><span><strong>Sub Total:</strong></span></td>
                                                     <td width="25%"><span class="float-end"><strong>${{ number_format($paymentSummary['subTotal'], 2) }}</strong></span></td>
                                                  </tr>
                                                  <!-- Registration Fee removed - it's already included in participant fees -->
                                               </tbody>
                                            </table>
                                            
                                            <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                                            <table width="100%" cellpadding="5" border="0" style="border: none;">
                                               <tbody>
                                                  <tr>
                                                     <td><span><strong>Grand Total:</strong></span></td>
                                                     <td width="25%"><span class="float-end"><strong>${{ number_format($paymentSummary['grandTotal'], 2) }}</strong></span></td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                            <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                                            <table width="100%" cellpadding="5" border="0" style="border: none;">
                                                <tbody>
                                               <!-- Payment Information -->
                                               <tr>
                                                  <td><span>This Payment Amount:</span></td>
                                                  <td width="25%"><span class="float-end">${{ number_format($paymentSummary['thisPaymentAmount'], 2) }}</span></td>
                                               </tr>
                                               <tr>
                                                  <td><span><strong>Total Amount Paid (All Payments):</strong></span></td>
                                                  <td width="25%"><span class="float-end"><strong>${{ number_format($paymentSummary['amountPaid'], 2) }}</strong></span></td>
                                               </tr>
                                               @if(($paymentSummary['amountRemaining'] ?? 0) > 0)
                                               <tr>
                                                  <td><span>Balance due now:</span></td>
                                                  <td width="25%"><span class="float-end">${{ number_format($paymentSummary['amountRemaining'], 2) }}</span></td>
                                               </tr>
                                               @endif
                                               @if(($pendEarly + $pendReg + $pendAdult) > 0)
                                               <tr>
                                                  <td colspan="2" style="padding-top: 12px; font-size: 13px; color: #555;"><strong>Pending payment breakdown</strong> (only unpaid participant slots)</td>
                                               </tr>
                                               @if($pendEarly > 0)
                                               <tr>
                                                  <td><span>{{ $pendEarly }} Student{{ $pendEarly > 1 ? 's' : '' }} (early) × ${{ number_format($priceEarly, 2) }}:</span></td>
                                                  <td width="25%"><span class="float-end">${{ number_format($pendSubEarly, 2) }}</span></td>
                                               </tr>
                                               @endif
                                               @if($pendReg > 0)
                                               <tr>
                                                  <td><span>{{ $pendReg }} Student{{ $pendReg > 1 ? 's' : '' }} (regular) × ${{ number_format($priceReg, 2) }}:</span></td>
                                                  <td width="25%"><span class="float-end">${{ number_format($pendSubReg, 2) }}</span></td>
                                               </tr>
                                               @endif
                                               @if($pendAdult > 0)
                                               <tr>
                                                  <td><span>{{ $pendAdult }} Adult{{ $pendAdult > 1 ? 's' : '' }} × ${{ number_format($adultPrice, 2) }}:</span></td>
                                                  <td width="25%"><span class="float-end">${{ number_format($pendSubAdult, 2) }}</span></td>
                                               </tr>
                                               @endif
                                               @endif
                                              
                                            </tbody>
                                            </table>
                                           
                                            </div>  
                                            <div>
                                               <table width="100%" cellpadding="5" border="0" style="border-collapse: collapse;">
                                                  <tr>
                                                     <td><span>Payment Mode:</span></td>
                                                     <td  width="25%">{{ $payment->payment_mode }}</td>
                                                  </tr>
                                                  @if($payment->payment_mode == 'Direct Bank Transfer')
                                                  <tr>
                                                     <td><span>Payment Date:</span></td>
                                                     <td  width="25%">{{ $payment->payment_date }}</td>
                                                     
                                                  </tr>
                                                  <tr>
                                                     <td><span>Payment Reference:</span></td>
                                                     <td  width="25%">{{ $payment->payment_reference }}</td>
                                                  </tr>
                                                  <tr>
                                                     <td><span>Payment Created On:</span></td>
                                                     <td  width="25%">{{ $payment->created_at ? $payment->created_at->format('d-M-Y') : 'N/A' }}</td>
                                                  </tr>
                                                  @else
                                                  <tr>
                                                     <td><span>Payment Reference:</span></td>
                                                     <td  width="25%">{{ $payment->transaction_id ?? 'N/A' }}</td>
                                                  </tr>
                                                  <tr>
                                                     <td><span>Payment Date:</span></td>
                                                     <td  width="25%">{{ $payment->transaction_date ? \Carbon\Carbon::parse($payment->transaction_date)->format('d-M-Y h:i A') : 'N/A' }}</td>
                                                  </tr>
                                                  @endif
                                               </table>
                                            </div>
                                            
                                            @if($recipientType === 'user' && $payment->payment_status == 3 && isset($paymentSummary) && $paymentSummary['amountRemaining'] == 0)
                                            <div style="margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 5px; text-align: center;">
                                               <h3 style="color: #f15b43; margin-bottom: 15px;">Next Steps</h3>
                                               <p style="font-size: 16px; line-height: 1.6; color: #333; margin-bottom: 20px;">
                                                   Your payment has been successfully processed and you have fully paid. You can now upload your abstract for the conference.
                                               </p>
                                               <a href="{{ route('accounts.dashboard') }}" 
                                                  style="display: inline-block; padding: 12px 30px; background-color: #f15b43; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px; margin-top: 10px;">
                                                   Go to Dashboard to Upload Abstract
                                               </a>
                                               <p style="font-size: 14px; color: #666; margin-top: 15px;">
                                                   Or visit: <a href="{{ route('accounts.dashboard') }}" style="color: #f15b43;">{{ route('accounts.dashboard') }}</a>
                                               </p>
                                            </div>
                                            @endif
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>   
                              </td>
                           </tr>
                           <tr>
                              <td>
                                 <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                    <tr>
                                       <td class="content-cell" align="center" style="font-size: 12px">
                                          © {{ date('Y') }} IRCC. All rights reserved.
                                       </td>
                                    </tr>
                                 </table>
                              </td>
                           </tr>
                        </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
      </table>
      <br>    
   </body>
</html>