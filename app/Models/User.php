<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'email',
        'password',
        'email_verified',
        'verification_token',
        'is_superadmin',
        'chzzk_channel_name',
        'chzzk_access_token',
        'chzzk_refresh_token',
        'chzzk_id',
        'youtube_id',
        'youtube_channel_name',
        'youtube_access_token',
        'youtube_refresh_token',
        'profile_picture_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
