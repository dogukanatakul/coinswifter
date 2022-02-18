<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('user')) {
            $user = $request->session()->get('user');
            $user = (object)[
                'id' => $user->status === 'new_device' ? null : $user->id ?? null
            ];
        } else {
            $user = (object)[
                'id' => null
            ];
        }
        \App\Helpers\LogActivity::addToLog(['user' => $user->id, 'status_text' => $request->getContent()]);
        return $next($request);
    }
}
