<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use MongoDB\Client as MongoClient;

class ChzzkStreamController extends Controller
{
    public function index()
    {
        $streams = DB::table('chzzk_jobs')->where('status', 'done')->get();
        return view('chzzk.streams', ['streams' => $streams]);
    }

    public function show($filename)
    {
        $video = asset('storage/'.$filename);

        $client = new MongoClient(env('MONGO_URI'));
        $collection = $client->selectDatabase('chzzk')->selectCollection('logs');
        $cursor = $collection->find(['filename' => $filename], [
            'projection' => ['timestamp' => 1, 'type' => 1],
            'sort' => ['timestamp' => 1],
        ]);

        $timeline = [];
        foreach ($cursor as $doc) {
            $sec = (int) floor($doc['timestamp']);
            if (!isset($timeline[$sec])) {
                $timeline[$sec] = ['chat' => 0, 'donation' => 0];
            }
            if ($doc['type'] === 'chat') {
                $timeline[$sec]['chat']++;
            } elseif ($doc['type'] === 'donation') {
                $timeline[$sec]['donation']++;
            }
        }

        ksort($timeline);
        $timelineData = [];
        foreach ($timeline as $sec => $counts) {
            $timelineData[] = ['time' => $sec, 'chat' => $counts['chat'], 'donation' => $counts['donation']];
        }

        return view('chzzk.playback', [
            'filename' => $filename,
            'video' => $video,
            'timeline' => $timelineData,
        ]);
    }
}
