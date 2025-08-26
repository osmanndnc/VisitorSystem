<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/**
 * VerifyEmailController
 *
 * SRP:
 *  - E-posta doğrulama linki ile gelen isteğin HTTP akışını yönetir.
 *  - Kimlik/İmza kontrolü EmailVerificationRequest tarafından yapılır.
 *
 * Not:
 *  - Invokable (tek aksiyon) controller, route’ta doğrudan sınıf olarak kullanılabilir.
 */
class VerifyEmailController extends Controller
{
    /**
     * Auth kullanıcısının e-postasını doğrular.
     * GET -> /email/verify/{id}/{hash}
     *
     * Akış:
     *  1) Zaten doğrulanmışsa HOME’a yönlendir (idempotency).
     *  2) Değilse markEmailAsVerified() çağır ve Verified olayı tetikle.
     *  3) HOME’a “verified=1” parametresi ile yönlendir (UI için sinyal).
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
    }
}
