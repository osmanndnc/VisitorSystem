<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * AdminUserController
 *
 * SRP (Single Responsibility Principle):
 *  - Admin kullanıcılarını listeleme, oluşturma, güncelleme ve aktif/pasif geçişini yönetir.
 *  - HTTP akışını yönetir; doğrulama Laravel validator'ına, veri kalıcılığı modele delege edilir.
 *
 * Güvenlik:
 *  - Kayıt, güncelleme ve durum değiştirme işlemleri server-side "super_admin" yetkisi gerektirir.
 *  - Kullanıcı kendi hesabını pasif edemez.
 */
class AdminUserController extends Controller
{
    /**
     * Admin kullanıcılarını listeler. (Blade `@foreach($users as $user)` bekliyor.)
     * GET -> /admin/users
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();

        // Sadece admin rolündeki kullanıcıları getir (Admin Listesi sayfası)
        $users = User::where('role', 'admin')->get();

        // Düzenleme modu: ?edit=ID gelirse prefill için kullanılacak kullanıcı
        $editUser = $request->has('edit')
            ? User::findOrFail($request->get('edit'))
            : null;

        return view('admin.users.index', compact('users', 'currentUser', 'editUser'));
    }

    /**
     * Yeni admin (veya security) kullanıcısı oluşturur.
     * POST -> /admin/users
     *
     * Not: Admin sayfasında da olsa, sunucu tarafında super_admin zorunludur.
     */
    public function store(Request $request): RedirectResponse
    {
        // Yetki kontrolü (UI'da gizlemek yetmez, server-side da şart)
        abort_unless(auth()->user()?->role === 'super_admin', 403, 'Yetkisiz');

        $validated = $request->validate([
            'ad_soyad'   => ['required', 'string', 'max:255'],
            'user_phone' => ['nullable', 'string', 'max:20'],
            'username'   => ['required', 'string', 'max:255', 'unique:users,username'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6'],
            'role'       => ['required', 'in:admin,security'],
            'is_active'  => ['required', 'boolean'],
        ]);

        User::create([
            'ad_soyad'   => $validated['ad_soyad'],
            'user_phone' => $validated['user_phone'] ?? null,
            'username'   => $validated['username'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => $validated['role'],
            'is_active'  => $validated['is_active'],
        ]);

        return back()->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Mevcut kullanıcıyı günceller.
     * PUT/PATCH -> /admin/users/{user}
     *
     * Not:
     *  - Parola boş bırakılırsa değiştirilmez.
     *  - username/email için "aynı kullanıcıyı yok say" kuralı eklenmiştir.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()?->role === 'super_admin', 403, 'Yetkisiz');

        $validated = $request->validate([
            'ad_soyad'   => ['required', 'string', 'max:255'],
            'user_phone' => ['nullable', 'string', 'max:20'],
            'username'   => ['required', 'string', 'max:191', Rule::unique('users', 'username')->ignore($user->id)],
            'email'      => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user->id)],
            'role'       => ['required', 'in:admin,security'],
            'is_active'  => ['required', 'boolean'],
            'password'   => ['nullable', 'string', 'min:6'],
        ]);

        $user->fill([
            'ad_soyad'   => $validated['ad_soyad'],
            'user_phone' => $validated['user_phone'] ?? null,
            'username'   => $validated['username'],
            'email'      => $validated['email'],
            'role'       => $validated['role'],
            'is_active'  => $validated['is_active'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı güncellendi.');
    }

    /**
     * Admin kullanıcısının aktif/pasif durumunu değiştirir.
     * PATCH -> /admin/users/{user}/toggle
     *
     * Not:
     *  - Kullanıcı kendi hesabını pasif yapamaz.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        abort_unless(auth()->user()?->role === 'super_admin', 403, 'Yetkisiz');

        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kendinizi pasif edemezsiniz.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return back()->with('success', 'Kullanıcının durumu güncellendi.');
    }
}
