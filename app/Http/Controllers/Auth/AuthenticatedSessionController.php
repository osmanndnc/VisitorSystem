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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
        try {
            $request->authenticate(); // Auth::attempt()

            // Eğer kullanıcı aktif değilse
            if (!auth()->user()->is_active) {
                Log::warning('Pasif kullanıcı giriş yapmaya çalıştı.', [
                    'username' => auth()->user()->username,
                    'ip' => $request->ip(),
                    'time' => now(),
                ]);

                auth()->logout();

                return redirect()->route('login')->withErrors([
                    'username' => 'Şu an aktif bir kullanıcı değilsiniz.'
                ]);
            }

            // Başarılı giriş logu
            Log::info('Giriş başarılı.', [
                'user_id' => auth()->id(),
                'username' => auth()->user()->username,
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            // Beni hatırla çerez işlemleri
            if ($request->boolean('remember')) {
                Cookie::queue('remember_username', $request->input('username'), 60 * 24 * 7);
                Cookie::queue('remember_password', $request->input('password'), 60 * 24 * 7);
            } else {
                Cookie::queue(Cookie::forget('remember_username'));
                Cookie::queue(Cookie::forget('remember_password'));
            }

            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::redirectToBasedOnRole());

        } catch (ValidationException $e) {
            // Giriş başarısız logu
            Log::error('Giriş başarısız.', [
                'username' => $request->input('username'),
                'ip' => $request->ip(),
                'time' => now(),
                'reason' => 'Kullanıcı adı veya şifre hatalı.',
            ]);

            //Kendi hata mesajımızı gönderiyoruz.
            return redirect()->route('login')->withErrors([
                'username' => 'Kullanıcı adı veya şifre hatalı.',
            ])->withInput($request->only('username', 'remember'));
            //throw $e; // Laravel kendi hata gösterimini yapsın
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Kullanıcı çıkış logu
        Log::info('Kullanıcı çıkış yaptı.', [
            'user_id' => auth()->id(),
            'username' => auth()->user()->username ?? 'Bilinmiyor',
            'ip' => $request->ip(),
            'time' => now(),
        ]);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Oturum kapatıldı.');
    }
}
