<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() && !$request->is('register') && !$request->is('login') && !$request->is('livewire/*')) {
            $user = User::where('email', 'victorhntf@gmail.com')->first();
            if ($user) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}
