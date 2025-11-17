<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\ProfilePhoto;
use App\Models\UserPromptAnswer;
use App\Models\ProfilePrompt;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function show(Request $request, $user_id)
    {
        $other = User::findOrFail($user_id);
        $photos = ProfilePhoto::where('user_id', $other->id)->orderBy('order')->get()->map(function ($p) {
            $thumbPath = 'profile_photos/thumbs/'.basename($p->path);
            $url = Storage::disk('public')->exists($thumbPath) ? Storage::url($thumbPath) : Storage::url($p->path);
            return ['id' => $p->id, 'url' => $url];
        });
        $answers = UserPromptAnswer::where('user_id', $other->id)->get()->map(function ($a) {
            $prompt = ProfilePrompt::find($a->prompt_id);
            return ['prompt' => $prompt?->text, 'answer' => $a->answer];
        });
        return Inertia::render('App/UserProfile', [
            'user' => ['id' => $other->id, 'name' => $other->name, 'bio' => $other->bio ?? null],
            'photos' => $photos,
            'answers' => $answers,
        ]);
    }
}