<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // ambil token dan masukkan ke variabel $token
        $jwt = $request->bearerToken();

        // cek jika jwt null atau kosong
        if ($jwt == 'null' || $jwt == '') {
            // jika ya maka response ini muncul
            return response()->json([
                'msg' => 'Akses ditolak, token tidak memenuhi'
            ], 401);
        } else {
            // decode token
            $jwtDecoded = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));

            // periksa peran pengguna
            $role = $jwtDecoded->role;

            // dapatkan jalur dan metode permintaan
            $path = $request->path();
            $method = $request->method();

            // tentukan akses berdasarkan peran dan endpoint
            if ($role == 'admin') {
                return $next($request);

            } elseif ($role == 'user') {
                // user hanya memiliki akses CRUD untuk products
                if (preg_match('/^products(\/\d+)?$/', $path)) {
                    return $next($request);
                } else {
                    return response()->json([
                        'msg' => 'Akses ditolak, user tidak memiliki izin untuk mengakses endpoint ini'
                    ], 403);
                }
            } else {
                // jika peran tidak dikenal
                return response()->json([
                    'msg' => 'Akses ditolak, peran tidak dikenali'
                ], 401);
            }
        }
    }
}