<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilePhoto;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileMediaController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate(['photo' => ['required','image']]);
        $path = $request->file('photo')->store('profile_photos', 'public');
        try {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('profile_photos/thumbs');
            $full = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
            $thumb = \Illuminate\Support\Facades\Storage::disk('public')->path('profile_photos/thumbs/'.basename($path));
            $info = getimagesize($full);
            if ($info) {
                [$w,$h] = [$info[0],$info[1]];
                $targetW = 480;
                $targetH = (int) round($h * ($targetW / $w));
                $img = null;
                switch ($info['mime']) {
                    case 'image/jpeg': $img = imagecreatefromjpeg($full); break;
                    case 'image/png': $img = imagecreatefrompng($full); break;
                    case 'image/webp': if (function_exists('imagecreatefromwebp')) { $img = imagecreatefromwebp($full); } break;
                }
                if ($img) {
                    $dst = imagecreatetruecolor($targetW, $targetH);
                    imagecopyresampled($dst, $img, 0,0,0,0, $targetW,$targetH, $w,$h);
                    imagejpeg($dst, $thumb, 80);
                    imagedestroy($dst);
                    imagedestroy($img);
                }
            }
        } catch (\Throwable $e) {}
        $order = ProfilePhoto::where('user_id', $request->user()->id)->max('order') + 1;
        $photo = ProfilePhoto::create(['user_id' => $request->user()->id, 'path' => $path, 'order' => $order]);
        return response()->json(['id' => $photo->id, 'path' => Storage::url($photo->path)]);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate(['orders' => ['required','array']]);
        foreach ($data['orders'] as $id => $order) {
            ProfilePhoto::where('id', $id)->where('user_id', $request->user()->id)->update(['order' => (int) $order]);
        }
        return response()->json(['ok' => true]);
    }

    public function delete(Request $request, $id)
    {
        $photo = ProfilePhoto::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        Storage::disk('public')->delete($photo->path);
        $photo->delete();
        return response()->json(['ok' => true]);
    }
}
