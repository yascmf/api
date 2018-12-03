<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $origin = $request->header('Origin');
        $originStr = env('ACCESS_CONTROL_ALLOW_ORIGIN', '*');
        $allowedOrigins = explode(',', $originStr);
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS, HEAD',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With, Origin, X-Auth-Token, App-Version, App-Root-Key',
        ];
        if (is_array($allowedOrigins) && (count($allowedOrigins) > 0) && in_array($origin, $allowedOrigins)) {
            $headers['Access-Control-Allow-Origin'] = $origin;
        }
        if ($request->isMethod('OPTIONS')) {
            return response()->json('', 204, $headers);
        }
        return $next($request)->withHeaders($headers);
    }

}
