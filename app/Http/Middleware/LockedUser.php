<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class LockedUser
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
//        if ($request->session()->has('user')) {
//            $user = User::find(session()->get('user')->id);
//            if (!empty($user->islem_kilidi)) {
//                return response()->json([
//                    'status' => 'fail',
//                    'message' => $user->islem_kilidi_sebebi . "<br> Kısıtlama kalkış tarihi: <b>" . $user->islem_kilidi."</b>"
//                ]);
//            }
//
//        }
        return $next($request);
    }
}
