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
            padding: 16px 16px;
            font-size: 16px;
            color: #475569;
            vertical-align: middle;

        }
        /* Her satırın altında border */
        .table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .table tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        .table tbody tr.passive-row {
            background: #f9fafb;
            opacity: 0.7;
        }
        
        /* Sütun Genişlikleri */
        .id-column { width: 80px; }
        .name-column { width: 200px; }
        .username-column { width: 150px; }
        .phone-column { width: 150px; }
        .status-column { width: 120px; }
        .detail-column { width: 100px; }
        .actions-column { width: 130px; }

        /* Durum Sütunu Hizalama */
        .status-column {
            text-align: center;
            vertical-align: middle;
        }
        
        /* Toggle Switch Container */
        .toggle-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* Toggle Switch */
        .toggle-switch {
            width: 80px; 
            height: 36px; 
            background: #f1f5f9; 
            border-radius: 999px; 
            border: 2px solid #e2e8f0;
            cursor: pointer; 
            position: relative; 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
            outline: none;
            margin: 0 auto;
        }

        .toggle-switch:hover {
            transform: scale(1.05);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);
        }

        .toggle-switch.active { 
            background: linear-gradient(135deg, #22c55e, #16a34a); 
            border-color: #16a34a;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 4px 12px rgba(34, 197, 94, 0.4);
        }

        .toggle-switch:not(.active) {
            background: linear-gradient(135deg, #f87171, #ef4444);
            border-color: #ef4444;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .toggle-switch .circle {
            position: absolute; 
            top: 2px; 
            left: 2px; 
            width: 28px; 
            height: 28px; 
            background: #ffffff;
            border-radius: 50%; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .toggle-switch.active .circle { 
            left: 48px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }

        .toggle-switch .label {
            position: absolute; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%,-50%);
            font-size: 11px; 
            color: #ffffff; 
            font-weight: 700; 
            pointer-events: none; 
            z-index: 1;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
            letter-spacing: 0.5px;
        }

        .toggle-switch.readonly {
            cursor: not-allowed;
            opacity: 0.8;
        }
        .toggle-switch.readonly:hover {
            transform: none;
        }

        /* Düzenle Butonu */
        .btn-edit {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            color: #0369a1;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #0ea5e9;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.2);
            position: relative;
            overflow: hidden;
        }

        .btn-edit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-edit:hover::before {
            left: 100%;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #bae6fd 0%, #7dd3fc 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
        }

        .btn-edit:active { 
            transform: translateY(-1px); 
        }

        /* Kullanıcı Ekle Butonu */
        .btn-style905 {
            background:#1d4ed8; 
            border:none; 
            color:#fff; 
            padding:12px 24px; 
            font-size:16px; 
            font-weight:600;
            border-radius:16px; 
            cursor:pointer; 
            transition:.3s; 
            box-shadow:0 6px 12px rgba(0,0,0,.15)
        }
        .btn-style905:hover { 
            background:#1746c1; 
            transform:translateY(-2px) 
        }

        /* Toggle Buton */
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
        .toggle-btn.pasif:hover { transform: translateY(-3px) scale(1.02); box-shadow:0 15px 35px rgba(16, 185, 129,0.5); }
        .toggle-btn:active { transform: translateY(-1px) scale(0.98); }

        /* Form Elemanları */
        input[type="text"], input[type="email"], input[type="password"], select {
            background:#fff; border:1px solid #d1d5db; padding:10px 12px; font-size:14px; border-radius:8px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,.05); transition: border-color .3s ease;
        }
        input:focus, select:focus { border-color:#2563eb; outline:none; box-shadow:0 0 0 2px rgba(37,99,235,.2); }
        
        /* Submit Butonu */
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
        
        /* Rol Kilitli */
        .role-locked{
            pointer-events: none; background-color: #f9fafb; color:#111827;
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            background-image: none;
        }
        .role-locked::-ms-expand{ display:none; }

        /* Kullanıcı Detay Kartı */
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
        
        /* User Avatar - Tablo renkleriyle uyumlu */
        .user-avatar {
            width: 80px; 
            height: 80px; 
            border-radius: 50%; 
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            border: 3px solid #94a3b8;
            box-shadow: 0 4px 12px rgba(148, 163, 184, 0.3);
            margin: 0 auto 12px; 
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
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

        /* Kullanıcı Form Kartı */
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
                    } else {
                        form.reportValidity();
                    }
                });
            }
            
            // Toggle form submit functionality
            document.querySelectorAll('.toggle-form').forEach(form => {
                form.querySelector('.toggle-switch').addEventListener('click', function (e) {
                    e.preventDefault(); 
                    form.submit();
                });
            });
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

        @if(auth()->user()->role === 'super_admin')
            <div class="text-right mb-6">
                <button class="btn-style905" type="button" @click="openCreateForm()">
                    + Kullanıcı Kaydı Ekle
                </button>
            </div>
        @endif

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
                                <th class="actions-column">İşlemler</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="border-t border-white/30 {{ !$user->is_active ? 'passive-row' : '' }}">
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->ad_soyad ?? '-' }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->user_phone ?? '-' }}</td>
                                <td>
                                    @if(auth()->user()->role === 'super_admin')
                                        <form class="toggle-form" action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="toggle-switch {{ $user->is_active ? 'active' : '' }}">
                                                <div class="circle"></div>
                                                <span class="label">{{ $user->is_active ? 'Aktif' : 'Pasif' }}</span>
                                            </button>
                                        </form>
                                    @else
                                        <div class="toggle-switch {{ $user->is_active ? 'active' : '' }} readonly">
                                            <div class="circle"></div>
                                            <span class="label">{{ $user->is_active ? 'Aktif' : 'Pasif' }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <!-- Büyüteç Icon'u -->
                                    <svg class="w-8 h-8 cursor-pointer hover:scale-110 transition-transform duration-200 mx-auto text-blue-600 hover:text-blue-800" 
                                         fill="none" 
                                         stroke="currentColor" 
                                         viewBox="0 0 24 24" 
                                         xmlns="http://www.w3.org/2000/svg"
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
                                        <path stroke-linecap="round" 
                                              stroke-linejoin="round" 
                                              stroke-width="2" 
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                                        </path>
                                    </svg>
                                </td>
                                @if(auth()->user()->role === 'super_admin')
                                    <td>
                                        <button @click="openEditForm({
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
                
                <!-- User Avatar - Tablo renkleriyle uyumlu -->
                <div class="user-avatar">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                
                <div class="user-name" x-text="currentUser?.ad || '-'"></div>
                <div class="user-username" x-text="currentUser?.username || ''"></div>
            </div>
            <div class="card-content">
                <div class="info-row">
                    <div class="info-label">Rol</div>
                    <div class="info-value" x-text="currentUser?.role || '-'"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Telefon</div>
                    <div class="info-value" x-text="currentUser?.phone || '-'"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value" x-text="currentUser?.email || '-'"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Oluşturulma</div>
                    <div class="info-value" x-text="currentUser?.created || '-'"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Güncellenme</div>
                    <div class="info-value" x-text="currentUser?.updated || '-'"></div>
                </div>
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
                                <input type="text" name="email" x-model="formData.email" required class="w-full">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700" x-text="isEditing ? 'Yeni Şifre (Boş bırakın)' : 'Şifre'"></label>
                                <input type="password" name="password" x-model="formData.password" :required="!isEditing" class="w-full">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Rol</label>
                                <select name="role" x-model="formData.role" class="w-full" :disabled="!isEditing" :class="{'role-locked': !isEditing}">
                                    <option value="admin">Admin</option>
                                    <option value="security">Güvenlik</option>
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