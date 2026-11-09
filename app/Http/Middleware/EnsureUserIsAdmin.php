<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->user()?->role, [UserRole::SuperAdmin, UserRole::Admin], true)) {
            abort(403, 'Area ini hanya untuk admin Infoscreening.');
        }

        return $next($request);
    }
}
