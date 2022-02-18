<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUser
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
        if ($request->session()->has('user')) {
            $statusPerms = [
                'verificationJob',
                'verificationControl',
                'user',
                'verificationInfo',

            ];
            if ($request->session()->get('user')->status !== 31) {
                array_push($statusPerms, 'getContact', 'updateContact');
            }
            $permUserStatus = [0, 1, 31, 32, 11, 12];
            if ($request->session()->get('user')->status === 2) {
                return $next($request);
            } else if (in_array($request->route()->getActionMethod(), $statusPerms) && in_array($request->session()->get('user')->status, $permUserStatus)) {
                return $next($request);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'error_key' => 'login',
                    'message' => __('api_messages.user_check_fail_message')
                ]);
            }

        } else {
            return response()->json([
                'status' => 'fail',
                'error_key' => 'login',
                'message' => __('api_messages.user_check_fail_message')
            ]);
        }
    }
}
