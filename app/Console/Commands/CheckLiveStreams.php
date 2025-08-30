<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\LiveStatusService;

class CheckLiveStreams extends Command
{
    protected $signature = 'streams:check-live';
    protected $description = 'Check if authorized streams are currently live';

    protected LiveStatusService $service;

    public function __construct(LiveStatusService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            if (!empty($user->chzzk_id)) {
                $status = $this->service->checkChzzk($user->chzzk_id);
                DB::table('live_streams')->updateOrInsert(
                    ['user_id' => $user->id, 'platform' => 'chzzk'],
                    [
                        'title' => $status['title'],
                        'started_at' => $status['started_at'],
                        'status' => $status['is_live'] ? 'live' : 'offline',
                    ]
                );
            }
            if (!empty($user->youtube_id)) {
                $status = $this->service->checkYoutube(
                    $user->youtube_id,
                    config('services.youtube.api_key')
                );
                DB::table('live_streams')->updateOrInsert(
                    ['user_id' => $user->id, 'platform' => 'youtube'],
                    [
                        'title' => $status['title'],
                        'started_at' => $status['started_at'],
                        'status' => $status['is_live'] ? 'live' : 'offline',
                    ]
                );
            }
        }
        $this->info('Live status updated.');
        return Command::SUCCESS;
    }
}
