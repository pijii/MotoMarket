<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, string $role, string ...$otherRoles): Response
    {
        abort_unless($request->user() && in_array($request->user()->role, [$role, ...$otherRoles], true), 403);

        return $next($request);
    }
}