<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use MongoDB\Client as MongoClient;
use Illuminate\Http\Response;

class ChzzkChatController extends Controller
{
    public function userInfo($userId)
    {
        $mongo = new MongoClient(env('MONGO_URI'));
        $collection = $mongo->selectDatabase('chzzk')->selectCollection('chats');
        $cursor = $collection->find(['user_id' => $userId], [
            'sort' => ['timestamp' => -1],
            'limit' => 50,
        ]);

        $nicknames = [];
        $chats = [];
        foreach ($cursor as $doc) {
            $chats[] = ['message' => $doc['message'], 'timestamp' => $doc['timestamp']];
            if (!in_array($doc['nickname'], $nicknames)) {
                $nicknames[] = $doc['nickname'];
            }
        }

        $sanctions = DB::table('chat_sanctions')->where('user_id', $userId)->count();

        return response()->json([
            'nicknames' => $nicknames,
            'chats' => $chats,
            'sanctions' => $sanctions,
        ]);
    }

    public function mute($userId)
    {
        $user = Auth::user();
        $endpoint = config('services.chzzk.api_url')."/channels/{$user->chzzk_id}/chat/users/{$userId}/mute";
        $response = Http::withToken($user->chzzk_access_token)->post($endpoint);

        if ($response->successful()) {
            DB::table('chat_sanctions')->insert([
                'user_id' => $userId,
                'type' => 'mute',
                'created_at' => now(),
            ]);
            return response()->json(['status' => 'muted']);
        }

        return response()->json($response->json(), $response->status());
    }

    public function ban($userId)
    {
        $user = Auth::user();
        $endpoint = config('services.chzzk.api_url')."/channels/{$user->chzzk_id}/chat/users/{$userId}/ban";
        $response = Http::withToken($user->chzzk_access_token)->post($endpoint);

        if ($response->successful()) {
            DB::table('chat_sanctions')->insert([
                'user_id' => $userId,
                'type' => 'ban',
                'created_at' => now(),
            ]);
            return response()->json(['status' => 'banned']);
        }

        return response()->json($response->json(), $response->status());
    }
}
