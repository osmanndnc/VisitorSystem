<x-app-layout>
    <style>
        html { zoom: 80% }
        body { background: linear-gradient(135deg, #f5f7fa, #e4ebf1); font-family:'Segoe UI', sans-serif }

        .card {
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.87);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }
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
        .id-column {
            width: 80px;
        }
        .name-column {
            width: 200px;
        }
        .username-column {
            width: 150px;
        }

        .phone-column {
            width: 150px;
        }

        .status-column {
            width: 120px;
        }

        .detail-column {
            width: 100px;
        }

        .actions-column {
            width: 120px;
        }

        .edit-btn {
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
        .edit-btn:hover {
            background: linear-gradient(135deg, #bae6fd 0%, #7dd3fc 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.3);
        }
        .btn-edit:active {
            transform: translateY(0);
        }
        .user-detail-card {
            position: fixed; 
            top: 50%; left: 50%; 
            transform: translate(-50%, -50%) scale(0.9);
            width: 430px;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
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
        .card-header {
            padding: 25px 20px 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.4);
            position: relative;
        }
        .card-close {
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
        }
        .card-close:hover { background: rgba(255,255,255,0.5) }
        .user-avatar {
            width: 80px; height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.5);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 0 auto 12px;
            background: #e5e7eb;
        }
        .user-name { font-size: 22px; font-weight: 700; }
        .user-username { font-size: 14px; color: #555; }
        .status-badge {
            position: absolute;
            top: 20px; left: 20px;
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 12px; font-weight: 600;
            letter-spacing: 0.5px;
        }
        .status-badge.aktif {
            background: rgba(34,197,94,0.15);
            color: #15803d;
            border: 1px solid rgba(34,197,94,0.3);
        }
        .status-badge.pasif {
            background: rgba(239,68,68,0.15);
            color: #991b1b;
            border: 1px solid rgba(239,68,68,0.3);
        }
        .card-content { padding: 20px; }
        .info-row {
            display: flex; justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            font-size: 14px;
        }
        .info-label { font-weight: 600; color: #333; }
        .info-value { color: #555; }
        .card-actions {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .toggle-btn {
            width: 280px;
            padding: 16px 24px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 15px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin: 0 auto;
        }
        .toggle-btn.aktif {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%);
            color: white;
            box-shadow:0 8px 25px rgba(239, 68, 68, 0.4);
        }
        .toggle-btn.aktif:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 50%, #991b1b 100%);
            transform: translateY(-3px) scale(1.02);
            box-shadow:0 15px 35px rgba(239, 68, 68, 0.5);
        }
        .toggle-btn.pasif {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }
        .toggle-btn.pasif:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.5);
        }
        
        .toggle-btn:active {
            transform: translateY(-1px) scale(0.98);
        }
        .card-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 40;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        .card-backdrop.show { opacity: 1; pointer-events: auto; }
        .toggle-switch {
            width: 70px; height: 32px; background: #cbd5e1; border-radius: 999px; border: none;
            cursor: pointer; position: relative; transition: background 0.4s ease;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        .toggle-switch.active { background: linear-gradient(90deg, #22c55e, #16a34a); }
        .toggle-switch .circle {
            position: absolute; top: 2px; left: 3px; width: 28px; height: 28px; background: #fff;
            border-radius: 50%; transition: left .3s ease; box-shadow: 0 2px 6px rgba(0,0,0,.2);
        }
        .toggle-switch.active .circle { left: 39px; }
        .toggle-switch .label {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
            font-size: 11px; color: #fff; font-weight: 600; text-shadow: 0 0 3px rgba(0,0,0,.7);
            pointer-events: none; z-index: 1;
        }

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

        .btn-style905{
            background:#1d4ed8; border:none; color:#fff; padding:12px 24px; font-size:16px; font-weight:600;
            border-radius:16px; cursor:pointer; transition:.3s; box-shadow:0 6px 12px rgba(0,0,0,.15)
        }
        .btn-style905:hover{ background:#1746c1; transform:translateY(-2px) }

        a.text-blue-600{
            display:inline-block; padding:8px 18px;
            background:linear-gradient(135deg,rgba(59,130,246,.15),rgba(59,130,246,.05));
            border:1px solid rgba(59,130,246,.3); border-radius:12px; color:#1d4ed8; font-weight:600; font-size:14px;
            text-align:center; box-shadow:0 4px 10px rgba(59,130,246,.1); transition:.25s; backdrop-filter:blur(6px)
        }
        a.text-blue-600:hover{ transform:translateY(-1px) scale(1.04); background:linear-gradient(135deg,#3b82f6,#2563eb); color:#fff; border-color:transparent; box-shadow:0 6px 15px rgba(37,99,235,.2) }
        input[type="text"], input[type="email"], input[type="password"], select{
            background:#fff; border:1px solid #d1d5db; padding:10px 12px; font-size:14px; border-radius:8px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,.05); transition: border-color .3s ease;
        }
        input:focus, select:focus{ border-color:#2563eb; outline:none; box-shadow:0 0 0 2px rgba(37,99,235,.2); }

        tr.passive-row{ opacity:.55; background:#f9fafb !important; transition: opacity .3s; }

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

        .role-locked{
        pointer-events: none;
        background-color: #f9fafb;
        color:#111827;
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
        background-image: none;
        }
        .role-locked::-ms-expand{ display:none; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kayıtFormu = document.getElementById('kullanici-formu');
            const toggleFormButton = document.getElementById('toggle-form-button');

            if (toggleFormButton && kayıtFormu) {
                toggleFormButton.addEventListener('click', function () {
                    kayıtFormu.classList.toggle('hidden');

                    if (!kayıtFormu.classList.contains('hidden')) {
                        kayıtFormu.scrollIntoView({ behavior: 'smooth' });

                        const form = kayıtFormu.querySelector('form');
                        form.action = "{{ route('security.users.store') }}";
                        kayıtFormu.querySelector('h2').innerText = "Yeni Kullanıcı Ekle";
                        kayıtFormu.querySelector('input[name="ad_soyad"]').value = "";
                        kayıtFormu.querySelector('input[name="user_phone"]').value = "";
                        kayıtFormu.querySelector('input[name="username"]').value = "";
                        kayıtFormu.querySelector('input[name="email"]').value = "";
                        kayıtFormu.querySelector('input[name="password"]').value = "";
                        kayıtFormu.querySelector('select[name="role"]').value = "security";

                        const methodInput = kayıtFormu.querySelector('input[name="_method"]');
                        if (methodInput) methodInput.remove();
                    }
                });
            }

            const cancelEditButton = document.getElementById('cancel-edit-button');
            if (cancelEditButton) {
                cancelEditButton.addEventListener('click', function () {
                    window.location.href = "{{ route('security.users.index') }}";
                });
            }

            document.querySelectorAll('.toggle-form').forEach(form => {
                form.querySelector('.toggle-switch').addEventListener('click', function () {
                    this.classList.toggle('active');
                    form.submit();
                });
            });
            const submitBtn = document.getElementById('submit-button');
            if (submitBtn) {
            const form = submitBtn.closest('form');
            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();                 // hemen gönderme
                submitBtn.blur();                   // her tıkta animasyon sıfırlansın
                requestAnimationFrame(() => {
                submitBtn.focus();                // :focus animasyonunu tetikle
                setTimeout(() => form.submit(), 1000); // 1 sn sonra gönder
                });
            });
            }
        });
    </script>

    <div class="py-6 max-w-7xl mx-auto" x-data="{ showCard:false, currentUser:null }">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Güvenlikçi Listesi</h2>
        </div>

        <div class="text-right mb-6">
            @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                <button id="toggle-form-button" class="btn-style905" type="button">
                    + Kullanıcı Kaydı Ekle
                </button>
            @endif
        </div>

        @if(auth()->user()->hasAnyRole(['super_admin', 'admin']))
            {{-- FORM KARTI: yalnızca görünüm için card sınıfları eklendi --}}
            <div id="kullanici-formu" class="card {{ isset($editUser) ? '' : 'hidden' }}">
                <div class="card-header">
                    <div class="title">{{ isset($editUser) ? 'Kullanıcıyı Güncelle' : 'Yeni Kullanıcı Ekle' }}</div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ isset($editUser) ? route('security.users.update', $editUser->id) : route('security.users.store') }}">
                        @csrf
                        @if(isset($editUser)) @method('PUT') @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium">Ad Soyad</label>
                                <input type="text" name="ad_soyad" value="{{ old('ad_soyad', $editUser->ad_soyad ?? '') }}" required class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium">Telefon</label>
                                <input type="text" name="user_phone" value="{{ old('user_phone', $editUser->user_phone ?? '') }}" class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium">Kullanıcı Adı</label>
                                <input type="text" name="username" value="{{ old('username', $editUser->username ?? '') }}" required class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium">Email</label>
                                <input type="email" name="email" value="{{ old('email', $editUser->email ?? '') }}" required class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium">
                                    {{ isset($editUser) ? 'Yeni Şifre' : 'Şifre' }}
                                </label>
                                <input type="password" name="password" class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="mb-4">
                                @php
                                    $isEdit = isset($editUser);
                                    $defaultRole = 'security'; 
                                    $selectedRole = old('role', $isEdit ? $editUser->role : $defaultRole);
                                @endphp

                                <label class="block text-sm font-medium">Rol</label>

                                @if($isEdit)
                                    {{-- DÜZENLE: normal select --}}
                                    <select name="role" class="w-full border rounded px-3 py-2">
                                        <option value="admin"    {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="security" {{ $selectedRole === 'security' ? 'selected' : '' }}>Güvenlik</option>
                                    </select>
                                @else
                                    {{-- YENİ KAYIT: select kilitli + hidden input (disabled alan submit olmaz) --}}
                                    <select class="w-full border rounded px-3 py-2 role-locked" disabled>
                                        <option value="admin"    {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="security" {{ $selectedRole === 'security' ? 'selected' : '' }}>Güvenlik</option>
                                    </select>
                                    <input type="hidden" name="role" id="roleHidden" value="{{ $defaultRole }}">
                                @endif
                            </div>
                        </div>

                        <input type="hidden" name="is_active" value="1">

                        <div class="flex justify-end">
                            <button type="submit" class="submit-animated" id="submit-button">
                                <span>{{ isset($editUser) ? 'Güncelle' : 'Kaydet' }}</span>
                                <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="✓">
                            </button>
                            @if(isset($editUser))
                                <button type="button" id="cancel-edit-button" class="btn-style905 bg-red-600 hover:bg-red-700 ml-4">İptal</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- LİSTE KARTI: tabloyu kart + scroll içine aldık --}}
        <div class="card table-card mt-8">
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
                            <th class="actions-column">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="border-t border-white/30 {{ !$user->is_active ? 'passive-row' : '' }}">
                                <td class="id-column">{{ $user->id }}</td>
                                <td class="name-column">{{ $user->ad_soyad ?? '-' }}</td>
                                <td class="username-column">{{ $user->username }}</td>
                                <td class="phone-column">{{ $user->user_phone ?? '-' }}</td>
                                <td class="status-column">
                                    <form class="toggle-form" action="{{ route('security.users.toggle', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="toggle-switch {{ $user->is_active ? 'active' : '' }}">
                                            <div class="circle"></div>
                                            <span class="label">{{ $user->is_active ? 'Aktif' : 'Pasif' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="detail-column">
                                    <img src="{{ asset('images/touch.gif') }}" 
                                         class="w-10 h-10 cursor-pointer hover:scale-110 transition-transform duration-200 mx-auto"
                                         alt="Detay Görüntüle"
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
                                                 editUrl: @js(route('security.users.index', ['edit' => $user->id])),
                                                 toggleUrl: @js(route('security.users.toggle', $user->id)),
                                             };
                                             showCard = true;">
                                </td>
                                <td class="actions-column">
                                    @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                                        <a href="{{ route('security.users.index', ['edit' => $user->id]) }}" class="edit-btn">
                                            Düzenle
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Backdrop -->
        <div class="card-backdrop" :class="{ 'show': showCard }" @click="showCard = false"></div>

        <!-- Kullanıcı Detay Kartı -->
        <div class="user-detail-card" :class="{ 'show': showCard }" @click.stop>
            <div class="card-header">
                <span class="status-badge" :class="currentUser?.isActive ? 'aktif' : 'pasif'" 
                      x-text="currentUser?.isActive ? 'AKTİF' : 'PASİF'"></span>
                <button class="card-close" @click="showCard = false">&times;</button>
                <img src="{{ asset('images/id-badge.gif') }}" class="user-avatar" alt="Kullanıcı">
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
                @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                    <form :action="currentUser?.toggleUrl || '#'" method="POST" class="inline-flex w-full">
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
    </div>
</x-app-layout>