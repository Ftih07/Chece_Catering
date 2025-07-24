<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visit;

class TrackVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        Visit::create([
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'url'         => $request->fullUrl(),
            'referrer'    => $request->headers->get('referer'),
            'path' => $request->path(), 
        ]);

        return $next($request);
    }
}
