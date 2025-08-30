<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class QueueYoutubeStreams extends Command
{
    protected $signature = 'youtube:queue';
    protected $description = 'Fetch completed YouTube streams and enqueue them for download';

    public function handle()
    {
        $users = User::whereNotNull('youtube_access_token')->get();
        foreach ($users as $user) {
            $streams = Http::withToken($user->youtube_access_token)
                ->get('https://www.googleapis.com/youtube/v3/search', [
                    'channelId' => $user->youtube_id,
                    'type' => 'video',
                    'eventType' => 'completed',
                    'part' => 'id',
                    'maxResults' => 50,
                ])->json('items', []);
            foreach ($streams as $stream) {
                $vid = $stream['id']['videoId'] ?? null;
                if (!$vid) {
                    continue;
                }
                DB::table('youtube_jobs')->updateOrInsert(
                    ['video_id' => $vid],
                    [
                        'download_url' => 'https://www.youtube.com/watch?v='.$vid,
                        'filename' => $vid.'.mp4',
                        'status' => 'queued',
                    ]
                );
            }
        }
        $this->info('Queued YouTube streams.');
        return Command::SUCCESS;
    }
}

