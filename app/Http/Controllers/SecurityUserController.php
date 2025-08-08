<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

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

        Log::channel('admin')->info('Güvenlik kullanıcı listesi görüntülendi', $this->logContext([
            'action'  => 'security_user_index',
            'status'  => 'success',
            'message' => 'Güvenlik kullanıcı listesi açıldı',
            'count'   => $users->count(),
        ]));

        return view('security.users.index', compact('users', 'editUser'));
    }

    /**
     * Güvenlik kullanıcısını oluşturur ve loglar.
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

            Log::channel('admin')->info('Yeni güvenlik kullanıcısı oluşturuldu', $this->logContext([
                'action'       => 'security_user_store',
                'status'       => 'success',
                'message'      => 'Yeni güvenlik kullanıcısı eklendi',
                'record_id'    => $user->id,
                'created_user' => $validated['username'],
                'role'         => $validated['role'],
            ]));

            return back()->with('success', 'Kullanıcı başarıyla oluşturuldu.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('admin')->warning('Güvenlik kullanıcı ekleme validasyon hatası', $this->logContext([
                'action'  => 'security_user_store',
                'status'  => 'failed',
                'message' => 'Form doğrulama hatası',
                'errors'  => $e->errors(),
            ]));
            throw $e;
        } catch (Throwable $e) {
            Log::channel('admin')->error('Güvenlik kullanıcı eklenemedi', $this->logContext([
                'action'  => 'security_user_store',
                'status'  => 'failed',
                'message' => $e->getMessage(),
            ]));
            throw $e;
        }
    }

    /**
     * Güvenlik kullanıcısını günceller ve loglar.
     */
    public function update(Request $request, User $user)
    {
        try {
            if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
                Log::channel('admin')->warning('Yetkisiz erişim denemesi', $this->logContext([
                    'action'     => 'security_user_update',
                    'status'     => 'failed',
                    'message'    => 'Rol güncelleme yetkisi yok',
                    'target_user'=> $user->username,
                ]));
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

            $after   = $user->only(['ad_soyad','user_phone','username','email','role','is_active']);
            $changed = array_keys(array_diff_assoc($after, $before));

            Log::channel('admin')->info('Güvenlik kullanıcısı güncellendi', $this->logContext([
                'action'         => 'security_user_update',
                'status'         => 'success',
                'message'        => 'Kullanıcı bilgileri güncellendi',
                'record_id'      => $user->id,
                'target_user'    => $user->username,
                'changed_fields' => $changed,
            ]));

            return redirect()->route('security.users.index')->with('success', 'Kullanıcı güncellendi');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('admin')->warning('Güvenlik kullanıcı güncelleme validasyon hatası', $this->logContext([
                'action'  => 'security_user_update',
                'status'  => 'failed',
                'message' => 'Form doğrulama hatası',
                'errors'  => $e->errors(),
                'record_id' => $user->id,
            ]));
            throw $e;
        } catch (Throwable $e) {
            Log::channel('admin')->error('Güvenlik kullanıcısı güncellenemedi', $this->logContext([
                'action'    => 'security_user_update',
                'status'    => 'failed',
                'message'   => $e->getMessage(),
                'record_id' => $user->id,
            ]));
            throw $e;
        }
    }

    /**
     * Güvenlik kullanıcısının aktif/pasif durumunu değiştirir ve loglar.
     */
    public function toggle($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        Log::channel('admin')->info('Güvenlik kullanıcısının durumu değiştirildi', $this->logContext([
            'action'      => 'security_user_toggle',
            'status'      => 'success',
            'message'     => 'Kullanıcı aktiflik durumu değişti',
            'record_id'   => $user->id,
            'target_user' => $user->username,
            'new_status'  => $user->is_active ? 'Aktif' : 'Pasif',
        ]));

        return back()->with('success', 'Durum değiştirildi.');
    }

    /**
     * Ortak log context metodu.
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
