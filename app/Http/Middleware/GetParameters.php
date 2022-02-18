<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GetParameters
{
    protected $user;

    public function __construct()
    {
        if (session()->has('user')) {
            $this->user = session()->get('user');
        } else {
            $this->user = false;
        }
    }

    public function handle(Request $request, Closure $next)
    {
        if ($request->filled('referer')) {
            session(['referer' => $request->referer]);
        } else if ($request->filled('locked')) {
            if (!empty($user = \App\Models\User::with('contacts')
                ->whereHas('contacts', function ($q) use ($request) {
                    $q->where('deger', $request->locked);
                })->first())) {
                $user->islem_kilidi = now()->tz('Europe/Istanbul')->subDays(7)->toDateTimeString();
                $user->islem_kilidi_sebebi = "Kendi isteğinizle hesabınızı kilitlediniz! Kilidi açmak için bize ulaşın.";
                $user->save();
                return response()->view('message', [
                    'message' => 'Kendi isteğinizle hesabınızı kilitlediniz! Kilidi açmak için bize ulaşın.',
                ]);
            }
        } else if ($request->filled('code') && $this->user) {
            $bot = new \App\Http\Controllers\Api\AuthController();
            $bot->verificationControl($request, $this->user);
            return redirect()->route('home', 'wallets');
        }
        return $next($request);
    }
}
