<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use MongoDB\Client as MongoClient;
use Illuminate\Http\Response;

class ChatController extends Controller
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
        DB::table('chat_sanctions')->insert([
            'user_id' => $userId,
            'type' => 'mute',
            'created_at' => now(),
        ]);
        return response()->json(['status' => 'muted']);
    }

    public function ban($userId)
    {
        DB::table('chat_sanctions')->insert([
            'user_id' => $userId,
            'type' => 'ban',
            'created_at' => now(),
        ]);
        return response()->json(['status' => 'banned']);
    }
}
