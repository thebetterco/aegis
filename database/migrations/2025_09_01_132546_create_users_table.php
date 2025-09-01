<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('email_verified')->default(false);
            $table->string('verification_token', 60)->nullable();
            $table->boolean('is_superadmin')->default(false);
            $table->string('chzzk_channel_name')->nullable();
            $table->string('chzzk_access_token')->nullable();
            $table->string('chzzk_refresh_token')->nullable();
            $table->string('chzzk_id')->nullable();
            $table->string('youtube_id')->nullable();
            $table->string('youtube_channel_name')->nullable();
            $table->string('youtube_access_token')->nullable();
            $table->string('youtube_refresh_token')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
