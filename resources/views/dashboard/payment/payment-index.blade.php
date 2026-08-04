<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to Payment Gateway</title>
</head>
<body>
    <h2>Please wait while we redirect you to BillDesk...</h2>
    <form method="POST" action="https://www.billdesk.com/pgidsk/PGIMerchantPayment" id="paymentForm">
        <input type="hidden" name="msg" value="<?php echo htmlspecialchars($finalRequest); ?>">
        <button type="submit">Click here if not redirected</button>
    </form>
    <script>
        document.getElementById('paymentForm').submit();
    </script>
</body>
</html>