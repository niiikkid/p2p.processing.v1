<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
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
        if (! is_production() && ! $request->header('Merchant-Token')) {
            return $next($request);
        }

        $token = $request->header('Merchant-Token');
        $merchant = Merchant::where('token', $token)->exists();

        if (! $merchant) {
            return response()->failWithMessage('Invalid merchant token.');
        }

        return $next($request);
    }
}
