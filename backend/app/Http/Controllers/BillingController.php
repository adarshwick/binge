<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Payment;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)->orderByDesc('created_at')->get(['id','gateway','type','amount','currency','status','created_at']);
        $subs = UserSubscription::where('user_id', $request->user()->id)->orderByDesc('starts_at')->get();
        $subsOut = $subs->map(function($s){
            $plan = SubscriptionPlan::find($s->subscription_plan_id);
            return [
                'plan' => $plan?->name,
                'status' => $s->status,
                'starts_at' => $s->starts_at,
                'ends_at' => $s->ends_at,
            ];
        });
        return Inertia::render('App/Billing', ['payments' => $payments, 'subscriptions' => $subsOut]);
    }
}
