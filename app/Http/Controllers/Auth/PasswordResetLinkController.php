<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * PasswordResetLinkController
 *
 * Sorumluluk (SRP):
 *  - Yalnızca "Şifre sıfırlama bağlantısı gönder" akışının HTTP kısmını yönetir.
 *  - Doğrulama Laravel validator; e-posta gönderimi Password broker tarafından yapılır.
 *
 * Temiz Akış:
 *  - create(): "Şifremi unuttum" formunu gösterir.
 *  - store(): email doğrular, Password::sendResetLink(...) çağırır,
 *             başarılıysa status ile, değilse hata ile geri döner.
 *
 * Notlar:
 *  - Rate limiting için middleware (ör. throttle:6,1) route tarafında tanımlanmalıdır.
 *  - Gizlilik: Geri dönüşlerde yalnızca 'email' alanını withInput ile taşır.
 */
class PasswordResetLinkController extends Controller
{
    /**
     * Şifre sıfırlama bağlantısı talep formunu gösterir.
     * GET -> /forgot-password
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Şifre sıfırlama bağlantısı gönderim talebini işler.
     * POST -> /forgot-password
     *
     * Adımlar:
     *  1) 'email' alanını doğrula.
     *  2) Password broker ile reset linki gönder.
     *  3) Başarılıysa status mesajı, değilse hata mesajı ile geri dön.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1) Giriş doğrulaması
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // 2) Broker üzerinden reset linki gönder
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // 3) Sonuç: başarılıysa status ile, değilse hatayı göster
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
