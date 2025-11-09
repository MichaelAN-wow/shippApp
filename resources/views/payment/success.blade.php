<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3 text-center">
                <h2 class="text-success">Payment Successful!</h2>
                <p class="mt-3">Thank you for your payment. Your transaction has been completed successfully.</p>
                <p class="mt-3">Transaction ID: <strong>{{ $transactionId }}</strong></p>
                <a href="{{ url('/') }}" class="btn btn-primary mt-3">Go to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
