<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WebRTCController extends Controller
{
    public function offer(Request $request, $match_id)
    {
        $data = $request->validate(['sdp' => ['required','string']]);
        Cache::put('webrtc_offer_'.$match_id, $data['sdp'], now()->addMinutes(5));
        return response()->json(['ok' => true]);
    }

    public function answer(Request $request, $match_id)
    {
        $data = $request->validate(['sdp' => ['required','string']]);
        Cache::put('webrtc_answer_'.$match_id, $data['sdp'], now()->addMinutes(5));
        return response()->json(['ok' => true]);
    }

    public function poll(Request $request, $match_id)
    {
        return response()->json([
            'offer' => Cache::get('webrtc_offer_'.$match_id),
            'answer' => Cache::get('webrtc_answer_'.$match_id),
        ]);
    }
}
