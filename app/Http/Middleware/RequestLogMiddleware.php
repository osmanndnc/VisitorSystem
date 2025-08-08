<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

// Tüm requestleri loglama

class RequestLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::channel('request')->info('REQ_IN', [
            'method' => $request->method(),
            'path'   => $request->path(),
            'inputs' => $this->mask($request->except(['password','password_confirmation'])),
        ]);

        $response = $next($request);

        Log::channel('request')->info('REQ_OUT', [
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }

    private function mask(array $data): array
    {
        foreach (['tc_no','phone'] as $k) {
            if (isset($data[$k])) $data[$k] = $this->maskValue($data[$k]);
        }
        return $data;
    }

    private function maskValue($v): string
    {
        $s = (string) $v; $n = strlen($s);
        return $n <= 4 ? str_repeat('*', $n) : substr($s,0,2) . str_repeat('*',$n-4) . substr($s,-2);
    }
}
