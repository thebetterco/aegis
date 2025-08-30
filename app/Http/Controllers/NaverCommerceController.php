<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class NaverCommerceController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user || !$user->is_superadmin) {
            abort(403);
        }

        $response = Http::withHeaders([
            'X-Naver-Client-Id' => config('services.naver_commerce.client_id'),
            'X-Naver-Client-Secret' => config('services.naver_commerce.client_secret'),
        ])->get(config('services.naver_commerce.base_url').'/products');

        $products = $response->json();

        return view('naver_commerce', ['products' => $products]);
    }
}
