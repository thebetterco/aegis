<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CheckLiveStreams;
use App\Console\Commands\QueueChzzkStreams;
use App\Console\Commands\QueueYoutubeStreams;
use App\Console\Commands\DownloadChzzkStreams;
use App\Console\Commands\DownloadYoutubeStreams;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CheckLiveStreams::class,
        QueueChzzkStreams::class,
        QueueYoutubeStreams::class,
        DownloadChzzkStreams::class,
        DownloadYoutubeStreams::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
