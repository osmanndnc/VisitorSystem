<x-app-layout>
    <style>
        html {
            zoom: 80%;
        }
        body {
            background: #f1f5f9;
        }
        .toggle-switch {
            position: relative;
            width: 80px;
            height: 36px;
            background: linear-gradient(90deg, #ccc, #e0e0e0);
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: background 0.4s ease;
        }

        .toggle-switch.active {
            background: linear-gradient(90deg, #00ff3cb9, #038521ea);
        }

        .toggle-switch .circle {
            position: absolute;
            top: 3px;
            left: 4px;
            width: 30px;
            height: 30px;
            background-color: white;
            border-radius: 50%;
            transition: left 0.3s ease;
        }

        .toggle-switch.active .circle {
            left: 46px;
        }

        .toggle-switch .label {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: white;
            font-weight: bold;
        }

        /* Animasyonlu submit butonu */
        .submit-animated {
            width: 200px;
            height: 50px;
            border-radius: 50px;
            background: #fff;
            border: 3px solid #6fb07f;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            font-size: 18px;
            font-weight: 600;
            color: #6fb07f;
            cursor: pointer;
        }

        .submit-animated:focus {
            animation: extend 1s ease-in-out forwards;
        }

        .submit-animated:focus span {
            animation: disappear 1s ease-in-out forwards;
        }

        .submit-animated:focus img {
            animation: appear 1s ease-in-out forwards;
        }

        .submit-animated img {
            position: absolute;
            width: 28px;
            height: 28px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
        }

        @keyframes extend {
            0% { width: 200px; height: 50px; border-radius: 50px; }
            50% { background: #6fb07f; }
            100% { width: 60px; height: 60px; border-radius: 50%; background: #6fb07f; }
        }

        @keyframes disappear {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }

        @keyframes appear {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .btn-style905 {
            position: relative;
            background-color: #716eef;
            border: 5px solid #716eef;
            color: #fff;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .btn-style905::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 120%;
            height: 155%;
            border: 1px solid #3936af;
            border-radius: 20px;
            transform: translate(-50%, -50%) scale(1.1);
            opacity: 0;
            transition: all 0.25s;
        }

        .btn-style905:hover {
            background-color: #3936af;
            border-color: #716eef;
            border-style: inset;
            border-radius: 20px;
        }

        .btn-style905:hover::before {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleForms = document.querySelectorAll('.toggle-form');
            toggleForms.forEach(form => {
                form.querySelector('.toggle-switch').addEventListener('click', function () {
                    this.classList.toggle('active');
                    form.submit();
                });
            });

            // Kayıt formunu aç/kapat
            const toggleFormButton = document.getElementById('toggle-form-button');
            const kayıtFormu = document.getElementById('kullanici-formu');
            toggleFormButton.addEventListener('click', function () {
                kayıtFormu.classList.toggle('hidden');
                if (!kayıtFormu.classList.contains('hidden')) {
                    kayıtFormu.scrollIntoView({ behavior: 'smooth' });
                }
            });
            
            // Kaydet butonunu animasyonla gönder
            const submitButton = document.getElementById('submit-button');
            const form = document.getElementById('kullanici-ekle-form');

            if (submitButton && form) {
                submitButton.addEventListener('click', function () {
                    submitButton.focus(); // animasyonu tetikle
                    setTimeout(() => {
                        form.submit(); // 1 sn sonra submit et
                    }, 1000); // süren animasyon kadar
                });
            }
        });
    </script>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Admin Kullanıcıları</h2>
            @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
                <button id="toggle-form-button" class="btn-style905">
                    + Kullanıcı Kaydı Ekle
                </button>
            @endif
        </div>

        <table class="table-auto w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">#</th>
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
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
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
                            @if(auth()->user()->role === 'super_admin')
                                <a href="{{ route('security.users.edit', $user->id) }}" class="text-blue-600">Düzenle</a>
                            @else
                                <span class="text-gray-400 cursor-not-allowed">Düzenle</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(auth()->user()->hasAnyRole(['admin', 'super_admin']))
        <div id="kullanici-formu" class="bg-white shadow rounded p-6 mt-10 hidden">
            <h2 class="text-lg font-bold mb-4">Yeni Kullanıcı Kaydı</h2>
            <form id="kullanici-ekle-form" method="POST" action="{{ route('security.users.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Kullanıcı Adı</label>
                    <input type="text" name="username" id="username" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Şifre</label>
                    <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                    <select name="role" id="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Rol Seçin</option>
                        <option value="security">Güvenlik</option>
                        @if(auth()->user()->role === 'super_admin')
                            <option value="admin">Admin</option>
                        @endif
                    </select>
                </div>

                <input type="hidden" name="is_active" value="1">

                <button type="button" class="submit-animated" id="submit-button">
                    <span>Kaydet</span>
                    <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="✓">
                </button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
