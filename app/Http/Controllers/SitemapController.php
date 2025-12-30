<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Discussion;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml
     */
    public function __invoke()
    {
        $discussions = Discussion::with('channel')
            ->latest('updated_at')
            ->get();

        $channels = Channel::latest('updated_at')->get();

        return response()->view('sitemap', [
            'discussions' => $discussions,
            'channels' => $channels,
        ])->header('Content-Type', 'text/xml');
    }
}
