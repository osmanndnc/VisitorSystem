<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            Log::info('Şifre sıfırlama bağlantısı gönderildi.', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            return back()->with('status', __($status));
        } else {
            Log::warning('Şifre sıfırlama denemesi başarısız.', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }
    }
}
