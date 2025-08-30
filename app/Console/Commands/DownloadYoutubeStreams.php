<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DownloadYoutubeStreams extends Command
{
    protected $signature = 'youtube:download';
    protected $description = 'Download queued YouTube livestreams';

    public function handle()
    {
        $jobs = DB::table('youtube_jobs')->where('status', 'queued')->get();
        foreach ($jobs as $job) {
            $response = Http::get($job->download_url);
            $filename = storage_path('app/'.$job->filename);
            file_put_contents($filename, $response->body());
            DB::table('youtube_jobs')->where('id', $job->id)->update(['status' => 'done']);
            $this->info('Downloaded '.$job->filename);
        }
        return Command::SUCCESS;
    }
}

