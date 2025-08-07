<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Giriş ekranını gösterir.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Giriş isteğini işler.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            $user = auth()->user();

            // Kullanıcı pasifse giriş iptal edilir.
            if (!$user->is_active) {
                Log::warning('Pasif kullanıcı giriş denemesi.', [
                    'username' => $user->username,
                    'ip' => $request->ip(),
                    'time' => now(),
                ]);

                auth()->logout();

                return redirect()->route('login')->withErrors([
                    'username' => 'Şu an aktif bir kullanıcı değilsiniz.'
                ]);
            }

            Log::info('Kullanıcı girişi başarılı.', [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            $this->handleRememberMe($request);

            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::redirectToBasedOnRole());

        } catch (ValidationException $e) {
            Log::error('Giriş başarısız.', [
                'username' => $request->input('username'),
                'ip' => $request->ip(),
                'time' => now(),
                'reason' => 'Kullanıcı adı veya şifre hatalı',
            ]);

            return redirect()->route('login')->withErrors([
                'username' => 'Kullanıcı adı veya şifre hatalı.',
            ])->withInput($request->only('username', 'remember'));
        }
    }

    /**
     * Oturumu sonlandırır.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();

        Log::info('Kullanıcı çıkış yaptı.', [
            'user_id' => $user->id ?? null,
            'username' => $user->username ?? 'Bilinmiyor',
            'ip' => $request->ip(),
            'time' => now(),
        ]);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Oturum kapatıldı.');
    }

    /**
     * "Beni hatırla" çerezlerini işler.
     */
    private function handleRememberMe(Request $request): void
    {
        if ($request->boolean('remember')) {
            Cookie::queue('remember_username', $request->input('username'), 60 * 24 * 7); // 7 gün
            Cookie::queue('remember_password', $request->input('password'), 60 * 24 * 7); // 7 gün

            Log::info('Beni hatırla çerezleri ayarlandı.', [
                'username' => $request->input('username'),
                'ip' => $request->ip(),
            ]);
        } else {
            Cookie::queue(Cookie::forget('remember_username'));
            Cookie::queue(Cookie::forget('remember_password'));

            Log::info('Beni hatırla çerezleri temizlendi.', [
                'ip' => $request->ip(),
            ]);
        }
    }
}
