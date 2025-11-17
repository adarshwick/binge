<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCreditLedger;
use App\Models\AppSetting;

class BoostController extends Controller
{
    public function apply(Request $request)
    {
        $user = $request->user();
        $price = (int) (optional(AppSetting::where('key','price_boost')->first())->value ?? 0);
        if ($price > 0) {
            $balance = (int) UserCreditLedger::where('user_id', $user->id)->sum('change');
            if ($balance < $price) {
                return response()->json(['error' => 'Insufficient credits'], 402);
            }
            UserCreditLedger::create([
                'user_id' => $user->id,
                'change' => -$price,
                'reason' => 'boost',
                'meta' => [],
            ]);
        }
        $user->boost_until = now()->addMinutes(15);
        $user->save();
        return response()->json(['boost_until' => $user->boost_until]);
    }
}
