<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Like;
use App\Models\ProfilePhoto;
use Illuminate\Support\Facades\Storage;

class DiscoverController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $excludedIds = Like::where('from_user_id', $user->id)->pluck('to_user_id')->toArray();
        $cacheKey = 'discover:'.implode(':', [
            $user->id,
            $user->pref_gender ?? 'any',
            $user->pref_min_age ?? 'min',
            $user->pref_max_age ?? 'max',
            $user->pref_distance_km ?? 'dist',
            (int)($user->lat*1000).'_'.(int)($user->lng*1000),
            count($excludedIds),
        ]);
        $candidates = \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function () use ($user, $excludedIds) {
            return User::query()
            ->where('id', '!=', $user->id)
            ->whereNotIn('id', $excludedIds)
            ->when($user->pref_gender && $user->pref_gender !== 'any', fn($q) => $q->where('gender', $user->pref_gender))
            ->get(['id','name','email','dob','lat','lng','gender','boost_until','created_at']);
        });

        // Preload first photo urls for candidates
        $photoMap = [];
        $ids = $candidates->pluck('id');
        if ($ids->count()) {
            $photos = ProfilePhoto::whereIn('user_id', $ids)->orderBy('order')->get();
            foreach ($photos->groupBy('user_id') as $uid => $group) {
                $first = $group->first();
                if ($first) {
                    $thumbPath = 'profile_photos/thumbs/'.basename($first->path);
                    $photoMap[$uid] = Storage::disk('public')->exists($thumbPath)
                        ? Storage::url($thumbPath)
                        : Storage::url($first->path);
                } else {
                    $photoMap[$uid] = null;
                }
            }
        }

        $cards = $candidates
            ->filter(function ($u) use ($user) {
                // Age filter
                if ($user->pref_min_age && $user->pref_max_age && $u->dob) {
                    $age = (int) floor((now()->diffInDays($u->dob)) / 365.25);
                    if ($age < $user->pref_min_age || $age > $user->pref_max_age) return false;
                }
                // Distance filter
                if ($user->lat && $user->lng && $user->pref_distance_km && $u->lat && $u->lng) {
                    $dist = self::haversineKm($user->lat, $user->lng, $u->lat, $u->lng);
                    if ($dist > $user->pref_distance_km) return false;
                }
                return true;
            })
            ->sortBy(function ($u) use ($user) {
                $boostPriority = (isset($u->boost_until) && $u->boost_until && $u->boost_until > now()) ? 0 : 1;
                $dist = ($user->lat && $user->lng && $u->lat && $u->lng) ? self::haversineKm($user->lat, $user->lng, $u->lat, $u->lng) : 99999;
                return [$boostPriority, $dist, -optional($u->created_at)->timestamp ?? 0];
            })
            ->take(20)
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'subtitle' => $u->email,
                'boosted' => ($u->boost_until && $u->boost_until > now()),
                'distanceKm' => ($user->lat && $user->lng && $u->lat && $u->lng) ? round(self::haversineKm($user->lat, $user->lng, $u->lat, $u->lng)) : null,
                'photo' => $photoMap[$u->id] ?? null,
            ]);
        return Inertia::render('App/Discover', ['cards' => $cards]);
    }

    private static function haversineKm($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}
