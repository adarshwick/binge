<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'reason' => ['required','string'],
            'notes' => ['nullable','string'],
        ]);
        Report::create([
            'reporter_id' => $request->user()->id,
            'reported_user_id' => (int) $data['user_id'],
            'reason' => $data['reason'],
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);
        return back();
    }
}