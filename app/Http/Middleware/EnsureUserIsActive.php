<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class EnsureUserIsActive
{
    use ApiResponse;

    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()) {
            return $this->error('Unauthenticated.', 401);
        }

        return $next($request);
    }
}