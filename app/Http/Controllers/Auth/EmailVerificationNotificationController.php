<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * EmailVerificationNotificationController
 *
 * SRP:
 *  - Yalnızca "e-posta doğrulama linkini yeniden gönder" talebini ele alır.
 *  - Kullanıcının doğrulanmış olup olmadığı kontrol edilir, idempotent davranış korunur.
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Yeni bir e-posta doğrulama bildirimi gönderir.
     * POST -> /email/verification-notification
     *
     * Not: Rate-limiting genelde middleware ile yapılır (ör. throttle:6,1).
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            // Zaten doğrulanmışsa normal akışa yönlendir.
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Laravel'in yerleşik bildirimini kullan: queue/notification kanallarını otomatik kullanır.
        $request->user()->sendEmailVerificationNotification();

        // UI tarafında okunabilir bir durum anahtarı
        return back()->with('status', 'verification-link-sent');
    }
}
