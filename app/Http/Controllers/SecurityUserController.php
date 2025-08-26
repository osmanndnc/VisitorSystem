<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * SecurityUserController
 *
 * SRP:
 *  - Sadece "security" rolüne sahip kullanıcıların listelenmesi, oluşturulması,
 *    güncellenmesi ve aktif/pasif durumunun değiştirilmesinden sorumludur.
 *
 * Clean Code:
 *  - Kısa ve tek görevli metodlar
 *  - Açıklayıcı yorumlar ve anlamlı değişken adları
 */
class SecurityUserController extends Controller
{
    /**
     * Güvenlik kullanıcılarını listeler ve (varsa) düzenleme modunu aktifleştirir.
     * GET -> /security/users
     */
    public function index(Request $request)
    {
        $users    = User::where('role', 'security')->get();   // mevcut akış korunur:contentReference[oaicite:22]{index=22}
        $editUser = $request->has('edit')
            ? User::where('role', 'security')->findOrFail($request->edit) // mevcut davranış:contentReference[oaicite:23]{index=23}
            : null;

        return view('security.users.index', compact('users', 'editUser')); //:contentReference[oaicite:24]{index=24}
    }

    /**
     * Yeni güvenlik kullanıcısı oluşturur.
     * POST -> /security/users
     *
     * Notlar:
     *  - Parola her zaman Hash::make ile saklanır.
     *  - role alanı admin/security olarak sınırlandırılmıştır (kaynak akışla uyumlu).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([ // kaynak validasyon korunur:contentReference[oaicite:25]{index=25}
            'ad_soyad'   => 'required|string|max:255',
            'user_phone' => 'nullable|string|max:20',
            'username'   => 'required|string|max:255|unique:users,username',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'role'       => 'required|in:admin,security',
            'is_active'  => 'required|boolean',
        ]);

        User::create([ // kaynak create akışı korunur:contentReference[oaicite:26]{index=26}
            'ad_soyad'   => $validated['ad_soyad'],
            'user_phone' => $validated['user_phone'],
            'username'   => $validated['username'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => $validated['role'],
            'is_active'  => $validated['is_active'],
        ]);

        return back()->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Güvenlik kullanıcısını günceller.
     * PUT/PATCH -> /security/users/{user}
     *
     * Notlar:
     *  - Parola boş bırakılırsa değiştirilmez.
     *  - İzin/yetki kontrolü proje politikanıza göre middleware ile eklenebilir.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([ // kaynak validasyon korunur:contentReference[oaicite:27]{index=27}
            'ad_soyad'   => 'required|string|max:255',
            'user_phone' => 'nullable|string|max:20',
            'username'   => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'password'   => 'nullable|string|min:6',
            'role'       => 'required|in:admin,security',
            'is_active'  => 'required|boolean',
        ]);

        // Güncelleme (kaynak akış):contentReference[oaicite:28]{index=28}
        $user->fill([
            'ad_soyad'   => $validated['ad_soyad'],
            'user_phone' => $validated['user_phone'],
            'username'   => $validated['username'],
            'email'      => $validated['email'],
            'role'       => $validated['role'],
            'is_active'  => $validated['is_active'],
        ]);

        // Parola opsiyonel (kaynak akış):contentReference[oaicite:29]{index=29}
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save(); //:contentReference[oaicite:30]{index=30}

        return redirect()->route('security.users.index')->with('success', 'Kullanıcı güncellendi'); //:contentReference[oaicite:31]{index=31}
    }

    /**
     * Güvenlik kullanıcısının aktif/pasif durumunu değiştirir.
     * POST -> /security/users/{id}/toggle
     */
    public function toggle($id)
    {
        $user = User::findOrFail($id); // kaynak akışta toggle doğrudan findOrFail + save:contentReference[oaicite:32]{index=32}
        $user->is_active = ! $user->is_active;
        $user->save();

        return back()->with('success', 'Durum değiştirildi.'); //:contentReference[oaicite:33]{index=33}
    }
}
