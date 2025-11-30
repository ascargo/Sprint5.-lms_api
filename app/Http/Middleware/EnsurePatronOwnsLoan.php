<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePatronOwnsLoan
{
    public function handle(Request $request, Closure $next): Response
    {
        $loan = $request->route('loan');
        $user = auth()->user();

        if ($user->role === 'admin') {
            return $next($request);
        }

        if ($loan->patron_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
