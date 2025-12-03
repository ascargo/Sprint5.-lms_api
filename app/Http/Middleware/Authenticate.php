<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Return null to avoid redirecting API clients to a web login route.
     */
    protected function redirectTo($request): ?string
    {
        return null;
    }

    /**
     * Throw a JSON-friendly 401 instead of redirecting.
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $this->redirectTo($request)
        );
    }
}
