<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BIllDesk Payment Integration</title>
</head>
<body>
  <h1>Redirecting to Payment Gateway...</h1>
<form id="sdklaunch" method="POST" action="{{$paymentUrl}}">
<input type="hidden" id="bdOrderId" name="bdOrderId" value="{{$parameters['bdorderid']}}">
<input type="hidden" id="merchantId" name="merchantId" value="{{$parameters['mercid']}}">
<input type="hidden" id="rdata" name="rdata" value="{{$parameters['rdata']}}">

</form>
<script>
    window.onload = function () {
      document.getElementById('sdklaunch').submit();
    };
  </script>
</body>
</html>