<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
        }

        if ($request->user()->role !== $role) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này!');
        }

        return $next($request);
    }
} 