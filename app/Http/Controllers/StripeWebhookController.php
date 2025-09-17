<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Checkout\Session;
use App\Models\Payment;
use App\Models\User;

class StripeWebhookController extends Controller
{
public function handleWebhook(Request $request)
{
    $data = $request->all();

    if (($data['type'] ?? '') === 'checkout.session.completed') {
        $session = (object) ($data['data']['object'] ?? []);

        // تأكد إنه مش متسجل قبل كده
        $existing = Payment::where('session_id', $session->id)->first();
        if ($existing) {
            return response()->json(['message' => 'Already processed']);
        }

        Payment::create([
            'user_id'    => $session->metadata['user_id'] ?? null,
            'session_id' => $session->id,
            'amount'     => $session->amount_total,
            'currency'   => $session->currency,
            'status'     => 'paid',
        ]);

        return response()->json(['message' => 'Payment recorded']);
    }

    return response('Webhook received', 200);
}

}
