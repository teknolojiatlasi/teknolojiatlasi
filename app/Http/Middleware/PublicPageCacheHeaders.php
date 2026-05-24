<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicPageCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! $this->shouldCache($request, $response)) {
            return $response;
        }

        $etag = '"' . sha1((string) $response->getContent()) . '"';

        if ($request->headers->get('If-None-Match') === $etag) {
            $response->setNotModified();

            return $response;
        }

        $response->headers->set('ETag', $etag);
        $response->headers->set('Cache-Control', 'private, max-age=60, stale-while-revalidate=300');

        return $response;
    }

    private function shouldCache(Request $request, Response $response): bool
    {
        if (! $request->isMethodCacheable() || $request->user()) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html');
    }
}
