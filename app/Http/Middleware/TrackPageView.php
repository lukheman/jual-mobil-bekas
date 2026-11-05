<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackPageView
{
    /**
     * Record a public page visit after the response has been produced.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $this->record($request, $response);

        return $response;
    }

    private function record(Request $request, Response $response): void
    {
        if (! $request->isMethod('GET') || $request->ajax()) {
            return;
        }

        if (auth()->check()) {
            return;
        }

        $routeName = $request->route()?->getName();
        $path = trim($request->path(), '/');

        if ($path === '') {
            $path = '/';
        }

        if (
            $response->getStatusCode() >= 400
            || str_starts_with($path, 'admin')
            || $path === 'up'
            || str_starts_with($path, '_')
            || in_array($routeName, ['login', 'register'], true)
        ) {
            return;
        }

        if (! str_contains((string) $response->headers->get('Content-Type', ''), 'text/html')) {
            return;
        }

        $visitorHash = hash('sha256', implode('|', [
            $request->hasSession() ? $request->session()->getId() : '',
            (string) $request->ip(),
            (string) $request->userAgent(),
        ]));

        // Hindari menghitung refresh berulang dari pengunjung yang sama.
        try {
            $alreadyCounted = PageView::query()
                ->where('visitor_hash', $visitorHash)
                ->where('path', $path)
                ->where('created_at', '>=', now()->subMinutes(30))
                ->exists();

            if ($alreadyCounted) {
                return;
            }

            PageView::create([
                'path' => $path,
                'route_name' => $routeName,
                'visitor_hash' => $visitorHash,
                'ip_hash' => hash('sha256', (string) $request->ip()),
                'referrer' => mb_substr((string) $request->headers->get('referer'), 0, 255) ?: null,
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 255) ?: null,
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Statistik tidak boleh mengganggu pengalaman pengunjung.
        }
    }
}
