<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function PHPUnit\Framework\isEmpty;

class IfDoesntVerifiedEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user->email_verified_at){
            return redirect()->route('verificar-email.index');
        }

        return $next($request);
    }
}
