<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class LiveStreamController extends Controller
{
    public function index()
    {
        $streams = DB::table('live_streams')->get();
        return view('live.streams', ['streams' => $streams]);
    }

    public function show($id)
    {
        $stream = DB::table('live_streams')->find($id);
        return view('live.manage', ['stream' => $stream]);
    }
}
