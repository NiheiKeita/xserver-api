<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 認証済みの場合はトップページにリダイレクト
        if (Auth::check()) {
            return response()->redirectTo('/');
        }
        return $next($request);
    }
}
