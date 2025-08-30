<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DownloadChzzkStreams extends Command
{
    protected $signature = 'chzzk:download';
    protected $description = 'Download queued Chzzk livestreams';

    public function handle()
    {
        $jobs = DB::table('chzzk_jobs')->where('status', 'queued')->get();
        foreach ($jobs as $job) {
            $response = Http::get($job->download_url);
            $filename = storage_path('app/'.$job->filename);
            file_put_contents($filename, $response->body());
            DB::table('chzzk_jobs')->where('id', $job->id)->update(['status' => 'done']);
            $this->info('Downloaded '.$job->filename);
        }
        return Command::SUCCESS;
    }
}
