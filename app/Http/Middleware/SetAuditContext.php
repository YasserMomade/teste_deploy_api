<?php

namespace App\Http\Middleware;

use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;

class SetAuditContext
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user()) {
            AuditLogService::setContext($request->user(), $request);
        }

        $response = $next($request);

        // Limpa o contexto depois que o request termina
        AuditLogService::clearContext();

        return $response;
    }
}