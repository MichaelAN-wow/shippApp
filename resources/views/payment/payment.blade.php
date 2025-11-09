<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h4 class="text-center">Stripe Payment</h4>

                <form id="payment-form">
                    <div class="form-group">
                        <label for="amount">Amount (USD):</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="1" placeholder="Enter amount" required>
                    </div>

                    <div class="form-group">
                        <label for="card-element">Credit or Debit Card</label>
                        <div id="card-element" class="form-control">
                            <!-- Stripe Card Element will be inserted here -->
                        </div>
                        <!-- Error message placeholder -->
                        <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                    </div>

                    <!-- Submit Button -->
                    <button class="btn btn-primary btn-block" id="submit-button" type="submit">Pay Now</button>
                </form>

                <!-- Payment Result -->
                <div id="payment-result" class="mt-3"></div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Stripe.js with your public key
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();

        // Create an instance of the card Element
        var cardElement = elements.create('card');

        // Mount the card Element into the `#card-element` div
        cardElement.mount('#card-element');

        // Handle real-time validation errors from the card Element
        cardElement.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            // Disable the submit button to prevent multiple submissions
            document.getElementById('submit-button').disabled = true;

            // Create a PaymentMethod with the card Element
            stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
            }).then(function(result) {
                if (result.error) {
                    // Display error in #card-errors
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;

                    // Re-enable the submit button
                    document.getElementById('submit-button').disabled = false;
                } else {
                    // Otherwise, send payment method ID to server
                    processPayment(result.paymentMethod.id);
                }
            });
        });

        // Function to process the payment
        function processPayment(paymentMethodId) {
            var amount = document.getElementById('amount').value;
            fetch('{{ route('payment.process') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    payment_method_id: paymentMethodId,
                    amount: amount
                })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(responseJson) {
                var paymentResultElement = document.getElementById('payment-result');
                if (responseJson.success) {
                    paymentResultElement.textContent = 'Payment successful!';
                } else {
                    paymentResultElement.textContent = 'Payment failed: ' + responseJson.error;
                }

                // Re-enable the submit button after processing
                document.getElementById('submit-button').disabled = false;
            })
            .catch(function(error) {
                console.error('Error:', error);
                document.getElementById('card-errors').textContent = 'Payment error!';

                // Re-enable the submit button after error
                document.getElementById('submit-button').disabled = false;
            });
        }
    </script>
</body>
</html>
