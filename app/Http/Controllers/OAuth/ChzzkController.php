<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ChzzkController extends Controller
{
    public function redirect()
    {
        $state = Str::random(40);
        Session::put('chzzkOauthState', $state);

        $query = http_build_query([
            'responseType' => 'code',
            'clientId' => config('services.chzzk.client_id'),
            'redirectUri' => route('oauth.chzzk.callback'),
            'scope' => 'profile channel:read channel:chat',
            'state' => $state,
        ]);

        return redirect(config('services.chzzk.auth_url').'?'.$query);
    }

    public function callback(Request $request)
    {
        if ($request->query('state') !== Session::pull('chzzkOauthState')) {
            abort(403, 'Invalid OAuth state');
        }

        $code = $request->query('code');
        $tokenResponse = Http::asForm()->post(config('services.chzzk.token_url'), [
            'grantType' => 'authorization_code',
            'clientId' => config('services.chzzk.client_id'),
            'clientSecret' => config('services.chzzk.client_secret'),
            'code' => $code,
            'redirectUri' => route('oauth.chzzk.callback'),
        ]);
        $tokens = $tokenResponse->json();

        $profile = Http::withToken($tokens['accessToken'])
            ->get(config('services.chzzk.api_url').'/users/me')
            ->json();

        $user = User::firstOrCreate(
            ['chzzk_id' => $profile['channelId']],
            [
                'email' => $profile['email'] ?? Str::uuid().'@example.com',
                'password' => Hash::make(Str::random(32)),
            ]
        );
        $user->chzzk_channel_name = $profile['channelName'] ?? null;
        $user->chzzk_access_token = $tokens['accessToken'];
        $user->chzzk_refresh_token = $tokens['refreshToken'] ?? null;
        $user->save();

        Auth::login($user);

        return redirect('/');
    }
}
