<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function pay(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'egp',
                    'product_data' => ['name' => $request->product_name],
                    'unit_amount' => $request->product_price * 100, // $15.00
                ],
                'quantity' => $request->quantity,
            ]],
            'mode' => 'payment',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'metadata' => [
                'user_id' => $request->user()->id,
            ],
        ]);

        return response()->json(['url' => $session->url]);
    }
}
