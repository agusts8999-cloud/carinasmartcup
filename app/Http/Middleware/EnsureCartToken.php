<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureCartToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasCookie('cart_token')) {
            Cookie::queue('cart_token', (string) Str::uuid(), 60 * 24 * 365);
        }

        return $next($request);
    }
}
