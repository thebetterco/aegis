<?php

namespace App\Http\Controllers\OAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class ChzzkController extends Controller
{
    public function redirect()
    {
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.chzzk.client_id'),
            'redirect_uri' => route('oauth.chzzk.callback'),
            'scope' => 'profile channel:read channel:chat',
        ]);
        return redirect(config('services.chzzk.auth_url').'?'.$query);
    }

    public function callback(Request $request)
    {
        $code = $request->query('code');
        $tokenResponse = Http::asForm()->post(config('services.chzzk.token_url'), [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.chzzk.client_id'),
            'client_secret' => config('services.chzzk.client_secret'),
            'code' => $code,
            'redirect_uri' => route('oauth.chzzk.callback'),
        ]);
        $tokens = $tokenResponse->json();

        $profile = Http::withToken($tokens['access_token'])
            ->get(config('services.chzzk.api_url').'/users/me')
            ->json();

        $user = User::firstOrCreate(
            ['chzzk_id' => $profile['id']],
            ['email' => $profile['email'] ?? null]
        );
        $user->chzzk_channel_name = $profile['channel_name'] ?? null;
        $user->chzzk_access_token = $tokens['access_token'];
        $user->chzzk_refresh_token = $tokens['refresh_token'];
        $user->save();

        Auth::login($user);
        return redirect('/');
    }
}
