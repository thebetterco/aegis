<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class QueueChzzkStreams extends Command
{
    protected $signature = 'chzzk:queue';
    protected $description = 'Fetch completed Chzzk streams and enqueue them for download';

    public function handle()
    {
        $users = User::whereNotNull('chzzk_access_token')->get();
        foreach ($users as $user) {
            $vods = Http::withToken($user->chzzk_access_token)
                ->get(config('services.chzzk.api_url').'/streams', [
                    'channel_id' => $user->chzzk_id,
                    'status' => 'completed',
                ])->json('data', []);
            foreach ($vods as $vod) {
                DB::table('chzzk_jobs')->updateOrInsert(
                    ['stream_id' => $vod['id']],
                    [
                        'download_url' => $vod['download_url'] ?? '',
                        'filename' => ($vod['id'] ?? uniqid()).'.mp4',
                        'status' => 'queued',
                    ]
                );
            }
        }
        $this->info('Queued Chzzk streams.');
        return Command::SUCCESS;
    }
}

