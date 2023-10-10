<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)//: Response
    {
        if (!auth('sanctum')->user()->is_admin) {
            return response(null, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        return $next($request);
    }
}
