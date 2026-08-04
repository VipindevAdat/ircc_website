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
                                                                     @if($isFirstPayment)
                                                                        @if($recipientType === 'admin')
                                                                           <h1>Hello Admin</h1>
                                                                           <p>Below are the registration details of <strong>{{ $registration->first_name }} {{ $registration->last_name }}</strong></p>
                                                                        @else
                                                                           <h1>Hello {{ $registration->first_name }} {{ $registration->last_name }}</h1>
                                                                           <p>Greetings from International Research Conference for Children!</p>
                                                                           <p>Your registration has been successfully completed.</p>
                                                                           <p></p>
                                                                        @endif
                                                                     @else
                                                                        @if($recipientType === 'admin')
                                                                           <h1>Hello Admin</h1>
                                                                           <p>Below are the participant details of <strong>{{ $registration->first_name }} {{ $registration->last_name }}</strong></p>
                                                                        @else
                                                                           <h1>Hello {{ $registration->first_name }} {{ $registration->last_name }}</h1>
                                                                           <p>Greetings from International Research Conference for Children!</p>
                                                                           <p>The new participants have been successfully added to your registration.</p>
                                                                           <p></p>
                                                                        @endif
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
                                       </tr>
                                       <tr>
                                          <td>
                                             <h4>Registration details</h4>
                                             <table width="100%" cellpadding="5" border="0" style="border: none;">
                                                <tbody>
                                                   <!-- Registration Number -->
                                                   <tr>
                                                      <td style="font-size: 14px; color: #82899a;">Registration ID:</td>
                                                      <td style="font-size: 15px; line-height: 30px; font-weight: 600; color: #4f8a10;">{{$registration->registration_number}}</td>
                                                   </tr>
                                                   <!-- Name -->
                                                   <tr>
                                                      <td style="font-size: 14px; color: #82899a;">Name:</td>
                                                      <td style="font-size: 15px; line-height: 30px; font-weight: 400;">{{$registration->first_name}} {{$registration->last_name}}</td>
                                                   </tr>
                                                   <!-- Email -->
                                                   <tr>
                                                      <td style="font-size: 14px; color: #82899a;">Email:</td>
                                                      <td style="font-size: 15px; line-height: 30px; font-weight: 400;">{{$registration->email}}</td>
                                                   </tr>
                                                   <!-- Phone Number -->
                                                   <tr>
                                                      <td style="font-size: 14px; color: #82899a;">Phone Number:</td>
                                                      <td style="font-size: 15px; line-height: 30px; font-weight: 400;">{{$registration->phone}}</td>
                                                   </tr>
                                                   <!-- School Name -->
                                                   <tr>
                                                      <td style="font-size: 14px; color: #82899a;">Institution Name:</td>
                                                      <td style="font-size: 15px; line-height: 30px; font-weight: 400;">{{$registration->institution_name}}</td>
                                                   </tr>
                                                </tbody>
                                             </table>
                                          </td>
                                       </tr>
                                       @if ($participants)
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
                                                            <td style="font-size: 15px; line-height: 30px; font-weight: 400;">{{$participant->first_name}} {{$participant->last_name}}</td>
                                                            <td style="font-size: 15px; line-height: 30px; font-weight: 400;">
                                                                {{ $participant->type == 0 ? 'No' : 'Yes' }}
                                                            </td>
                                                            <td style="font-size: 15px; line-height: 30px; font-weight: 400;">
                                                               {{ $participant->class ? $participant->class : '--' }}
                                                           </td>  
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>                                           
                                          </td>
                                       </tr>
                                       @endif
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