<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminUserController extends Controller
{
    /**
     * Admin kullanıcıları listeler. Düzenleme modu parametre ile tetiklenir.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        
        // YENİ: Her iki rol için de kullanıcıları getir
        $adminUsers = User::where('role', 'admin')->get();
        $securityUsers = User::where('role', 'security')->get();
        
        // MEVCUT: Eski kod korundu
        $users = User::where('role', 'admin')->get();

        $editUser = null;
        if ($request->has('edit')) {
            $editUser = User::findOrFail($request->get('edit'));
        }

        Log::channel('admin')->info('Admin kullanıcı listesi görüntülendi', $this->logContext([
            'action'  => 'admin_user_index',
            'status'  => 'success',
            'message' => 'Admin kullanıcı listesi görüntülendi',
            'edit_id' => $request->get('edit'),
            'count'   => $users->count(),
        ]));

        // YENİ: Her iki veri setini de gönder
        return view('admin.users.index', compact('adminUsers', 'securityUsers', 'users', 'currentUser', 'editUser'));
    }

    /**
     * Yeni admin veya security rolüne sahip kullanıcı oluşturur.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ad_soyad'   => 'required|string|max:255',
                'user_phone' => 'nullable|string|max:20',
                'username'   => 'required|string|max:255|unique:users,username',
                'email'      => 'required|email|unique:users,email',
                'password'   => 'required|string|min:6',
                'role'       => 'required|in:admin,security',
                'is_active'  => 'required|boolean',
            ]);

            $user = User::create([
                'ad_soyad'   => $validated['ad_soyad'],
                'user_phone' => $validated['user_phone'],
                'username'   => $validated['username'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'role'       => $validated['role'],
                'is_active'  => $validated['is_active'],
            ]);

            Log::channel('admin')->info('Kullanıcı oluşturuldu', $this->logContext([
                'action'       => 'admin_user_store',
                'status'       => 'success',
                'message'      => 'Yeni admin kullanıcısı oluşturuldu',
                'target_user'  => $user->username,
                'target_role'  => $user->role,
                'target_active'=> $user->is_active,
            ]));

            return back()->with('success', 'Admin kullanıcı başarıyla oluşturuldu.');
        } catch (Throwable $e) {
            Log::channel('admin')->error('Admin kullanıcı oluşturma başarısız', $this->logContext([
                'action'  => 'admin_user_store',
                'status'  => 'failed',
                'message' => $e->getMessage(),
            ]));
            throw $e; // standart hata akışı
        }
    }

    /**
     * Mevcut admin kullanıcısını günceller.
     */
    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'super_admin') {
            Log::channel('admin')->warning('Yetkisiz yönetici kullanıcı güncelleme girişimi', $this->logContext([
                'action'      => 'admin_user_update',
                'status'      => 'forbidden',
                'message'     => 'super_admin gereklidir',
                'target_user' => $user->username,
            ]));
            abort(403, 'Yetkisiz');
        }

        try {
            $validated = $request->validate([
                'ad_soyad'   => 'required|string|max:255',
                'user_phone' => 'nullable|string|max:20',
                'username'   => 'required|string|max:191',
                'email'      => 'required|email|max:191',
                'role'       => 'required|in:admin,security',
                'is_active'  => 'required|boolean',
                'password'   => 'nullable|string|min:6',
            ]);

            // Eski değerleri kopyala değişim seti için
            $before = $user->only(['ad_soyad','user_phone','username','email','role','is_active']);

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

            // Değişen alanları bul
            $after   = $user->only(['ad_soyad','user_phone','username','email','role','is_active']);
            $changed = array_keys(array_diff_assoc($after, $before));

            Log::channel('admin')->info('Admin kullanıcı güncellendi', $this->logContext([
                'action'        => 'admin_user_update',
                'status'        => 'success',
                'message'       => 'Admin kullanıcısı güncellendi',
                'target_user'   => $user->username,
                'changed_fields'=> $changed,
                'new_role'      => $user->role,
                'new_active'    => $user->is_active,
            ]));

            return redirect()->route('admin.users.index')->with('success', 'Kullanıcı güncellendi.');
        } catch (Throwable $e) {
            Log::channel('admin')->error('Admin kullanıcı güncelleme başarısız', $this->logContext([
                'action'      => 'admin_user_update',
                'status'      => 'failed',
                'message'     => $e->getMessage(),
                'target_user' => $user->username,
            ]));
            throw $e;
        }
    }

    /**
     * Admin kullanıcısının aktif/pasif durumu değiştirilir.
     */
    public function toggleStatus(User $user)
    {
        if (auth()->user()->role !== 'super_admin') {
            Log::channel('admin')->warning('Yetkisiz geçiş denemesi', $this->logContext([
                'action'      => 'admin_user_toggle',
                'status'      => 'forbidden',
                'message'     => 'super_admin gereklidir',
                'target_user' => $user->username,
            ]));
            abort(403, 'Yetkisiz');
        }

        if (auth()->id() === $user->id) {
            Log::channel('admin')->warning('Kendini devre dışı bırakma engellendi', $this->logContext([
                'action'      => 'admin_user_toggle',
                'status'      => 'blocked',
                'message'     => 'Kullanıcı kendi hesabını pasif yapamaz',
                'target_user' => $user->username,
            ]));
            return back()->with('error', 'Kendinizi pasif edemezsiniz.');
        }

        try {
            $user->is_active = !$user->is_active;
            $user->save();

            Log::channel('admin')->info('Admin kullanıcı durumu değiştirildi', $this->logContext([
                'action'      => 'admin_user_toggle',
                'status'      => 'success',
                'message'     => 'Kullanıcı durumu değiştirildi',
                'target_user' => $user->username,
                'new_status'  => $user->is_active ? 'active' : 'inactive',
            ]));

            return back()->with('success', 'Kullanıcının durumu güncellendi.');
        } catch (Throwable $e) {
            Log::channel('admin')->error('Admin kullanıcı geçişi başarısız oldu', $this->logContext([
                'action'      => 'admin_user_toggle',
                'status'      => 'failed',
                'message'     => $e->getMessage(),
                'target_user' => $user->username,
            ]));
            throw $e;
        }
    }

    /**
     * Ortak log context bilgisi (standart).
     */
    private function logContext(array $extra = []): array
    {
        $actor = auth()->user();

        return array_merge([
            'user_id'  => $actor->id ?? null,
            'username' => $actor->username ?? 'Anonim',
            'ip'       => request()->ip(),
            'time'     => now()->toDateTimeString(),
        ], $extra);
    }
}