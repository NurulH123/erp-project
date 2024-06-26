<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (is_null($user->company) && $user->is_owner) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Maaf Anda Belum Mendaftarkan Perusahaan Anda'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
