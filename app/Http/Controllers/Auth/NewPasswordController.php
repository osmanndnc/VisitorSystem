<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * NewPasswordController
 *
 * SRP (Single Responsibility Principle):
 *  - Şifre sıfırlama ekranını göstermek ve yeni şifre talebini işlemekle sorumlu.
 *  - Kimlik doğrulama/iş kuralları Password broker ve event sistemi üzerinden yürür.
 *
 * Akış Özeti:
 *  - create(): Token’lı reset formunu gösterir.
 *  - store(): Token + email + password validasyonu -> broker.reset(...) ile şifre güncelleme.
 */
class NewPasswordController extends Controller
{
    /**
     * Şifre sıfırlama sayfasını (token ile) gösterir.
     * GET -> /reset-password/{token}
     */
    public function create(Request $request): View
    {
        // View, token ve email gibi parametreleri Request içinden alabilir.
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Yeni şifre talebini işler.
     * POST -> /reset-password
     *
     * Adımlar:
     *  1) Giriş doğrulaması (token, email, password[confirmed + policy]).
     *  2) Password::reset(...) ile broker üzerinden şifre güncelle.
     *  3) Başarılı ise PasswordReset event'i tetiklenir ve login sayfasına yönlendirilir.
     *  4) Hata durumunda, email alanı korunarak geri dönülür.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1) Validasyon – parola politikası framework'ün varsayılan kurallarını kullanır
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2) Broker aracılığıyla reset işlemi
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                // Kullanıcının parolasını güvenli şekilde güncelle
                $user->forceFill([
                    'password'       => Hash::make($request->string('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                // Bildirim/izleme için Laravel olayı
                event(new PasswordReset($user));
            }
        );

        // 3) Sonuç: başarılıysa login sayfasına, değilse aynı sayfaya dön
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
