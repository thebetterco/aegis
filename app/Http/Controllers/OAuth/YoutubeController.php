<?php

namespace App\Http\Controllers\OAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class YoutubeController extends Controller
{
    public function redirect()
    {
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.youtube.client_id'),
            'redirect_uri' => route('oauth.youtube.callback'),
            'scope' => 'https://www.googleapis.com/auth/youtube.readonly',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);
        return redirect('https://accounts.google.com/o/oauth2/auth?'.$query);
    }

    public function callback(Request $request)
    {
        $code = $request->query('code');
        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.youtube.client_id'),
            'client_secret' => config('services.youtube.client_secret'),
            'code' => $code,
            'redirect_uri' => route('oauth.youtube.callback'),
        ]);
        $tokens = $tokenResponse->json();

        $profile = Http::withToken($tokens['access_token'])
            ->get('https://www.googleapis.com/youtube/v3/channels', [
                'mine' => 'true',
                'part' => 'snippet',
            ])->json();

        $item = $profile['items'][0]['snippet'] ?? [];

        $user = User::firstOrCreate(
            ['youtube_id' => $profile['items'][0]['id'] ?? null],
            ['email' => null]
        );
        $user->youtube_channel_name = $item['title'] ?? null;
        $user->youtube_access_token = $tokens['access_token'];
        $user->youtube_refresh_token = $tokens['refresh_token'] ?? null;
        $user->save();

        Auth::login($user);
        return redirect('/');
    }
}
