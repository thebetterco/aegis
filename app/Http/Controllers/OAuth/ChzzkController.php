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
		$state = $request->query('state');
		$tokenParams = [
            'grantType' => 'authorization_code',
            'clientId' => config('services.chzzk.client_id'),
            'clientSecret' => config('services.chzzk.client_secret'),
            'code' => $code,
			'state' => $state,
            //'redirectUri' => route('oauth.chzzk.callback'),
		];
		//print_r($tokenParams);
        $tokenResponse = Http::withHeaders(
				[
					'headers' => [
						'Client-Id' => config('services.chzzk.client_id'),
						'Client-Secret' => config('services.chzzk.client_secret'),
						'response-type' => 'application/json',
					]
				]
			)->post(
				config('services.chzzk.token_url'), 
				$tokenParams
			);
        $tokenResponse = $tokenResponse->json();
		//print_r($tokenResponse);
		$tokens = $tokenResponse['content'];
		/*
		$cid = config('services.chzzk.client_id');
		$cs = config('services.chzzk.client_secret');


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Client-Id: '.$cid,
			'Client-Secret: '.$cs,
			'Content-type: application/json',
		]);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tokenParams));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, config('services.chzzk.token_url'));
		$ret = curl_exec($ch);

		var_dump($ret);
		print_r(curl_getinfo($ch));
		*/

        $profile = Http::withToken($tokens['accessToken'])
            ->get(config('services.chzzk.api_url').'/open/v1/users/me')
            ->json()['content'];
		print_r($profile);

        $user = User::firstOrCreate(
            ['chzzk_id' => $profile['id']],
            [
                'email' => $profile['email'] ?? null,
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
