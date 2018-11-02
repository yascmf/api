<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

/**
 * Middleware setting response header
 *
 * @author raoyc
 */
class RequestIdentifierMiddleware
{
    public function handle($request, Closure $next)
    {
        global $app;
        $app->requestId = $request->requestId = strtoupper(str_random(8));
        try {
            $response = $next($request);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        $content = $response->getOriginalContent();
        // $response->headers->set('X-Author', 'raoyc', false);
        $response->headers->set('X-Request-Id', $request->requestId, false);
        if ($response instanceof Response) {
            if (is_array($content)) {
                $content['x-request-id'] = $app->requestId;
                $response->setContent($content);
            }
        }
        return $response;
    }
}