@extends('layouts.app')
@section('head')
<style>
   .donation-section {
      padding: 60px 0;
   }
   .donation-card {
      background: #fff;
      border-radius: 12px;
      padding: 40px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      border-top: 4px solid #F15B43;
      margin-bottom: 30px;
   }
   .donation-amount-options {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin: 30px 0;
   }
   .donation-amount-btn {
      padding: 15px 30px;
      border: 2px solid #F15B43;
      background: #fff;
      color: #F15B43;
      border-radius: 8px;
      font-size: 18px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
   }
   .donation-amount-btn:hover,
   .donation-amount-btn.active {
      background: #F15B43;
      color: #fff;
   }
   .donation-custom-amount {
      margin: 20px 0;
   }
   .donation-custom-amount input {
      width: 100%;
      max-width: 300px;
      padding: 15px;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 18px;
   }
   .donation-custom-amount input:focus {
      border-color: #F15B43;
      outline: none;
   }
   .donate-button {
      background: #F15B43;
      color: #fff;
      padding: 15px 40px;
      border: none;
      border-radius: 25px;
      font-size: 18px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 20px;
   }
   .donate-button:hover {
      background: #CB4B36;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(241, 91, 67, 0.3);
   }
</style>
@endsection
@section('content')
<main class="site-main">
   <!-- Inner Banner html start-->
   <section class="inner-banner-wrap">
      <div class="inner-baner-container" style="background-image: url({{ url("front/images/inner-banner-img-1.jpg")}}">
         <div class="container">
            <div class="inner-banner-content">
               <h1 class="inner-title">Donation</h1>
            </div>
         </div>
      </div>
   </section>
   <!-- Inner Banner html end-->

   <section class="donation-section">
      <div class="container">
         <div class="row justify-content-center">
            <div class="col-lg-10">
               <div class="donation-card">
                  <div class="section-head text-center mb-4">
                     <h2 class="section-title">Support IRCC 2026</h2>
                     <p style="font-size: 16px; color: #666; margin-top: 15px;">
                        Your generous contribution helps us empower young researchers and make this conference accessible to students from all backgrounds.
                     </p>
                  </div>

                  <div class="text-center">
                     <h3 style="color: #F15B43; margin-bottom: 20px;">Select Donation Amount</h3>
                     
                     <div class="donation-amount-options justify-content-center">
                        <button class="donation-amount-btn" data-amount="25">$25</button>
                        <button class="donation-amount-btn" data-amount="50">$50</button>
                        <button class="donation-amount-btn" data-amount="100">$100</button>
                        <button class="donation-amount-btn" data-amount="250">$250</button>
                        <button class="donation-amount-btn" data-amount="500">$500</button>
                        <button class="donation-amount-btn" data-amount="1000">$1,000</button>
                     </div>

                     <div class="donation-custom-amount">
                        <p style="margin-bottom: 10px; font-weight: 600;">Or enter a custom amount:</p>
                        <input type="number" id="custom-amount" placeholder="Enter amount" min="1" step="0.01">
                     </div>

                     <div style="margin-top: 30px;">
                        <button class="donate-button" id="donate-btn">Donate Now</button>
                     </div>

                     <p style="margin-top: 20px; font-size: 14px; color: #555;">
                        You will be redirected to <a href="https://www.paypal.com/paypalme/mancapaypal" target="_blank" rel="noopener noreferrer">PayPal</a> to complete your donation securely.
                     </p>
                     <p style="margin-top: 10px; font-size: 14px; color: #666; font-style: italic;">
                        Your contribution is tax-deductible.
                     </p>
                  </div>
               </div>

               <div class="donation-card">
                  <h3 style="color: #F15B43; margin-bottom: 20px;">How Your Donation Helps</h3>
                  <ul style="line-height: 2; font-size: 16px;">
                     <li>Provide resources and materials for conference participants</li>
                     <li>Enable accessibility accommodations for all attendees</li>
                     <li>Fund research awards and recognition programs</li>
                     <li>Support scholarships for students from underserved communities</li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </section>
</main>

<script>
   document.addEventListener('DOMContentLoaded', function() {
      /** IRCC donations — PayPal.Me (same account as https://www.paypal.com/paypalme/mancapaypal) */
      const PAYPAL_ME_BASE = 'https://www.paypal.com/paypalme/mancapaypal';

      const amountButtons = document.querySelectorAll('.donation-amount-btn');
      const customAmountInput = document.getElementById('custom-amount');
      const donateBtn = document.getElementById('donate-btn');
      let selectedAmount = null;

      // Handle preset amount buttons
      amountButtons.forEach(button => {
         button.addEventListener('click', function() {
            // Remove active class from all buttons
            amountButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            // Set selected amount
            selectedAmount = this.getAttribute('data-amount');
            // Clear custom amount input
            customAmountInput.value = '';
         });
      });

      // Handle custom amount input
      customAmountInput.addEventListener('input', function() {
         if (this.value) {
            // Remove active class from all buttons
            amountButtons.forEach(btn => btn.classList.remove('active'));
            selectedAmount = this.value;
         }
      });

      // Handle donate button click — open PayPal.Me with chosen amount
      donateBtn.addEventListener('click', function() {
         let raw = selectedAmount || (customAmountInput.value || '').trim();
         var dollars = parseFloat(raw);

         if (!raw || isNaN(dollars) || dollars <= 0) {
            alert('Please select or enter a donation amount.');
            return;
         }

         dollars = Math.round(dollars * 100) / 100;
         var amountSegment = (dollars % 1 === 0) ? String(Math.round(dollars)) : dollars.toFixed(2);
         window.location.href = PAYPAL_ME_BASE + '/' + encodeURIComponent(amountSegment);
      });
   });
</script>
@endsection
