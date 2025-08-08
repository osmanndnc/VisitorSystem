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

            // Kullanıcı pasifse 
            if (!$user->is_active) {
                Log::channel('auth')->warning('Pasif kullanıcı giriş denemesi', $this->logContext([
                    'action'  => 'login_attempt',
                    'status'  => 'failed',
                    'message' => 'Kullanıcı pasif durumda'
                ]));

                auth()->logout();

                return redirect()->route('login')->withErrors([
                    'username' => 'Şu an aktif bir kullanıcı değilsiniz.'
                ]);
            }

            // Başarılı giriş
            Log::channel('auth')->info('Kullanıcı girişi başarılı', $this->logContext([
                'action'  => 'login',
                'status'  => 'success',
                'message' => 'Giriş başarılı'
            ]));

            $this->handleRememberMe($request);
            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::redirectToBasedOnRole());

        } catch (ValidationException $e) {
            // Hatalı giriş
            Log::channel('auth')->error('Giriş başarısız', $this->logContext([
                'action'  => 'login',
                'status'  => 'failed',
                'message' => 'Kullanıcı adı veya şifre hatalı'
            ]));

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

        Log::channel('auth')->info('Kullanıcı çıkış yaptı', $this->logContext([
            'action'  => 'logout',
            'status'  => 'success',
            'message' => 'Oturum kapatıldı',
            'user_id' => $user->id ?? null,
            'username'=> $user->username ?? 'Bilinmiyor'
        ]));

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

            Log::channel('auth')->info('"Beni hatırla" aktif', $this->logContext([
                'action'  => 'remember_me',
                'status'  => 'enabled',
                'message' => 'Beni hatırla çerezleri ayarlandı'
            ]));
        } else {
            Cookie::queue(Cookie::forget('remember_username'));
            Cookie::queue(Cookie::forget('remember_password'));

            Log::channel('auth')->info('"Beni hatırla" devre dışı', $this->logContext([
                'action'  => 'remember_me',
                'status'  => 'disabled',
                'message' => 'Beni hatırla çerezleri temizlendi'
            ]));
        }
    }

    /**
     * Ortak log context bilgisi.
     */
    private function logContext(array $extra = []): array
    {
        $user = auth()->user();

        return array_merge([
            'user_id'  => $user->id ?? null,
            'username' => $user->username ?? request()->input('username'),
            'ip'       => request()->ip(),
            'time'     => now()->toDateTimeString(),
        ], $extra);
    }
}
