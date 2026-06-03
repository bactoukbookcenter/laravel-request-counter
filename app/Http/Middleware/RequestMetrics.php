<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestMetrics
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        // Database cache driver won't auto-create the key on increment — seed it once.
        Cache::add('request_count', 0, null);
        $count = (int) Cache::increment('request_count');

        $response = $next($request);

        $duration = round((microtime(true) - $start) * 1000, 3);

        Log::info('request_handled', [
            'method'      => $request->method(),
            'path'        => '/' . ltrim($request->path(), '/'),
            'status'      => $response->getStatusCode(),
            'duration_ms' => $duration,
            'count'       => $count,
        ]);

        // Only annotate the active OTel span in production (where the extension is loaded).
        if (extension_loaded('opentelemetry')) {
            \OpenTelemetry\API\Trace\Span::getCurrent()
                ->setAttribute('app.request_count', $count)
                ->setAttribute('app.duration_ms', $duration);
        }

        return $response;
    }
}
