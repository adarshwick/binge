<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate(['lat' => ['required','numeric'], 'lng' => ['required','numeric']]);
        $user = $request->user();
        $user->lat = $data['lat'];
        $user->lng = $data['lng'];
        $user->save();
        return response()->json(['ok' => true]);
    }
}
