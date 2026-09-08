<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Route-level RBAC gate, per discovery.md §3. Usage: ->middleware('role:Super Administrator,A&R / Artist Manager')
// This enforces "which roles may reach this route at all" — the coarse dimension of
// the §3 matrix. Where the matrix also restricts to "own records only" (e.g. an
// Artist viewing their own royalty statements), that scoping happens in the
// controller against $request->user(), not here.
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole(...$roles)) {
            return response()->json(['message' => 'Forbidden — insufficient role.'], 403);
        }

        return $next($request);
    }
}
