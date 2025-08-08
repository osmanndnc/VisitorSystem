<x-app-layout>
    <style>
        html {
            zoom: 80%;
        }

        body {
            background: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sec-page .card{
            border-radius: 22px;
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 12px 24px rgba(15,23,42,.06);
        }
        .sec-page .card-header{
            display:flex;align-items:center;justify-content:space-between;
            padding: 12px 16px 0 16px;
        }
        .sec-page .card-body{ padding: 18px 16px 20px 16px; }
        .sec-page .title{ font-weight:800; font-size:20px; color:#0f172a; }

        .sec-page .table-card { padding: 10px; }
        .sec-page .table-scroll { overflow:auto; border-radius: 14px; }

        /* Mevcut stillerin AYNEN */
        .toggle-switch {
            width: 80px; height: 36px; background: #cbd5e1; border-radius: 999px; border: none;
            cursor: pointer; position: relative; transition: background 0.4s ease;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        .toggle-switch.active { background: linear-gradient(90deg, #22c55e, #16a34a); }
        .toggle-switch .circle {
            position: absolute; top: 3px; left: 4px; width: 30px; height: 30px; background: #fff;
            border-radius: 50%; tran sition: left .3s ease; box-shadow: 0 2px 6px rgba(0,0,0,.2);
        }
        .toggle-switch.active .circle { left: 46px; }
        .toggle-switch .label {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
            font-size: 12px; color: #fff; font-weight: 600; text-shadow: 0 0 3px rgba(0,0,0,.7);
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

        /* Form alanları */
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

    <!-- <script>
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
    </script> -->
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

    <div class="py-6 max-w-7xl mx-auto sec-page">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Güvenlik Kullanıcıları</h2>
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
                                <!-- <label class="block text-sm font-medium">Rol</label>
                                <select name="role" required class="w-full border rounded px-3 py-2">
                                    <option value="">Rol Seçin</option>
                                    <option value="admin" {{ old('role', $editUser->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="security" {{ old('role', $editUser->role ?? '') == 'security' ? 'selected' : '' }}>Güvenlik</option>
                                </select> -->
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
            <!-- <div class="card-header"><div class="title">Kullanıcı Listesi</div></div> -->
            <div class="table-scroll">
                <table class="table-auto w-full bg-white rounded">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Ad Soyad</th>
                            <th class="px-4 py-2">Telefon</th>
                            <th class="px-4 py-2">Kullanıcı Adı</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Rol</th>
                            <th class="px-4 py-2">Oluşturulma</th>
                            <th class="px-4 py-2">Güncellenme</th>
                            <th class="px-4 py-2">Durum</th>
                            <th class="px-4 py-2">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                            <tr class="border-t {{ !$user->is_active ? 'passive-row' : '' }}">
                                <td class="px-4 py-2">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $user->ad_soyad }}</td>
                                <td class="px-4 py-2">{{ $user->user_phone }}</td>
                                <td class="px-4 py-2">{{ $user->username }}</td>
                                <td class="px-4 py-2">{{ $user->email }}</td>
                                <td class="px-4 py-2">{{ ucfirst($user->role) }}</td>
                                <td class="px-4 py-2">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2">{{ $user->updated_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2">
                                    <form class="toggle-form" action="{{ route('security.users.toggle', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="toggle-switch {{ $user->is_active ? 'active' : '' }}">
                                            <div class="circle"></div>
                                            <span class="label">{{ $user->is_active ? 'Aktif' : 'Pasif' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-2">
                                    @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                                        <a href="{{ route('security.users.index', ['edit' => $user->id]) }}" class="text-blue-600">Düzenle</a>
                                    @else
                                        <span class="text-gray-400 cursor-not-allowed">Düzenle</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
