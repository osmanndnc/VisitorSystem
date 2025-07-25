<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // Kullanıcıları listele
    public function index()
    {
        $currentUser = Auth::user();

        // Tüm admin kullanıcılarını getir
        $users = User::where('role', 'admin')->get();

        return view('admin.users.index', compact('users', 'currentUser'));
    }

    // Kullanıcıyı düzenleme sayfası
    public function edit(User $user)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Yetkisiz');
        }

        return view('admin.users.edit', compact('user'));
    }

    // Kullanıcıyı güncelle
    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Yetkisiz');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'role' => 'required|in:admin,super_admin',
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:6',
        ]);

        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->is_active = $validated['is_active'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

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

        return back()->with('success', 'Kullanıcının durumu güncellendi.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,security',
            'is_active' => 'required|boolean',
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->is_active,
        ]);

        return back()->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

}
