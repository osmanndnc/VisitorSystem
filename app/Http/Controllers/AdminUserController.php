<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $users = User::where('role', 'admin')->get();

        // Formu açmak için parametre varsa
        $editUser = null;
        if ($request->has('edit')) {
            $editUser = User::findOrFail($request->get('edit'));
        }

        return view('admin.users.index', compact('users', 'currentUser', 'editUser'));
    }
    

    // Kullanıcıyı güncelle
    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Yetkisiz');
        }

        $validated = $request->validate([
            'ad_soyad'   => 'required|string|max:255',
            'user_phone' => 'nullable|string|max:20',
            'username' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'role' => 'required|in:admin,security',
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:6',
        ]);

        $user->ad_soyad = $validated['ad_soyad'];
        $user->user_phone = $validated['user_phone'];

        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->is_active = $validated['is_active'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        Log::info('Admin kullanıcısı güncellendi', [
            'target user' => $user->username,
            'new role' => $user->role,
            'activity' => $user->is_active ? 'Aktif' : 'Pasif',
            'person of transaction' => auth()->user()->username,
            'time' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı güncellendi.');
    }

    // Kullanıcının aktif/pasif durumunu değiştir
    public function toggleStatus(User $user)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Yetkisiz');
        }

        // Kendi hesabını pasif yapamaz
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kendinizi pasif edemezsiniz.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        // Güvenlik aktif pasif kontrol logu
        Log::info('Admin kullanıcısının durumu değiştirildi', [
            'process' => $user->is_active ? 'Aktif edildi' : 'Pasif edildi',
            'target user' => $user->username,
            'person of transaction' => auth()->user()->username,
            'time' => now(),
        ]);

        return back()->with('success', 'Kullanıcının durumu güncellendi.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ad_soyad'   => 'required|string|max:255',
            'user_phone' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,security',
            'is_active' => 'required|boolean',
        ]);

        User::create([
            'ad_soyad'   => $request->ad_soyad,
            'user_phone' => $request->user_phone,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->is_active,
        ]);

        Log::info('Yeni admin kullanıcısı oluşturuldu', [
            'created user' => $request->username,
            'role' => $request->role,
            'created by' => auth()->user()->username,
            'time' => now(),
        ]);

        return back()->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

}
