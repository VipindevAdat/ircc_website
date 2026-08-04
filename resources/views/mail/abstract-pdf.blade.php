<html>
   <body>
      <div style="padding:20px">
         <div style="display:flex;justify-content: space-between;">
            <span style="font-size:21px;color:black;background:white;float:right;flex: 1; text-align: right; white-space: nowrap;">{{$id}}</span>
         </div>
         <style>
            p {
               text-align: center; 
               font-size: 28px !important;
            }
            h4 {
               text-align: center;
               font-size: 34px !important;
            }
            h2 {
               text-align: left;
               font-size: 30px !important;
            }
            h3 {
               text-align: right;
            }
            .title-class {
               text-align: left !important;
               font-size: 26px !important;
            }
            .abstract-class {
               font-size: 15pt !important;
               line-height: 1.5 !important;
               text-align: justify !important;
            }
            /* WYSIWYG output often embeds small inline font sizes; normalize for readable PDF */
            .abstract-class * {
               font-size: inherit !important;
               line-height: inherit !important;
            }
         </style>
         
         <h4 style="font-size:21px;font-family:'Arial',sans-serif;color:black;text-align:center;">International Research Conference for Children 2026 (IRCC'26)</h4>
         
         {{-- <p><span class="title-class" style="font-size:21px;font-family:'Arial',sans-serif;color:black;text-align:center;">{{$id}}</span></p> --}}
         
         @php
            $publicRoot = is_dir(base_path('public_html')) ? base_path('public_html') : public_path();
            $stemLogoPath = $publicRoot . '/front/images/logo-stem4girls.png';
            $gyraLogoPath = $publicRoot . '/front/images/logo-gyra.png';
            $irccLogoPath = $publicRoot . '/front/images/unbound-logo.png';
         @endphp
         <table align="center" style="padding-bottom:20px;">
            <tr>
               <td align="center">
                  <img src="{{ $stemLogoPath }}" width="150">
               </td>
               <td align="center">
                  <img src="{{ $gyraLogoPath }}" width="150">
               </td>
               <td align="center">
                  <img src="{{ $irccLogoPath }}" width="150" alt="IRCC">
               </td>
            </tr>
         </table>
         
         <p><strong><span style="font-size:21px;font-family:'Arial',sans-serif;color:black;">Category: {{ $result->category->name ?? 'N/A' }}</span></strong></p>
         <p><strong><span style="font-size:21px;font-family:'Arial',sans-serif;color:black;">Domain: {{ $result->abstractDomain->domain ?? 'N/A' }}</span></strong></p>
         <p><strong><span style="font-size:21px;font-family:'Arial',sans-serif;color:black;">Title: {{$result['abstract_title']}}</span></strong></p>
         <p><strong><span style="font-size:21px;font-family:'Arial',sans-serif;color:black;">Corresponding Author: {{$result['correspondent_author']}}</span></strong></p>
         <p><strong><span style="font-size:21px;font-family:'Arial',sans-serif;color:black;">Student Authors: {{ implode(', ', $result['student_authors']) }}</span></strong></p>
         @if($result['additional_authors'])
         <p><strong><span style="font-size:21px;font-family:'Arial',sans-serif;color:black;">Additional Authors: {{$result['additional_authors']}}</span></strong></p>
         @endif
         
         <p class="abstract-class">
            <strong><span style="font-size:15pt;font-family:'Arial',sans-serif;">Abstract:</span></strong>
            {{ $result['abstract_content'] }}
         </p>
         
       
         <p class="abstract-class">
            <strong><span style="font-size:15pt;font-family:'Arial',sans-serif;">References:</span></strong><br>
            @if($result['references'])
            @php $references=json_decode($result['references'])@endphp
            @foreach($references as $reference)
           {{$reference}}<br>
            @endforeach
            @endif
         </p>
        
      </div>
   </body>
</html>