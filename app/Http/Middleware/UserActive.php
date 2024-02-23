<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user->status) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Akun Anda Tidak aktif'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
