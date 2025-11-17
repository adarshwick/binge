<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Rekognition\RekognitionClient;
use App\Models\AppSetting;
use App\Models\ProfilePhoto;

class RekognitionController extends Controller
{
    public function compare(Request $request)
    {
        $request->validate(['selfie' => ['required','image']]);
        $key = optional(AppSetting::where('key','aws_access_key_id')->first())->value;
        $secret = optional(AppSetting::where('key','aws_secret_access_key')->first())->value;
        $region = optional(AppSetting::where('key','aws_region')->first())->value ?? 'us-east-1';
        if (! $key || ! $secret) return response()->json(['error' => 'AWS not configured'], 422);
        $client = new RekognitionClient(['version' => 'latest', 'region' => $region, 'credentials' => ['key' => $key, 'secret' => $secret]]);
        $selfieBytes = file_get_contents($request->file('selfie')->getRealPath());
        $photos = ProfilePhoto::where('user_id', $request->user()->id)->get();
        foreach ($photos as $photo) {
            $profileBytes = file_get_contents(storage_path('app/public/'.$photo->path));
            $result = $client->compareFaces([
                'SourceImage' => ['Bytes' => $selfieBytes],
                'TargetImage' => ['Bytes' => $profileBytes],
                'SimilarityThreshold' => 80,
            ]);
            $matches = $result->get('FaceMatches') ?? [];
            if (!empty($matches)) {
                return response()->json(['matched' => true, 'similarity' => $matches[0]['Similarity']]);
            }
        }
        return response()->json(['matched' => false]);
    }
}
