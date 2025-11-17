<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class OtpController extends Controller
{
    public function show()
    {
        return Inertia::render('App/Otp');
    }

    public function send(Request $request)
    {
        $data = $request->validate(['phone' => ['required','string']]);
        $user = $request->user();
        $user->phone = $data['phone'];
        $user->save();
        $code = (string) random_int(100000, 999999);
        Cache::put('otp_'.$user->id, $code, now()->addMinutes(10));
        return back();
    }

    public function verify(Request $request)
    {
        $data = $request->validate(['code' => ['required','string']]);
        $user = $request->user();
        $code = Cache::get('otp_'.$user->id);
        if ($code && $code === $data['code']) {
            $user->phone_verified_at = now();
            $user->save();
            Cache::forget('otp_'.$user->id);
            return redirect()->route('app.discover');
        }
        return back();
    }
}
