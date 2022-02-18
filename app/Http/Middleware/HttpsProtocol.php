<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HttpsProtocol
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if ((env('APP_ENV') != "local") and (!empty(env('APP_ENV')))) {
//            if (!strstr($request->header('host'), 'www.')) {
//                $host = "www." . $request->header('host');
//                $request->headers->set('host', $host);
//                return redirect()->secure($request->getRequestUri(), 301);
//            }
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "http") {
                return redirect()->secure($request->getRequestUri(), 302);
            }
        }
        return $next($request);
    }
}
