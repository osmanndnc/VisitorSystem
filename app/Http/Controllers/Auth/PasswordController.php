<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * PasswordController
 *
 * SRP:
 *  - Oturum açmış kullanıcının parolasını güncelleme akışını yönetir.
 *  - Doğrulama Laravel validasyon kurallarıyla yapılır, iş mantığı modele delege edilir.
 *
 * Güvenlik:
 *  - current_password kuralı ile mevcut parola doğrulanır.
 *  - Yeni parola için framework’ün parola politikası (Password::defaults) kullanılır.
 */
class PasswordController extends Controller
{
    /**
     * Kullanıcının parolasını günceller.
     * PATCH -> /profile/password
     *
     * Adımlar:
     *  1) current_password ile mevcut parolanın doğrulanması.
     *  2) Yeni parolanın politika + confirmed ile doğrulanması.
     *  3) Hash edip modele yazma.
     *  4) Başarı durumunda durum mesajı ile geri dönme.
     */
    public function update(Request $request): RedirectResponse
    {
        // 1-2) Validasyon (FormRequest'e de ayrılabilir)
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', PasswordRule::defaults(), 'confirmed'],
        ]);

        // 3) Şifreyi güvenli bir şekilde güncelle
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // 4) Kullanıcı deneyimi: status anahtarı UI'da toast/alert için kullanılabilir
        return back()->with('status', 'password-updated');
    }
}
