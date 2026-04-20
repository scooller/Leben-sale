<?php

namespace App\Http\Middleware;

use App\Models\FrontendPreviewLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCatalogPreviewOrAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        if (Auth::guard('sanctum')->check()) {
            return $next($request);
        }

        if (FrontendPreviewLink::isAuthorizedForRequest($request)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthenticated.',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
