<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! is_production() && ! $request->header('Signature-Token')) {
            return $next($request);
        }

        $signature_token = sign_request($request->all(), config('app.api_secret_key'));

        if ($signature_token !== $request->header('Signature-Token')) {
            return response()->failWithMessage('Invalid signature token.');
        }

        return $next($request);
    }
}
