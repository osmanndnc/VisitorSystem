<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SecurityUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role', 'security')->get();
        $editUser = null;
        
        if ($request->has('edit')) {
            $editUser = User::where('role', 'security')->findOrFail($request->edit);
        }

        return view('security.users.index', compact('users', 'editUser'));
    }

    public function update(Request $request, User $user)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Yetkisiz erişim');
        }

        $validated = $request->validate([
            'ad_soyad'   => 'required|string|max:255',
            'user_phone' => 'nullable|string|max:20',   
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,security',
            'is_active' => 'required|boolean',
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

        return back()->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    

}
