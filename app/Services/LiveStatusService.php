<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LiveStatusService
{
    public function checkChzzk(string $channelId): array
    {
        $url = config('services.chzzk.api_url')."/service/v1/channels/{$channelId}/live";
        $response = Http::get($url)->json();
        $status = data_get($response, 'content.status');
        return [
            'is_live' => $status === 'ON_AIR',
            'title' => data_get($response, 'content.liveTitle'),
            'started_at' => data_get($response, 'content.openDate'),
        ];
    }

    public function checkYoutube(string $channelId, string $apiKey): array
    {
        $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
            'part' => 'snippet',
            'channelId' => $channelId,
            'eventType' => 'live',
            'type' => 'video',
            'key' => $apiKey,
        ])->json();
        $items = data_get($response, 'items', []);
        if (count($items) > 0) {
            $item = $items[0];
            return [
                'is_live' => true,
                'title' => data_get($item, 'snippet.title'),
                'started_at' => data_get($item, 'snippet.publishedAt'),
            ];
        }
        return [
            'is_live' => false,
            'title' => null,
            'started_at' => null,
        ];
    }
}
