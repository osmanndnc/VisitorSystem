<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        // Inline <script> blokları için nonce üretiyoruz 
        $nonce = base64_encode(random_bytes(16));
        app()->instance('csp-nonce', $nonce);

        $response = $next($request);

        // GENİŞ ama pratik politika: inline handler'lar ve yaygın CDN'ler çalışır
        $csp = [
            "default-src 'self' https:",

            // Görseller: sunucu + her https kaynağı + data/blob (base64/svgs)
            "img-src 'self' https: data: blob:",

            // CSS: inline stiller + her https kaynağı (Google Fonts CSS dahil)
            "style-src 'self' 'unsafe-inline' https:",

            // JS: inline <script> ve onclick gibi handler'lar için 'unsafe-inline' GEREKLİ
            // CDN'lerden (https) script yüklemeyi açıyoruz; nonce da eklendi
            "script-src 'self' 'unsafe-inline' 'nonce-{$nonce}' https:",

            // Font dosyaları: sunucu + https CDN + data:
            "font-src 'self' https: data:",

            // Fetch/HMR/WebSocket: self + tüm https ve ws/wss (lokal Vite dev server dahil)
            "connect-src 'self' https: ws: wss:",

            // Sayfamız başka siteye gömülmesin
            "frame-ancestors 'none'",

            // Raporları bu endpointe raporlar
            "report-uri /csp-report",
        ];

        return $response
            ->header('X-Frame-Options', 'DENY')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()')
            // Sadece Raporla 
            ->header('Content-Security-Policy-Report-Only', implode('; ', $csp)); 
    }
}
