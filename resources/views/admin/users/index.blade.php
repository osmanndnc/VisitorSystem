<x-app-layout>
    <style>
        html { zoom: 80% }
        body { background: linear-gradient(135deg, #f5f7fa, #e4ebf1); font-family:'Segoe UI', sans-serif }

        .backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 40;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        .backdrop.show { opacity: 1; pointer-events: auto; }

        .modal-close {
            position: absolute;
            top: 20px; right: 20px;
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.3);
            border: none;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.2s;
            backdrop-filter: blur(6px);
            color: #4b5563;
        }
        .modal-close:hover { background: rgba(255,255,255,0.5); transform: rotate(90deg); }

        /* Tablo ve Formlar İçin Ortak Kart Stili */
        .card, .user-detail-card, .user-form-card {
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.87);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        /* Tablo Stilleri */
        .table thead th {
            background: rgba(226, 232, 240, 0.9);
            color: #1e293b;
            font-weight: 600;
            font-size: 16px;
            padding: 20px 16px;
            border-bottom: 2px solid rgba(203, 213, 225, 0.8);
            text-align: center;
        }
        .table tbody td {
            text-align: center;
            padding: 20px 16px;
            font-size: 14px;
            color: #475569;
            vertical-align: middle;
        }
        .id-column { width: 80px; }
        .name-column { width: 200px; }
        .username-column { width: 150px; }
        .phone-column { width: 150px; }
        .status-column { width: 120px; }
        .detail-column { width: 100px; }
        .action-column { width: 130px; }
        .status-badge-inline {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-badge-inline.aktif {
            background: rgba(34, 197, 94, 0.15);
            color: #15803d;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .status-badge-inline.pasif {
            background: rgba(239, 68, 68, 0.15);
            color: #991b1b;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Buton Stilleri */
        .btn-edit {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            color: #0369a1;
            padding: 9px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #0ea5e9;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.2);
        }
        .btn-edit:hover {
            background: linear-gradient(135deg, #bae6fd 0%, #7dd3fc 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.3);
        }
        .btn-edit:active { transform: translateY(0); }
        .btn-style905 {
            background:#1d4ed8; border:none; color:#fff; padding:12px 24px; font-size:16px; font-weight:600;
            border-radius:16px; cursor:pointer; transition:.3s; box-shadow:0 6-px 12px rgba(0,0,0,.15)
        }
        .btn-style905:hover { background:#1746c1; transform:translateY(-2px) }
        .toggle-btn {
            width: 280px; padding: 16px 24px; border-radius: 20px; font-weight: 700;
            font-size: 15px; text-align: center; transition: all 0.4s;
            border: none; cursor: pointer; text-transform: uppercase;
            letter-spacing: 1px; display: block; margin: 0 auto;
        }
        .toggle-btn.aktif {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%);
            color: white; box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }
        .toggle-btn.aktif:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 15px 35px rgba(239, 68, 68, 0.5); }
        .toggle-btn.pasif {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white; box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }
        .toggle-btn.pasif:hover { transform: translateY(-3px) scale(1.02); box-shadow:0 15px 35px rgba(16, 185, 129, 0.5); }
        .toggle-btn:active { transform: translateY(-1px) scale(0.98); }

        /* Form Elemanları */
        input[type="text"], input[type="email"], input[type="password"], select {
            background:#fff; border:1px solid #d1d5db; padding:10px 12px; font-size:14px; border-radius:8px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,.05); transition: border-color .3s ease;
        }
        input:focus, select:focus { border-color:#2563eb; outline:none; box-shadow:0 0 0 2px rgba(37,99,235,.2); }
        .submit-animated {
            width: 200px; height: 50px; border-radius: 50px;
            background: linear-gradient(135deg,#1d4ed8 0%,#2563eb 100%);
            border: none; position: relative; overflow: hidden;
            font-size: 16px; font-weight: 600; color: #fff; cursor: pointer;
            transition: all .3s ease; box-shadow: 0 8px 20px rgba(0,80,160,.2);
        }
        .submit-animated:hover { background:#1746c1; }
        .submit-animated img {
            position:absolute; width:26px; height:26px; top:50%; left:50%;
            transform:translate(-50%,-50%); opacity:0;
        }
        .submit-animated:focus { animation: extend 1s ease-in-out forwards; }
        .submit-animated:focus span { animation: disappear 1s ease-in-out forwards; }
        .submit-animated:focus img { animation: appear 1s ease-in-out forwards; }
        @keyframes extend {
            0% { width: 200px; height: 50px; border-radius: 50px; }
            50% { background: #22c55e; }
            100% { width: 60px; height: 60px; border-radius: 50%; background: #22c55e; }
        }
        @keyframes disappear { 0% {opacity:1;} 100% {opacity:0;} }
        @keyframes appear { 0% {opacity:0;} 100% {opacity:1;} }

        /* Kullanıcı Detay Kartı Modalı */
        .user-detail-card {
            position: fixed; 
            top: 50%; left: 50%; 
            transform: translate(-50%, -50%) scale(0.9);
            width: 430px;
            z-index: 50;
            opacity: 0;
            transition: all 0.35s ease;
            pointer-events: none;
        }
        .user-detail-card.show {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
            pointer-events: auto;
        }
        .user-avatar {
            width: 80px; height: 80px; border-radius: 50%; object-fit: cover;
            border: 2px solid rgba(255,255,255,0.5);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 0 auto 12px; background: #e5e7eb;
        }
        .user-name { font-size: 22px; font-weight: 700; }
        .user-username { font-size: 14px; color: #555; }
        .status-badge {
            position: absolute; top: 20px; left: 20px;
            padding: 6px 14px; border-radius: 12px;
            font-size: 12px; font-weight: 600; letter-spacing: 0.5px;
        }
        .status-badge.aktif { background: rgba(34,197,94,0.15); color: #15803d; border: 1px solid rgba(34,197,94,0.3); }
        .status-badge.pasif { background: rgba(239,68,68,0.15); color: #991b1b; border: 1px solid rgba(239,68,68,0.3); }
        .info-row {
            display: flex; justify-content: space-between; padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.3); font-size: 14px;
        }
        .info-label { font-weight: 600; color: #333; }
        .info-value { color: #555; }
        .card-header {
            padding: 25px 20px 15px; text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.4); position: relative;
        }
        .card-content { padding: 20px; }
        .card-actions { padding: 20px; text-align: center; }

        /* Kullanıcı Form Modalı */
        .user-form-card {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0.95);
            width: 950px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 50;
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            pointer-events: none;
            max-height: 90vh;
            overflow-y: auto;
        }
        .user-form-card.show {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
            pointer-events: auto;
        }
        .form-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            position: relative;
        }
        .form-title { font-size: 24px; font-weight: 700; color: #1f2937; }
        .form-body { padding: 20px; }
        .form-footer { padding: 20px; text-align: right; border-top: 1px solid #e5e7eb; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sadece form submit animasyonunu yöneten kısım
            const submitBtn = document.getElementById('submit-button');
            if (submitBtn) {
                const form = submitBtn.closest('form');
                submitBtn.addEventListener('click', function (e) {
                    if (form.checkValidity()) {
                        e.preventDefault(); 
                        submitBtn.blur();
                        requestAnimationFrame(() => {
                            submitBtn.focus();
                            setTimeout(() => form.submit(), 1000);
                        });
                    }
                });
            }
        });
    </script>
    
    <div class="py-6 max-w-7xl mx-auto" 
         x-data="{ 
             showDetailCard: false, 
             showFormCard: false,
             currentUser: null,
             formAction: '',
             formTitle: '',
             formData: {
                ad_soyad: '', user_phone: '', username: '', email: '', 
                password: '', role: 'admin'
             },
             isEditing: false,
             openCreateForm() {
                 this.formAction = '{{ route('admin.users.store') }}';
                 this.formTitle = 'Yeni Kullanıcı Ekle';
                 this.isEditing = false;
                 this.formData = {
                     ad_soyad: '', user_phone: '', username: '', email: '', 
                     password: '', role: 'admin'
                 };
                 this.showFormCard = true;
             },
             openEditForm(user) {
                 this.formAction = user.updateUrl;
                 this.formTitle = 'Kullanıcıyı Güncelle';
                 this.isEditing = true;
                 this.formData = {
                     ad_soyad: user.ad_soyad,
                     user_phone: user.user_phone,
                     username: user.username,
                     email: user.email,
                     password: '', 
                     role: user.role
                 };
                 this.showFormCard = true;
             }
         }">

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Admin Listesi</h2>
        </div>

        <div class="text-right mb-6">
            @if(auth()->user()->role === 'super_admin')
                <button class="btn-style905" type="button" @click="openCreateForm()">
                    + Kullanıcı Kaydı Ekle
                </button>
            @endif
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table">
                    <thead>
                        <tr>
                            <th class="id-column">ID</th>
                            <th class="name-column">Ad Soyad</th>
                            <th class="username-column">Kullanıcı Adı</th>
                            <th class="phone-column">Telefon</th>
                            <th class="status-column">Durum</th>
                            <th class="detail-column">Detay</th>
                            @if(auth()->user()->role === 'super_admin')
                                <th class="action-column">İşlem</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($users as $user)
                            <tr class="border-t border-white/30 {{ !$user->is_active ? 'opacity-60' : '' }}">
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->ad_soyad ?? '-' }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->user_phone ?? '-' }}</td>
                                <td>
                                    <span class="status-badge-inline {{ $user->is_active ? 'aktif' : 'pasif' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                                <td>
                                    <img src="{{ asset('images/touch.gif') }}" 
                                        class="w-10 h-10 cursor-pointer hover:scale-110 transition-transform duration-200 mx-auto"
                                        alt="Detay"
                                        title="Detayları Görüntüle"
                                        @click="
                                            currentUser = {
                                                id: {{ $user->id }},
                                                ad: @js($user->ad_soyad ?? '-'),
                                                username: @js($user->username),
                                                phone: @js($user->user_phone ?? '-'),
                                                email: @js($user->email),
                                                role: @js(ucfirst($user->role)),
                                                created: @js(optional($user->created_at)->format('Y-m-d H:i')),
                                                updated: @js(optional($user->updated_at)->format('Y-m-d H:i')),
                                                isActive: {{ $user->is_active ? 'true' : 'false' }},
                                                toggleUrl: @js(route('admin.users.toggle', $user->id)),
                                            };
                                            showDetailCard = true;">
                                </td>
                                @if(auth()->user()->role === 'super_admin')
                                    <td>
                                        <button @click="openEditForm({
                                                    id: {{ $user->id }},
                                                    ad_soyad: @js($user->ad_soyad ?? ''),
                                                    user_phone: @js($user->user_phone ?? ''),
                                                    username: @js($user->username),
                                                    email: @js($user->email),
                                                    role: @js($user->role),
                                                    updateUrl: @js(route('admin.users.update', $user->id))
                                                })" class="btn-edit" type="button">
                                            Düzenle
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="backdrop" :class="{ 'show': showDetailCard }" @click="showDetailCard = false"></div>
        <div class="user-detail-card" :class="{ 'show': showDetailCard }" @click.stop>
            <div class="card-header">
                <span class="status-badge" :class="currentUser?.isActive ? 'aktif' : 'pasif'" 
                      x-text="currentUser?.isActive ? 'AKTİF' : 'PASİF'"></span>
                <button class="modal-close" @click="showDetailCard = false">&times;</button>
                <img src="{{ asset('images/id-badge.gif') }}" class="user-avatar" alt="Kullanıcı">
                <div class="user-name" x-text="currentUser?.ad || '-'"></div>
                <div class="user-username" x-text="currentUser?.username || ''"></div>
            </div>
            <div class="card-content">
                <div class="info-row"><div class="info-label">Rol</div><div class="info-value" x-text="currentUser?.role || '-'"></div></div>
                <div class="info-row"><div class="info-label">Telefon</div><div class="info-value" x-text="currentUser?.email || '-'"></div></div>
                <div class="info-row"><div class="info-label">Email</div><div class="info-value" x-text="currentUser?.email || '-'"></div></div>
                <div class="info-row"><div class="info-label">Oluşturulma Tarihi</div><div class="info-value" x-text="currentUser?.created || '-'"></div></div>
                <div class="info-row"><div class="info-label">Güncellenme Tarihi</div><div class="info-value" x-text="currentUser?.updated || '-'"></div></div>
            </div>
            <div class="card-actions">
                @if(auth()->user()->role === 'super_admin')
                    <form :action="currentUser?.toggleUrl || '#'" method="POST" class="w-full flex justify-center">
                        @csrf @method('PATCH')
                        <button type="submit" 
                                class="toggle-btn"
                                :class="currentUser?.isActive ? 'aktif' : 'pasif'">
                            <span x-text="currentUser?.isActive ? 'Pasifleştir' : 'Aktifleştir'"></span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if(auth()->user()->role === 'super_admin')
            <div class="backdrop" :class="{ 'show': showFormCard }" @click="showFormCard = false"></div>
            <div class="user-form-card" :class="{ 'show': showFormCard }" @click.stop>
                <div class="form-header">
                    <button class="modal-close" @click="showFormCard = false">&times;</button>
                    <div class="form-title" x-text="formTitle"></div>
                </div>
                <div class="form-body">
                    <form id="kullanici-formu" method="POST" :action="formAction" class="space-y-4">
                        @csrf
                        <template x-if="isEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ad Soyad</label>
                                <input type="text" name="ad_soyad" x-model="formData.ad_soyad" required class="w-full">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telefon</label>
                                <input type="text" name="user_phone" x-model="formData.user_phone" class="w-full">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kullanıcı Adı</label>
                                <input type="text" name="username" x-model="formData.username" required class="w-full">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" x-model="formData.email" required class="w-full">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700" x-text="isEditing ? 'Yeni Şifre (Boş bırakın)' : 'Şifre'"></label>
                                <input type="password" name="password" x-model="formData.password" :required="!isEditing" class="w-full">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Rol</label>
                                <select name="role" x-model="formData.role" class="w-full" :disabled="!isEditing">
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                                <template x-if="!isEditing">
                                   <input type="hidden" name="role" x-model="formData.role">
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="is_active" value="1">
                    </form>
                </div>
                <div class="form-footer">
                     <button type="submit" form="kullanici-formu" class="submit-animated" id="submit-button">
                        <span x-text="isEditing ? 'Güncelle' : 'Kaydet'"></span>
                        <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="✓">
                     </button>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>