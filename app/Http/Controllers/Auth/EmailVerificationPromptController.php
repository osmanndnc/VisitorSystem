<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * EmailVerificationPromptController
 *
 * SRP:
 *  - E-posta doğrulama gereksinimi için kullanıcıya prompt (bilgilendirme) gösterir
 *    veya kullanıcı doğrulandıysa hedef sayfaya yönlendirir.
 *
 * Not: __invoke kullanımı ile controller tek-aksiyonlu (invokable) hale getirilmiştir.
 */
class EmailVerificationPromptController extends Controller
{
    /**
     * E-posta doğrulama bilgilendirme ekranını gösterir ya da doğrulanmışsa yönlendirir.
     * GET -> /email/verify
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(RouteServiceProvider::HOME)
            : view('auth.verify-email');
    }
}
