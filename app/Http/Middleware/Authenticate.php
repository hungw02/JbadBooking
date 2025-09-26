<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Lưu URL hiện tại để chuyển hướng sau khi đăng nhập
            session()->put('url.intended', url()->current());
            return redirect()->route('login')->with('info', 'Vui lòng đăng nhập để tiếp tục');
        }

        return $next($request);
    }
}
