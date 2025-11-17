<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\CreditPack;
use App\Models\UserSubscription;
use App\Models\UserCreditLedger;
use App\Models\Payment;

class PremiumController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate(['plan_id' => ['required','exists:subscription_plans,id']]);
        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        $start = now();
        $end = now()->addDays(30);
        UserSubscription::create([
            'user_id' => $request->user()->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $start,
            'ends_at' => $end,
        ]);
        Payment::create([
            'user_id' => $request->user()->id,
            'gateway' => $request->input('gateway','manual'),
            'type' => 'subscription',
            'amount' => $plan->price,
            'currency' => 'USD',
            'status' => 'succeeded',
            'meta' => ['plan' => $plan->name],
        ]);
        return back();
    }

    public function buyCredits(Request $request)
    {
        $data = $request->validate(['pack_id' => ['required','exists:credit_packs,id']]);
        $pack = CreditPack::findOrFail($data['pack_id']);
        UserCreditLedger::create([
            'user_id' => $request->user()->id,
            'change' => $pack->credits,
            'reason' => 'purchase',
            'meta' => ['pack' => $pack->name],
        ]);
        Payment::create([
            'user_id' => $request->user()->id,
            'gateway' => $request->input('gateway','manual'),
            'type' => 'credits',
            'amount' => $pack->price,
            'currency' => 'USD',
            'status' => 'succeeded',
            'meta' => ['pack' => $pack->name],
        ]);
        return back();
    }

    public function stripeIntent(Request $request)
    {
        $data = $request->validate(['type' => ['required','in:subscription,credits'], 'id' => ['required','integer']]);
        $secret = optional(\App\Models\AppSetting::where('key','stripe_secret')->first())->value;
        if (! $secret) return response()->json(['error' => 'Stripe not configured'], 422);
        $stripe = new \Stripe\StripeClient($secret);
        if ($data['type'] === 'subscription') {
            $plan = SubscriptionPlan::findOrFail($data['id']);
            $intent = $stripe->paymentIntents->create([
                'amount' => (int) round($plan->price * 100),
                'currency' => 'usd',
                'metadata' => ['user_id' => $request->user()->id, 'type' => 'subscription', 'plan' => $plan->name],
            ]);
        } else {
            $pack = CreditPack::findOrFail($data['id']);
            $intent = $stripe->paymentIntents->create([
                'amount' => (int) round($pack->price * 100),
                'currency' => 'usd',
                'metadata' => ['user_id' => $request->user()->id, 'type' => 'credits', 'credits' => $pack->credits],
            ]);
        }
        return response()->json(['client_secret' => $intent->client_secret]);
    }

    public function paypalOrder(Request $request)
    {
        $data = $request->validate(['type' => ['required','in:subscription,credits'], 'id' => ['required','integer']]);
        $clientId = optional(\App\Models\AppSetting::where('key','paypal_client_id')->first())->value;
        $secret = optional(\App\Models\AppSetting::where('key','paypal_secret')->first())->value;
        if (! $clientId || ! $secret) return response()->json(['error' => 'PayPal not configured'], 422);
        $tokenResp = \Illuminate\Support\Facades\Http::asForm()->withBasicAuth($clientId, $secret)->post('https://api-m.paypal.com/v1/oauth2/token', ['grant_type' => 'client_credentials']);
        $accessToken = optional($tokenResp->json())['access_token'] ?? null;
        if (! $accessToken) return response()->json(['error' => 'PayPal auth failed'], 422);
        if ($data['type'] === 'subscription') {
            $plan = SubscriptionPlan::findOrFail($data['id']);
            $amount = number_format($plan->price, 2, '.', '');
        } else {
            $pack = CreditPack::findOrFail($data['id']);
            $amount = number_format($pack->price, 2, '.', '');
        }
        $order = \Illuminate\Support\Facades\Http::withToken($accessToken)->post('https://api-m.paypal.com/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => ['currency_code' => 'USD', 'value' => $amount],
                'custom_id' => (string) $request->user()->id,
                'description' => $data['type'],
            ]],
        ])->json();
        return response()->json($order);
    }

    public function stripeCheckout(Request $request)
    {
        $data = $request->validate(['type' => ['required','in:subscription,credits'], 'id' => ['required','integer']]);
        $secret = optional(\App\Models\AppSetting::where('key','stripe_secret')->first())->value;
        if (! $secret) return response()->json(['error' => 'Stripe not configured'], 422);
        $stripe = new \Stripe\StripeClient($secret);
        if ($data['type'] === 'subscription') {
            $plan = SubscriptionPlan::findOrFail($data['id']);
            $priceData = [
                'currency' => 'usd',
                'product_data' => ['name' => 'Subscription: '.$plan->name],
                'unit_amount' => (int) round($plan->price * 100),
            ];
            $metadata = ['user_id' => $request->user()->id, 'type' => 'subscription', 'plan' => $plan->name];
        } else {
            $pack = CreditPack::findOrFail($data['id']);
            $priceData = [
                'currency' => 'usd',
                'product_data' => ['name' => 'Credits: '.$pack->name],
                'unit_amount' => (int) round($pack->price * 100),
            ];
            $metadata = ['user_id' => $request->user()->id, 'type' => 'credits', 'credits' => $pack->credits];
        }
        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => $priceData,
                'quantity' => 1,
            ]],
            'success_url' => url('/app/premium').'?success=1',
            'cancel_url' => url('/app/premium').'?canceled=1',
            'payment_intent_data' => ['metadata' => $metadata],
        ]);
        return response()->json(['url' => $session->url]);
    }

    public function paypalCapture(Request $request)
    {
        $data = $request->validate(['order_id' => ['required','string']]);
        $clientId = optional(\App\Models\AppSetting::where('key','paypal_client_id')->first())->value;
        $secret = optional(\App\Models\AppSetting::where('key','paypal_secret')->first())->value;
        if (! $clientId || ! $secret) return response()->json(['error' => 'PayPal not configured'], 422);
        $tokenResp = \Illuminate\Support\Facades\Http::asForm()->withBasicAuth($clientId, $secret)->post('https://api-m.paypal.com/v1/oauth2/token', ['grant_type' => 'client_credentials']);
        $accessToken = optional($tokenResp->json())['access_token'] ?? null;
        if (! $accessToken) return response()->json(['error' => 'PayPal auth failed'], 422);
        $capture = \Illuminate\Support\Facades\Http::withToken($accessToken)->post('https://api-m.paypal.com/v2/checkout/orders/'.$data['order_id'].'/capture')->json();
        $resource = $capture['purchase_units'][0] ?? [];
        $amount = (float) ($resource['payments']['captures'][0]['amount']['value'] ?? 0);
        $currency = strtoupper($resource['payments']['captures'][0]['amount']['currency_code'] ?? 'USD');
        $userId = (int) ($resource['custom_id'] ?? $request->user()->id);
        Payment::create([
            'user_id' => $userId,
            'gateway' => 'paypal',
            'type' => $resource['description'] ?? 'purchase',
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'succeeded',
            'meta' => $capture,
        ]);
        return response()->json(['ok' => true]);
    }
}
