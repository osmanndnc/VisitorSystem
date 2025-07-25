<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SecurityUserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'security')->get();
        return view('security.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        // admin VEYA super_admin erişebilsin
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Yetkisiz erişim');
        }

        return view('security.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Yetkisiz erişim');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'is_active' => 'required|boolean',
        ]);

        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->is_active = $validated['is_active'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('security.users.index')->with('success', 'Kullanıcı güncellendi');
    }

    public function toggle($id)
    {
        $user = User::findOrFail($id); // ya da SecurityUser
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Durum değiştirildi.');
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
