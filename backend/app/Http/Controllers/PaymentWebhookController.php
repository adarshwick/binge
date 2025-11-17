<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\UserSubscription;
use App\Models\UserCreditLedger;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Stripe\Webhook as StripeWebhook;

class PaymentWebhookController extends Controller
{
    public function stripe(Request $request)
    {
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET') ?: optional(\App\Models\AppSetting::where('key','stripe_webhook_secret')->first())->value;
        $event = null;
        try {
            $event = StripeWebhook::constructEvent($request->getContent(), $sigHeader, $secret);
        } catch (\Exception $e) {
            return response()->json(['error' => 'invalid signature'], 400);
        }
        $type = $event['type'] ?? null;
        $data = $event['data']['object'] ?? [];
        $eventId = $event['id'] ?? null;
        if ($eventId && DB::table('processed_events')->where('event_id',$eventId)->exists()) {
            return response()->json(['ok' => true]);
        }
        $userId = $data['metadata']['user_id'] ?? null;
        if (! $userId) return response()->json(['ok' => true]);
        if (in_array($type, ['payment_intent.succeeded','invoice.payment_succeeded'])) {
            $amount = isset($data['amount_received']) ? $data['amount_received'] / 100 : 0;
            Payment::create([
                'user_id' => $userId,
                'gateway' => 'stripe',
                'type' => $data['metadata']['type'] ?? 'subscription',
                'amount' => $amount,
                'currency' => strtoupper($data['currency'] ?? 'USD'),
                'status' => 'succeeded',
                'meta' => $data,
            ]);
            if (($data['metadata']['type'] ?? '') === 'credits') {
                $credits = (int) ($data['metadata']['credits'] ?? 0);
                if ($credits > 0) {
                    UserCreditLedger::create(['user_id' => $userId, 'change' => $credits, 'reason' => 'purchase', 'meta' => ['gateway' => 'stripe']]);
                }
            } elseif (($data['metadata']['type'] ?? '') === 'subscription') {
                $planName = (string) ($data['metadata']['plan'] ?? '');
                $plan = $planName ? SubscriptionPlan::where('name', $planName)->first() : null;
                $start = now();
                $end = now()->addDays(30);
                if ($plan) {
                    UserSubscription::create([
                        'user_id' => $userId,
                        'subscription_plan_id' => $plan->id,
                        'status' => 'active',
                        'starts_at' => $start,
                        'ends_at' => $end,
                    ]);
                }
            }
        }
        if ($eventId) DB::table('processed_events')->insert(['event_id'=>$eventId,'gateway'=>'stripe','created_at'=>now(),'updated_at'=>now()]);
        return response()->json(['ok' => true]);
    }

    public function paypal(Request $request)
    {
        $payload = $request->all();
        $resource = $payload['resource'] ?? [];
        $webhookId = env('PAYPAL_WEBHOOK_ID') ?: optional(\App\Models\AppSetting::where('key','paypal_webhook_id')->first())->value;
        $clientId = optional(\App\Models\AppSetting::where('key','paypal_client_id')->first())->value;
        $secret = optional(\App\Models\AppSetting::where('key','paypal_secret')->first())->value;
        if ($webhookId && $clientId && $secret) {
            $tokenResp = \Illuminate\Support\Facades\Http::asForm()->withBasicAuth($clientId, $secret)->post('https://api-m.paypal.com/v1/oauth2/token', ['grant_type' => 'client_credentials']);
            $accessToken = optional($tokenResp->json())['access_token'] ?? null;
            if ($accessToken) {
                $verification = \Illuminate\Support\Facades\Http::withToken($accessToken)->post('https://api-m.paypal.com/v1/notifications/verify-webhook-signature', [
                    'transmission_id' => $request->header('Paypal-Transmission-Id'),
                    'transmission_time' => $request->header('Paypal-Transmission-Time'),
                    'cert_url' => $request->header('Paypal-Cert-Url'),
                    'auth_algo' => $request->header('Paypal-Auth-Algo'),
                    'transmission_sig' => $request->header('Paypal-Transmission-Sig'),
                    'webhook_id' => $webhookId,
                    'webhook_event' => $payload,
                ])->json();
                if (($verification['verification_status'] ?? '') !== 'SUCCESS') {
                    return response()->json(['error' => 'invalid signature'], 400);
                }
            }
        }
        $eventId = $payload['id'] ?? null;
        if ($eventId && DB::table('processed_events')->where('event_id',$eventId)->exists()) {
            return response()->json(['ok' => true]);
        }
        $userId = $resource['custom_id'] ?? null;
        if (! $userId) return response()->json(['ok' => true]);
        $status = $resource['status'] ?? 'COMPLETED';
        if ($status === 'COMPLETED') {
            $amount = (float) ($resource['amount']['value'] ?? 0);
            Payment::create([
                'user_id' => $userId,
                'gateway' => 'paypal',
                'type' => $resource['custom_type'] ?? 'subscription',
                'amount' => $amount,
                'currency' => strtoupper($resource['amount']['currency_code'] ?? 'USD'),
                'status' => 'succeeded',
                'meta' => $resource,
            ]);
            if (($resource['custom_type'] ?? '') === 'credits') {
                $credits = (int) ($resource['custom_credits'] ?? 0);
                if ($credits > 0) {
                    UserCreditLedger::create(['user_id' => $userId, 'change' => $credits, 'reason' => 'purchase', 'meta' => ['gateway' => 'paypal']]);
                }
            } elseif (($resource['custom_type'] ?? '') === 'subscription') {
                $planName = (string) ($resource['custom_plan'] ?? '');
                $plan = $planName ? SubscriptionPlan::where('name', $planName)->first() : null;
                $start = now();
                $end = now()->addDays(30);
                if ($plan) {
                    UserSubscription::create([
                        'user_id' => $userId,
                        'subscription_plan_id' => $plan->id,
                        'status' => 'active',
                        'starts_at' => $start,
                        'ends_at' => $end,
                    ]);
                }
            }
        }
        if ($eventId) DB::table('processed_events')->insert(['event_id'=>$eventId,'gateway'=>'paypal','created_at'=>now(),'updated_at'=>now()]);
        return response()->json(['ok' => true]);
    }
}
