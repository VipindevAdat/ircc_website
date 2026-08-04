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
                           <img src="{{ asset('assets/images/ircc.png') }}" class="logo" alt="IRCC Logo">
                        </a>
                     </td>
                  </tr>
                  <tr>
                     <td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
                        <table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                           <tr>
                              <td class="content-cell">
                                 <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" class="MainContainer">
                                    <tbody>
                                       <tr>
                                          <td>
                                             <table border="0" width="100%" style="border: none">
                                                <tbody>
                                                   <tr>
                                                      <td>
                                                         <table border="0" style="width: 100%; float: left; text-align: left; margin: 0 0 0px;">
                                                            <tbody>
                                                               <tr>
                                                                  <td style="width: 20%; vertical-align: middle;">
                                                                     <tr>
                                                                        <td>
                                                                           <table border="0" cellpadding="0" style="width: 100%; border: solid 1px transparent; font-family: 'Roboto', sans-serif;">
                                                                              <tbody>
                                                                                 <tr>
                                                                                    <td>
                                                                                       <table width="100%" cellpadding="5" border="0" style="border: none;">
                                                                                          <tbody>
                                                                                             <tr>
                                                                                                <td style="font-size: 15px; line-height: 30px; font-weight: 400;">Hello,</td>
                                                                                             </tr>
                                                                                             <tr>
                                                                                                <td style="font-size: 15px; line-height: 30px; font-weight: 400;">Greetings from International Research Conference for Children !!</td>
                                                                                             </tr>
                                                                                             @if((int) $data->status === 0)
                                                                                             <tr>
                                                                                                <td style="font-size: 15px; line-height: 40px; font-weight: 400;">Your abstract has been saved as a <strong>draft</strong>. You can return to the dashboard to edit and submit it when you are ready.</td>
                                                                                             </tr>
                                                                                             <tr>
                                                                                                <td style="font-size: 15px; line-height: 40px; font-weight: 400;">No review will begin until you submit the final version.</td>
                                                                                             </tr>
                                                                                             @else
                                                                                             <tr>
                                                                                                <td style="font-size: 15px; line-height: 40px; font-weight: 400;">Your abstract has been successfully submitted. It’s presently being given consideration for the review process.</td>
                                                                                             </tr>
                                                                                             <tr>
                                                                                                <td style="font-size: 15px; line-height: 40px; font-weight: 400;">You will be informed regarding the acceptance of your abstract after peer review. Kindly be patient.</td>
                                                                                             </tr>
                                                                                             @endif
                                                                                          </tbody>
                                                                                       </table>
                                                                                    </td>
                                                                                 </tr>
                                                                              </tbody>
                                                                           </table>
                                                                        </td>
                                                                     </tr>
                                                         
                                                                     <!-- Abstract and Registration Details -->
                                                                     <tr>
                                                                        <td>
                                                                           <table border="0" cellpadding="5" width="100%" style="border: none;">
                                                                              <tbody>
                                                                                 <tr>
                                                                                    <td style="font-size: 15px; font-weight: 600; color: #4f8a10; text-align: left; padding: 5px 0;">Abstract ID: {{$data->pid}}</td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td style="font-size: 15px; font-weight: 600; color: #4f8a10; text-align: left; padding: 5px 0;">Registration Id: {{$registration->registration_number}}</td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td style="font-size: 15px; text-align: left; padding: 5px 0;">Title: {{$data->abstract_title}}</td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td style="font-size: 15px; text-align: left; padding: 5px 0;">Authors: {{ is_array($data->student_authors) ? implode(', ', $data->student_authors) : 'N/A' }}</td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td style="font-size: 15px; text-align: left; padding: 5px 0;">Corresponding author: {{$registration->first_name}} {{ $registration->last_name }}</td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td style="font-size: 15px; text-align: left; padding: 5px 0;">Mail Id: {{$data->correspondent_author}}</td>
                                                                                 </tr>
                                                                              </tbody>
                                                                           </table>
                                                                        </td>
                                                                     </tr>
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
   </body>
</html>