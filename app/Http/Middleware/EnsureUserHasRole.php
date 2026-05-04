<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    use ApiResponse;

    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (! $user) {
            return $this->error('Unauthenticated.', 401);
        }

        if (! $user->hasAnyRole($roles)) {
            return $this->forbidden(
                'You do not have permission to access this resource.'
            );
        }

        return $next($request);
    }
}