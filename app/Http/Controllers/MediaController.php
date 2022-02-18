<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function getMedia($uuid)
    {
        if (Cache::has($uuid)) {
            $cache = Cache::get($uuid);
            Cache::forget($uuid);
            return response()->make($cache['file'], 200)->header("Content-Type", $cache['ext']);
        } else {
            abort(404);
        }

    }
}
