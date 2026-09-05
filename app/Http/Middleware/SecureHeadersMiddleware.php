<?php

namespace App\Http\Middleware;
use Closure;

class SecureHeadersMiddleware
{

    private $unwantedHeaderList = [
        'X-Powered-By',
        'Server',
    ];
    public function handle($request, Closure $next)
    {
        $this->removeUnwantedHeaders($this->unwantedHeaderList);
        $response = $next($request);
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Strict-Transport-Security', 'max-age:31536000; includeSubDomains');
        $response->headers->set('Content-Security-Policy', "upgrade-insecure-requests");
        $response->headers->set('pragma', 'no-cache');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0, s-maxage=0');
        $response->headers->set('Expires', '0');
        return $response;
    }
    private function removeUnwantedHeaders($headerList)
    {
        foreach ($headerList as $header)
            header_remove($header);
    }
}
