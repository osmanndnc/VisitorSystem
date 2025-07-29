<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cookie;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Kullanıcı doğrulandıktan hemen sonra aktiflik durumunu kontrol edelim:
        if (!auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'username' => 'Şu an aktif bir kullanıcı değilsiniz.'
            ]);
        }

        // Beni Hatırla seçeneği işaretliyse kullanıcı adı ve şifreyi çereze kaydet
        if ($request->boolean('remember')) {
            // Çerez 1 hafta geçerli olacak
            Cookie::queue('remember_username', $request->input('username'), 60 * 24 * 7);
            Cookie::queue('remember_password', $request->input('password'), 60 * 24 * 7);
        } else {
            // Beni Hatırla seçeneği işaretli değilse çerezleri sil
            Cookie::queue(Cookie::forget('remember_username'));
            Cookie::queue(Cookie::forget('remember_password'));
        }

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::redirectToBasedOnRole());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Oturum kapatıldı.');
    }
}
