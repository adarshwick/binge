<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Conversation;
use App\Models\UserMatch;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $blockedOut = \App\Models\UserBlock::where('user_id', $user->id)->pluck('blocked_user_id')->toArray();
        $blockedIn = \App\Models\UserBlock::where('blocked_user_id', $user->id)->pluck('user_id')->toArray();
        $blockedIds = array_unique(array_merge($blockedOut, $blockedIn));
        $cacheKey = 'chat:list:user:'.$user->id;
        $conversations = \Illuminate\Support\Facades\Cache::remember($cacheKey, 10, function () use ($user) {
            $matches = UserMatch::query()
                ->where(function ($q) use ($user) {
                    $q->where('user_id_a', $user->id)->orWhere('user_id_b', $user->id);
                })
                ->get();
            return Conversation::whereIn('match_id', $matches->pluck('id'))
                ->get()
                ->map(function ($c) use ($user, $blockedIds) {
                    $m = UserMatch::find($c->match_id);
                    $otherId = $m->user_id_a === $user->id ? $m->user_id_b : $m->user_id_a;
                    if (in_array($otherId, $blockedIds)) return null;
                    $other = User::find($otherId);
                    $last = Message::where('conversation_id', $c->id)->latest()->first();
                    $unread = Message::where('conversation_id', $c->id)->where('sender_id','!=',$user->id)->whereNull('read_at')->count();
                    return [
                        'id' => $m->id,
                        'name' => $other->name,
                        'last' => $last?->content ?? '',
                        'unread' => $unread,
                    ];
                })->filter()->values();
        });
        return Inertia::render('App/Chat/Index', ['list' => $conversations]);
    }

    public function show(Request $request, $match_id)
    {
        $conversation = Conversation::where('match_id', $match_id)->first();
        if ($conversation) {
            $m = UserMatch::find($conversation->match_id);
            $user = $request->user();
            $otherId = $m->user_id_a === $user->id ? $m->user_id_b : $m->user_id_a;
            $blocked = \App\Models\UserBlock::where('user_id', $user->id)->where('blocked_user_id', $otherId)->exists()
                || \App\Models\UserBlock::where('user_id', $otherId)->where('blocked_user_id', $user->id)->exists();
            if ($blocked) abort(403);
        }
        if ($conversation) {
            Message::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', $request->user()->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            event(new \App\Events\MessagesRead($match_id, $request->user()->id));
        }
        $messages = collect();
        $hasMore = false;
        if ($conversation) {
            $batch = Message::where('conversation_id', $conversation->id)->orderByDesc('id')->limit(50)->get(['id','type','content','sender_id','delivered_at','read_at']);
            $messages = $batch->sortBy('id')->values();
            $minId = $batch->min('id');
            $hasMore = $minId ? Message::where('conversation_id', $conversation->id)->where('id', '<', $minId)->exists() : false;
        }
        $otherId = null;
        if ($conversation) {
            $m = UserMatch::find($conversation->match_id);
            $otherId = $m->user_id_a === $request->user()->id ? $m->user_id_b : $m->user_id_a;
        }
        return Inertia::render('App/Chat/Show', ['match_id' => $match_id, 'messages' => $messages, 'has_more' => $hasMore, 'other_id' => $otherId]);
    }

    public function send(Request $request, $match_id)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);
        $conversation = Conversation::where('match_id', $match_id)->firstOrFail();
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'type' => 'text',
            'content' => $validated['content'],
            'delivered_at' => now(),
        ]);
        $message = Message::where('conversation_id', $conversation->id)->latest()->first();
        event(new MessageSent($match_id, $message));
        $m = UserMatch::find($match_id);
        $receiverId = $m->user_id_a === $request->user()->id ? $m->user_id_b : $m->user_id_a;
        $tokens = DeviceToken::where('user_id', $receiverId)->pluck('token');
        $serverKey = optional(\App\Models\AppSetting::where('key','fcm_server_key')->first())->value;
        foreach ($tokens as $token) {
            if (! $serverKey) break;
            Http::withHeaders(['Authorization' => 'key '.$serverKey, 'Content-Type' => 'application/json'])
                ->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $token,
                    'notification' => ['title' => 'New message', 'body' => $message->content],
                    'data' => ['match_id' => $match_id],
                ]);
        }
        return redirect()->route('app.chat.show', ['match_id' => $match_id]);
    }

    public function typing(Request $request, $match_id)
    {
        event(new \App\Events\Typing($match_id, $request->user()->id));
        return response()->json(['ok' => true]);
    }

    public function uploadVoice(Request $request, $match_id)
    {
        $request->validate(['voice' => ['required','file','mimetypes:audio/mpeg,audio/mp4,audio/wav']]);
        $path = $request->file('voice')->store('voice_notes', 'public');
        $conversation = Conversation::where('match_id', $match_id)->firstOrFail();
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'type' => 'voice',
            'content' => \Illuminate\Support\Facades\Storage::url($path),
            'delivered_at' => now(),
        ]);
        $message = Message::where('conversation_id', $conversation->id)->latest()->first();
        event(new MessageSent($match_id, $message));
        return redirect()->route('app.chat.show', ['match_id' => $match_id]);
    }

    public function uploadImage(Request $request, $match_id)
    {
        $request->validate(['image' => ['required','file','mimes:jpeg,png,gif,webp']]);
        $path = $request->file('image')->store('chat_images', 'public');
        $url = \Illuminate\Support\Facades\Storage::url($path);
        $conversation = Conversation::where('match_id', $match_id)->firstOrFail();
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()->id,
            'type' => 'image',
            'content' => $url,
            'delivered_at' => now(),
        ]);
        $message = Message::where('conversation_id', $conversation->id)->latest()->first();
        event(new MessageSent($match_id, $message));
        if ($request->wantsJson()) {
            return response()->json(['url' => $url]);
        }
        return redirect()->route('app.chat.show', ['match_id' => $match_id]);
    }

    public function messages(Request $request, $match_id)
    {
        $conversation = Conversation::where('match_id', $match_id)->firstOrFail();
        $before = $request->query('before');
        $q = Message::where('conversation_id', $conversation->id)->orderByDesc('id');
        if ($before) $q->where('id', '<', (int) $before);
        $batch = $q->limit(50)->get(['id','type','content','sender_id','delivered_at','read_at']);
        $messages = $batch->sortBy('id')->values()->all();
        $minId = $batch->min('id');
        $hasMore = $minId ? Message::where('conversation_id', $conversation->id)->where('id', '<', $minId)->exists() : false;
        return response()->json(['messages' => $messages, 'has_more' => $hasMore]);
    }
}
