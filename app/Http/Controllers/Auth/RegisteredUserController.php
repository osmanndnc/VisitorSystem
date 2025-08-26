<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * RegisteredUserController
 *
 * SRP (Single Responsibility Principle):
 *  - Yalnızca kayıt formunu göstermek ve gelen kayıt isteğini işlemekten sorumludur.
 *  - Doğrulama framework katmanına (validator) delege edilir, veri kalıcılığı modele aittir.
 *
 * Temiz akış:
 *  - create(): Kayıt formunu gösterir.
 *  - store(): Validasyon → kullanıcı oluştur → Registered event → oturum aç → HOME’a yönlendir.
 */
class RegisteredUserController extends Controller
{
    /**
     * Kayıt formunu gösterir.
     * GET -> /register
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Kayıt isteğini işler.
     * POST -> /register
     *
     * Adımlar:
     *  1) Form girişlerini doğrula (isim, email benzersizliği, parola politikası).
     *  2) Kullanıcı modelini oluştur ve parolayı hash’le.
     *  3) Registered olayı tetikle (dinleyiciler e-posta vb. işlemleri yapabilir).
     *  4) Kullanıcıyı otomatik giriş yap.
     *  5) Ana sayfaya (intended/HOME) yönlendir.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1) Validasyon (istersen FormRequest’e ayrılabilir)
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2) Kalıcılaştırma – parolayı daima hash’leyerek sakla
        $user = User::create([
            'name'     => $request->string('name'),
            'email'    => $request->string('email'),
            'password' => Hash::make($request->string('password')),
        ]);

        // 3) Domain olayları – dinleyicilerin tetiklenmesi için
        event(new Registered($user));

        // 4) Oturum aç
        Auth::login($user);

        // 5) Rol/guard akışına uygun hedefe yönlendir
        return redirect(RouteServiceProvider::HOME);
    }
}
