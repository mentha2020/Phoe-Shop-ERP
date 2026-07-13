<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            !$request->secure() &&
            $request->header('X-Forwarded-Proto') !== 'https' &&
            !App::runningInConsole()
        ) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
