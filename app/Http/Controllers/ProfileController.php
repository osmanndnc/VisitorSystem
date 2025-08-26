<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * ProfileController
 *
 * SRP (Single Responsibility Principle):
 *  - Profil sayfasını göstermek, profil bilgilerini güncellemek ve hesabı silmekten sorumludur.
 *  - Doğrulama ProfileUpdateRequest ve Laravel validator mekanizmalarına delege edilir.
 */
class ProfileController extends Controller
{
    /**
     * Profil düzenleme formunu görüntüler.
     * GET -> /profile
     */
    public function edit(Request $request): View
    {
        // View yalnızca oturumdaki kullanıcıyı alır.
        return view('profile.edit', ['user' => $request->user()]);
    }

    /**
     * Kullanıcının profil bilgilerini günceller.
     * PATCH -> /profile
     *
     * Akış:
     *  1) Request validasyon (ProfileUpdateRequest::validated())
     *  2) Modeli doldur (fill)
     *  3) E-posta değişmişse doğrulamayı sıfırla (email_verified_at = null)
     *  4) Kaydet ve geri dön
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user      = $request->user();
        $validated = $request->validated(); // kaynak: mevcut akış validasyon kullanıyor:contentReference[oaicite:8]{index=8}

        $user->fill($validated);

        // Kaynakta e-posta değişiminde verify reset mantığı var, korunur
        if ($user->isDirty('email')) {
            $user->email_verified_at = null; //:contentReference[oaicite:9]{index=9}
        }

        $user->save(); //:contentReference[oaicite:10]{index=10}

        // UI için durum anahtarı
        return Redirect::route('profile.edit')->with('status', 'profile-updated'); //:contentReference[oaicite:11]{index=11}
    }

    /**
     * Kullanıcının hesabını kalıcı olarak siler.
     * DELETE -> /profile
     *
     * Güvenlik:
     *  - current_password kuralı ile parola doğrulaması yapılır.
     *  - Çıkış + session invalidate + CSRF token yenileme.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Parola doğrulaması (mevcut akışta var):contentReference[oaicite:12]{index=12}
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();         //:contentReference[oaicite:13]{index=13}
        $user->delete();        //:contentReference[oaicite:14]{index=14}

        // Oturumu güvenle kapat
        $request->session()->invalidate();   //:contentReference[oaicite:15]{index=15}
        $request->session()->regenerateToken();

        return Redirect::to('/');            //:contentReference[oaicite:16]{index=16}
    }
}
