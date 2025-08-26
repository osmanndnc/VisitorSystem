<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * ConfirmablePasswordController
 *
 * SRP (Single Responsibility Principle):
 *  - Yalnızca "parola doğrulama" (sensitive action öncesi) ekranını gösterir ve doğrulama talebini işler.
 *  - İş kuralı / doğrulama Laravel guard & validation mekanizmasına delege edilir.
 */
class ConfirmablePasswordController extends Controller
{
    /**
     * Parola doğrulama sayfasını gösterir.
     * GET -> /user/confirm-password
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Parola doğrulamasını yapar ve başarılıysa hedefe yönlendirir.
     * POST -> /user/confirm-password
     *
     * Akış:
     *  1) Aktif kullanıcının e-posta + girilen parola ile guard::validate
     *  2) Başarısızsa ValidationException fırlatılır (standart Laravel davranışı)
     *  3) Başarılıysa session'a 'auth.password_confirmed_at' yazılır
     *  4) Intended (hedeflenen sayfa) ya da HOME yönlendirmesi
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = [
            'email'    => $request->user()->email,
            'password' => $request->string('password'),
        ];

        if (! Auth::guard('web')->validate($credentials)) {
            // Laravel'in standart "auth.password" çevirisiyle uyumlu hata
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        // Bu timestamp daha sonra "yakın zamanda parola doğrulandı mı?" kontrolü için kullanılır.
        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
