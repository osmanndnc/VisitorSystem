<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
//use App\Providers\RouteServiceProvider;


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

        
        // Kullanıcı doğrulandıktan hemen sonra kontrol edelim:
        if (!auth()->user()->is_active) {
            auth()->logout(); // Oturumu kapat
            return redirect()->route('login')->withErrors([
                'username' => 'Şu an aktif bir kullanıcı değilsiniz.'
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::redirectToBasedOnRole());
        //return redirect(RouteServiceProvider::redirectToBasedOnRole());


        // return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        //return redirect('/');
        return redirect()->route('login')->with('status', 'Oturum kapatıldı.');

    }

}
