<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

/**
 * AuthenticatedSessionController
 *
 * Sorumluluk (SRP - Single Responsibility):
 * - Sadece HTTP işlemleriyle ilgilenir: login ekranını gösterir, login/logout işlemlerini yapar.
 * - Doğrulama, yetkilendirme, yönlendirme ve session/cookie işlemleri burada sade, düzenli şekilde yer alır.
 *
 * Bağımlılık Ayrımı (DIP - Dependency Inversion):
 * - LoginRequest içinde kimlik doğrulama (authenticate) soyutlanmıştır.
 * - Cookie, Session gibi bağımlılıklar Laravel façade’leri üzerinden yönetilir.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Login sayfasını görüntülemek için kullanılır.
     *
     * @return View
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Kullanıcı giriş isteğini işler.
     *
     * Akış:
     *  1) LoginRequest üzerinden doğrulama ve kimlik kontrolü yapılır.
     *  2) Kullanıcı pasifse oturum sonlandırılır ve hata gösterilir.
     *  3) Remember me işaretliyse cookie’ye kullanıcı adı ve şifre geçici olarak yazılır.
     *  4) Session ID güvenlik için yenilenir.
     *  5) Rol bazlı hedef sayfaya yönlendirilir.
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Kimlik doğrulama işlemini yap
        $request->authenticate();

        // 2. Giriş yapan kullanıcıyı al
        $user = auth()->user();

        // 3. Eğer kullanıcı pasifse logout yap ve hata dön
        if (!$user || !$user->is_active) {
            Auth::guard('web')->logout();

            return redirect()
                ->route('login')
                ->withErrors(['username' => 'Şu an aktif bir kullanıcı değilsiniz.'])
                ->withInput($request->only('username', 'remember'));
        }

        // 4. Session fixation önlemek için oturum ID'sini yenile
        $request->session()->regenerate();

        // 5. "Beni hatırla" işaretliyse çerezlere username ve password kaydet (30 gün)
        if ($request->filled('remember')) {
            Cookie::queue('remember_username', $request->username, 60 * 24 * 30); // 30 gün
            Cookie::queue('remember_password', $request->password, 60 * 24 * 30); // 30 gün
        } else {
            // İşaretli değilse çerezleri temizle
            Cookie::queue(Cookie::forget('remember_username'));
            Cookie::queue(Cookie::forget('remember_password'));
        }

        // 6. Başarılı girişten sonra rol bazlı yönlendirme yap
        return redirect()->intended(RouteServiceProvider::redirectToBasedOnRole());
    }

    /**
     * Kullanıcının oturumunu sonlandırır.
     *
     * Akış:
     *  1) Guard üzerinden logout işlemi yapılır.
     *  2) Session ve CSRF token'ı sıfırlanır.
     *  3) Login sayfasına yönlendirilir.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'Oturum kapatıldı.');
    }
}
