<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePatronSelf
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $patron = $request->route('patron');

        if ($user->role === 'admin') {
            return $next($request);
        }

        if ($patron->id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
