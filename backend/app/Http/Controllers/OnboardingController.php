<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Aws\Rekognition\RekognitionClient;
use App\Models\AppSetting;
use App\Models\ProfilePhoto;
use App\Models\Verification;

class OnboardingController extends Controller
{
    public function complete(Request $request)
    {
        $data = $request->validate([
            'pref_min_age' => ['nullable','integer'],
            'pref_max_age' => ['nullable','integer'],
            'pref_distance_km' => ['nullable','integer'],
            'pref_gender' => ['nullable','string'],
            'lat' => ['nullable','numeric'],
            'lng' => ['nullable','numeric'],
            'answers' => ['nullable','array'],
            'bio' => ['nullable','string'],
        ]);
        $user = $request->user();
        $user->fill($data);
        $user->onboarding_completed = true;
        $user->save();
        if (!empty($data['answers'])) {
            foreach ($data['answers'] as $promptId => $answer) {
                \App\Models\UserPromptAnswer::updateOrCreate(['user_id' => $user->id, 'prompt_id' => (int) $promptId], ['answer' => (string) $answer]);
            }
        }
        return redirect()->route('app.discover');
    }

    public function selfie(Request $request)
    {
        $request->validate(['photo' => ['required','image']]);
        $path = $request->file('photo')->store('verifications', 'public');
        $provider = optional(AppSetting::where('key','verification_provider')->first())->value;
        if ($provider === 'aws') {
            $key = optional(AppSetting::where('key','aws_access_key_id')->first())->value;
            $secret = optional(AppSetting::where('key','aws_secret_access_key')->first())->value;
            $region = optional(AppSetting::where('key','aws_region')->first())->value ?? 'us-east-1';
            if ($key && $secret) {
                $client = new RekognitionClient(['version' => 'latest', 'region' => $region, 'credentials' => ['key' => $key, 'secret' => $secret]]);
                $selfieBytes = file_get_contents($request->file('photo')->getRealPath());
                $photos = ProfilePhoto::where('user_id', $request->user()->id)->orderBy('order')->get();
                $matched = false;
                foreach ($photos as $photo) {
                    $profilePath = storage_path('app/public/'.$photo->path);
                    if (!is_file($profilePath)) continue;
                    $profileBytes = file_get_contents($profilePath);
                    $result = $client->compareFaces([
                        'SourceImage' => ['Bytes' => $selfieBytes],
                        'TargetImage' => ['Bytes' => $profileBytes],
                        'SimilarityThreshold' => 80,
                    ]);
                    $matches = $result->get('FaceMatches') ?? [];
                    if (!empty($matches)) { $matched = true; break; }
                }
                $ver = Verification::create([
                    'user_id' => $request->user()->id,
                    'photo_path' => $path,
                    'status' => $matched ? 'approved' : 'pending',
                ]);
                if ($matched) {
                    $user = $request->user();
                    $user->verified_at = now();
                    $user->save();
                }
                return back();
            }
        }
        Verification::create([
            'user_id' => $request->user()->id,
            'photo_path' => $path,
            'status' => 'pending',
        ]);
        return back();
    }
}
