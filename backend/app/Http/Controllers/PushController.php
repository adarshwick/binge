<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceToken;

class PushController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'token' => ['required','string'],
            'platform' => ['nullable','string'],
        ]);
        DeviceToken::updateOrCreate(['token' => $data['token']], ['user_id' => $request->user()->id, 'platform' => $data['platform'] ?? null]);
        return response()->json(['ok' => true]);
    }
}
