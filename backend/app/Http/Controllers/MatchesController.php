<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\UserMatch;
use App\Models\User;
use App\Models\ProfilePhoto;
use Illuminate\Support\Facades\Storage;

class MatchesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $matches = UserMatch::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id_a', $user->id)->orWhere('user_id_b', $user->id);
            })->get()
            ->map(function ($m) use ($user) {
                $otherId = $m->user_id_a === $user->id ? $m->user_id_b : $m->user_id_a;
                $other = User::find($otherId);
                $first = ProfilePhoto::where('user_id', $other->id)->orderBy('order')->first();
                $photo = null;
                if ($first) {
                    $thumbPath = 'profile_photos/thumbs/'.basename($first->path);
                    $photo = Storage::disk('public')->exists($thumbPath) ? $thumbPath : $first->path;
                }
                return [
                    'id' => $m->id,
                    'user' => ['id' => $other->id, 'name' => $other->name, 'photo' => $photo ? Storage::url($photo) : null],
                ];
            });
        return Inertia::render('App/Matches', ['items' => $matches]);
    }
}
