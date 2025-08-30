<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OAuth\ChzzkController;
use App\Http\Controllers\OAuth\YoutubeController;
use App\Http\Controllers\NaverCommerceController;
use App\Http\Controllers\ChzzkStreamController;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-email/{token}', [AuthController::class, 'verify'])->name('verify.email');

Route::get('/oauth/chzzk', [ChzzkController::class, 'redirect'])->name('oauth.chzzk');
Route::get('/oauth/chzzk/callback', [ChzzkController::class, 'callback'])->name('oauth.chzzk.callback');
Route::get('/oauth/youtube', [YoutubeController::class, 'redirect'])->name('oauth.youtube');
Route::get('/oauth/youtube/callback', [YoutubeController::class, 'callback'])->name('oauth.youtube.callback');

Route::middleware(['auth'])->group(function () {
    Route::get('/chzzk/streams', [ChzzkStreamController::class, 'index']);
    Route::get('/chzzk/streams/{filename}', [ChzzkStreamController::class, 'show']);
    Route::get('/naver-commerce', [NaverCommerceController::class, 'dashboard'])->middleware('superadmin');
});
