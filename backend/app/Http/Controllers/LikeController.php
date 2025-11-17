<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Like;
use App\Models\UserMatch;
use App\Models\Conversation;
use Inertia\Inertia;
use App\Models\UserCreditLedger;
use App\Models\AppSetting;

class LikeController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'to_user_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:pass,like,super_like'],
        ]);

        if (in_array($validated['type'], ['like','super_like']) && ! $user->hasFeature('Unlimited Swipes')) {
            $limit = (int) (optional(AppSetting::where('key','daily_swipe_limit')->first())->value ?? 0);
            if ($limit > 0) {
                $todayCount = Like::where('from_user_id', $user->id)
                    ->whereIn('type',['like','super_like'])
                    ->whereDate('created_at', now()->toDateString())
                    ->count();
                if ($todayCount >= $limit) {
                    return response()->json(['error' => 'Daily swipe limit reached'], 429);
                }
            }
        }

        if ($validated['type'] === 'super_like') {
            $price = (int) (optional(AppSetting::where('key','price_super_like')->first())->value ?? 0);
            if ($price > 0) {
                $balance = (int) UserCreditLedger::where('user_id', $user->id)->sum('change');
                if ($balance < $price) {
                    return response()->json(['error' => 'Insufficient credits'], 402);
                }
                UserCreditLedger::create([
                    'user_id' => $user->id,
                    'change' => -$price,
                    'reason' => 'super_like',
                    'meta' => ['to' => $validated['to_user_id']],
                ]);
            }
        }

        $like = Like::updateOrCreate(
            ['from_user_id' => $user->id, 'to_user_id' => $validated['to_user_id']],
            ['type' => $validated['type']]
        );

        $matched = false;
        if (in_array($validated['type'], ['like', 'super_like'])) {
            $reciprocal = Like::where('from_user_id', $validated['to_user_id'])
                ->where('to_user_id', $user->id)
                ->whereIn('type', ['like', 'super_like'])
                ->exists();
            if ($reciprocal) {
                $a = min($user->id, $validated['to_user_id']);
                $b = max($user->id, $validated['to_user_id']);
                $match = UserMatch::firstOrCreate(['user_id_a' => $a, 'user_id_b' => $b]);
                Conversation::firstOrCreate(['match_id' => $match->id]);
                $matched = true;
                event(new \App\Events\MatchCreated($match->id));
                $tokensA = \App\Models\DeviceToken::where('user_id', $a)->pluck('token');
                $tokensB = \App\Models\DeviceToken::where('user_id', $b)->pluck('token');
                $serverKey = optional(\App\Models\AppSetting::where('key','fcm_server_key')->first())->value;
                foreach ([$tokensA, $tokensB] as $tokens) {
                    foreach ($tokens as $token) {
                        if (! $serverKey) break;
                        \Illuminate\Support\Facades\Http::withHeaders(['Authorization' => 'key '.$serverKey, 'Content-Type' => 'application/json'])
                            ->post('https://fcm.googleapis.com/fcm/send', [
                                'to' => $token,
                                'notification' => ['title' => 'It\'s a match!', 'body' => 'You have a new match'],
                                'data' => ['match_id' => $match->id],
                            ]);
                    }
                }
            }
        }

        return response()->json(['matched' => $matched]);
    }

    public function likedMe(Request $request)
    {
        $user = $request->user();
        if (! $user->hasFeature('See Who Liked Me')) {
            return redirect()->route('app.premium');
        }
        $likes = Like::where('to_user_id', $user->id)->whereIn('type', ['like','super_like'])->get();
        $items = $likes->map(function ($l) {
            $u = \App\Models\User::find($l->from_user_id);
            $first = \App\Models\ProfilePhoto::where('user_id', $u->id)->orderBy('order')->first();
            $photo = null;
            if ($first) {
                $thumbPath = 'profile_photos/thumbs/'.basename($first->path);
                $photo = \Illuminate\Support\Facades\Storage::disk('public')->exists($thumbPath) ? $thumbPath : $first->path;
            }
            return ['id' => $u->id, 'name' => $u->name, 'photo' => $photo ? \Illuminate\Support\Facades\Storage::url($photo) : null];
        });
        return Inertia::render('App/LikedMe', ['items' => $items]);
    }
}
