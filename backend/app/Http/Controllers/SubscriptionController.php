<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;

class SubscriptionController extends Controller
{
    public function cancel(Request $request)
    {
        $sub = UserSubscription::where('user_id', $request->user()->id)->where('status','active')->latest()->firstOrFail();
        $sub->status = 'canceled';
        $sub->save();
        return back();
    }

    public function renew(Request $request)
    {
        $sub = UserSubscription::where('user_id', $request->user()->id)->where('status','canceled')->latest()->firstOrFail();
        $sub->status = 'active';
        $sub->starts_at = now();
        $sub->ends_at = now()->addDays(30);
        $sub->save();
        return back();
    }

    public function changePlan(Request $request)
    {
        $data = $request->validate(['plan_id' => ['required','exists:subscription_plans,id']]);
        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        $sub = UserSubscription::where('user_id', $request->user()->id)->where('status','active')->latest()->firstOrFail();
        $sub->subscription_plan_id = $plan->id;
        $sub->save();
        return back();
    }
}
