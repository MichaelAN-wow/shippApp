<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Charge;

class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        return view('payment.payment');
    }

    public function processPayment(Request $request)
    {
        // Set your secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Validate the request data
        $validatedData = $request->validate([
            'payment_method_id' => 'required'
        ]);

        try {
            // Create a PaymentIntent with the amount and currency
            // $paymentIntent = PaymentIntent::create([
            //     'amount' => 39 * 100, // Amount in cents
            //     'currency' => 'usd',
            //     'payment_method' => $validatedData['payment_method_id'],
            //     'confirmation_method' => 'manual',
            //     'confirm' => true,
            //     'return_url' => route('payment.success'),
            // ]);

            $charge = Charge::create([
                "amount" => 39 * 100,
                "currency" => "usd",
                "source" => $validatedData['payment_method_id'],
                "description" => "Payment for with platform fee."
            ]);

            return response()->json([
                'success' => true,
                'payment_intent' => 'Payment successful',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
