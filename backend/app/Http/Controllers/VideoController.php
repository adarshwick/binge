<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\ChimeSDKMeetings\ChimeSDKMeetingsClient;
use App\Models\AppSetting;

class VideoController extends Controller
{
    public function start(Request $request, $match_id)
    {
        $provider = optional(\App\Models\AppSetting::where('key','video_provider')->first())->value ?? 'agora';
        $appId = optional(\App\Models\AppSetting::where('key','agora_app_id')->first())->value;
        $token = optional(\App\Models\AppSetting::where('key','agora_token')->first())->value;
        $channel = 'match_'.$match_id;
        return response()->json(['provider' => $provider, 'channel' => $channel, 'appId' => $appId, 'token' => $token]);
    }

    public function chimeJoin(Request $request, $match_id)
    {
        $key = optional(AppSetting::where('key','aws_access_key_id')->first())->value;
        $secret = optional(AppSetting::where('key','aws_secret_access_key')->first())->value;
        $region = optional(AppSetting::where('key','aws_region')->first())->value ?? 'us-east-1';
        if (! $key || ! $secret) return response()->json(['error' => 'AWS not configured'], 422);
        $client = new ChimeSDKMeetingsClient(['version' => 'latest', 'region' => $region, 'credentials' => ['key' => $key, 'secret' => $secret]]);
        $meeting = $client->createMeeting(['ClientRequestToken' => uniqid('', true), 'ExternalMeetingId' => 'match_'.$match_id, 'MediaRegion' => $region]);
        $attendee = $client->createAttendee(['MeetingId' => $meeting['Meeting']['MeetingId'], 'ExternalUserId' => (string) $request->user()->id]);
        return response()->json(['meeting' => $meeting['Meeting'], 'attendee' => $attendee['Attendee']]);
    }
}
