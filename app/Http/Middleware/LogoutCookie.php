<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LogoutCookie
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Cache::has('signout')) {
            $x = [
                session()->get('user')->id,
                $request->ip(),
                $request->header('user-agent'),
            ];
            if (in_array(explode('|', Cache::get('signout'))[0], $x) && in_array(explode('|', Cache::get('signout'))[1], $x) && in_array(explode('|', Cache::get('signout'))[2], $x)) {
                session()->forget('user');
                \App\Models\LogActivity::where('id', explode('|', Cache::get('signout'))[3])->delete();
                Cache::forget('signout');
                return response()->json([
                    'status' => 'fail',
                    'error_key' => 'login',
                    'message' => __('api_messages.user_check_fail_message')
                ]);
            } else {
                return $next($request);
            }
        }
        return $next($request);
    }
}
