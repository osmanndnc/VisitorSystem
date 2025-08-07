<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SecurityUserController extends Controller
{
    /**
     * Güvenlik kullanıcılarını listeler ve düzenleme modunu aktifleştirir.
     */
    public function index(Request $request)
    {
        $users = User::where('role', 'security')->get();
        $editUser = null;

        if ($request->has('edit')) {
            $editUser = User::where('role', 'security')->findOrFail($request->edit);
        }

        return view('security.users.index', compact('users', 'editUser'));
    }

    /**
     * Güvenlik kullanıcısını oluşturur ve loglar.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad_soyad'   => 'required|string|max:255',
            'user_phone' => 'nullable|string|max:20',
            'username'   => 'required|string|max:255|unique:users,username',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'role'       => 'required|in:admin,security',
            'is_active'  => 'required|boolean',
        ]);

        User::create([
            'ad_soyad'   => $validated['ad_soyad'],
            'user_phone' => $validated['user_phone'],
            'username'   => $validated['username'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => $validated['role'],
            'is_active'  => $validated['is_active'],
        ]);

        Log::info('Yeni güvenlik kullanıcısı oluşturuldu', [
            'created_user' => $validated['username'],
            'role'         => $validated['role'],
            'created_by'   => auth()->user()->username,
            'time'         => now(),
        ]);

        return back()->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Güvenlik kullanıcısını günceller ve loglar.
     */
    public function update(Request $request, User $user)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Yetkisiz erişim');
        }

        $validated = $request->validate([
            'ad_soyad'   => 'required|string|max:255',
            'user_phone' => 'nullable|string|max:20',
            'username'   => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'password'   => 'nullable|string|min:6',
            'role'       => 'required|in:admin,security',
            'is_active'  => 'required|boolean',
        ]);

        $user->fill([
            'ad_soyad'   => $validated['ad_soyad'],
            'user_phone' => $validated['user_phone'],
            'username'   => $validated['username'],
            'email'      => $validated['email'],
            'role'       => $validated['role'],
            'is_active'  => $validated['is_active'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        Log::info('Güvenlik kullanıcısı güncellendi', [
            'target_user'        => $user->username,
            'new_role'           => $user->role,
            'updated_activity'   => $user->is_active ? 'Aktif' : 'Pasif',
            'updated_by'         => auth()->user()->username,
            'time'               => now(),
        ]);

        return redirect()->route('security.users.index')->with('success', 'Kullanıcı güncellendi');
    }

    /**
     * Güvenlik kullanıcısının aktif/pasif durumunu değiştirir ve loglar.
     */
    public function toggle($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        Log::info('Güvenlik kullanıcısının durumu değiştirildi', [
            'target_user' => $user->username,
            'new_status'  => $user->is_active ? 'Aktif' : 'Pasif',
            'changed_by'  => auth()->user()->username,
            'time'        => now(),
        ]);

        return back()->with('success', 'Durum değiştirildi.');
    }
}
