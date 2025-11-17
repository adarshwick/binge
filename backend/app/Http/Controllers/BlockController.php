<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserBlock;

class BlockController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['user_id' => ['required','exists:users,id']]);
        if ($data['user_id'] == $request->user()->id) return response()->json(['error' => 'Invalid'], 422);
        UserBlock::firstOrCreate(['user_id' => $request->user()->id, 'blocked_user_id' => (int) $data['user_id']]);
        return back();
    }

    public function destroy(Request $request, $user_id)
    {
        UserBlock::where('user_id', $request->user()->id)->where('blocked_user_id', (int) $user_id)->delete();
        return back();
    }
}