<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    /**
     * Admin kullanıcıları listeler. Düzenleme modu parametre ile tetiklenir.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $users = User::where('role', 'admin')->get();

        $editUser = null;
        if ($request->has('edit')) {
            $editUser = User::findOrFail($request->get('edit'));
        }

        return view('admin.users.index', compact('users', 'currentUser', 'editUser'));
    }

    /**
     * Yeni admin veya security rolüne sahip kullanıcı oluşturur.
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

        Log::info('Yeni admin kullanıcısı oluşturuldu', [
            'created_user' => $validated['username'],
            'role'         => $validated['role'],
            'created_by'   => auth()->user()->username,
            'time'         => now(),
        ]);

        return back()->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Mevcut admin kullanıcısını günceller.
     */
    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Yetkisiz');
        }

        $validated = $request->validate([
            'ad_soyad'   => 'required|string|max:255',
            'user_phone' => 'nullable|string|max:20',
            'username'   => 'required|string|max:191',
            'email'      => 'required|email|max:191',
            'role'       => 'required|in:admin,security',
            'is_active'  => 'required|boolean',
            'password'   => 'nullable|string|min:6',
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

        Log::info('Admin kullanıcısı güncellendi', [
            'target_user'      => $user->username,
            'new_role'         => $user->role,
            'updated_activity' => $user->is_active ? 'Aktif' : 'Pasif',
            'updated_by'       => auth()->user()->username,
            'time'             => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı güncellendi.');
    }

    /**
     * Admin kullanıcısının aktif/pasif durumu değiştirilir.
     */
    public function toggleStatus(User $user)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Yetkisiz');
        }

        // Kendi hesabını pasif yapmaya izin verilmez
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kendinizi pasif edemezsiniz.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        Log::info('Admin kullanıcısının durumu değiştirildi', [
            'target_user' => $user->username,
            'new_status'  => $user->is_active ? 'Aktif edildi' : 'Pasif edildi',
            'changed_by'  => auth()->user()->username,
            'time'        => now(),
        ]);

        return back()->with('success', 'Kullanıcının durumu güncellendi.');
    }
}
