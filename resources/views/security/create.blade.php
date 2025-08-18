<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@if (session('success'))
    <div class="mb-4 text-green-600">
        {{ session('success') }}
    </div>
@endif
<x-app-layout>
    <style>
        html {
            zoom: 80%;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom right, #f3f4f6, #e2e8f0);
            background-attachment: fixed;
            background-size: cover;
        }

        .center-box {
            max-width: 1300px;
            margin: 3rem auto;
            padding: 2.8rem 3rem;
            border-radius: 2rem;
            background: rgba(255, 255, 255, 0.65); /* cam efekti açık ton */
            backdrop-filter: blur(30px) saturate(160%);
            -webkit-backdrop-filter: blur(30px) saturate(160%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
        }

        .center-box h2,
        .center-box h3 {
            font-size: 1.9rem;
            font-weight: 800;
            margin-bottom: 1.8rem;
            color: #0f172a;
        }

        .center-box label {
            font-weight: 600;
            color: #1e293b;
        }

        .center-box input,
        .center-box textarea,
        .center-box select {
            width: 100%;
            padding: 0.9rem 1.3rem;
            margin-top: 0.4rem;
            border-radius: 1.2rem;
            font-size: 1rem;
            background: #f5f5f5; /* açık gri */
            color: #1e293b;       /* koyu mavi gri yazı */
            border: 1px solid #d1d5db;
            box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .center-box input::placeholder,
        .center-box textarea::placeholder {
            color: #94a3b8;
        }

        .center-box input:focus,
        .center-box textarea:focus,
        .center-box select:focus {
            outline: none;
            background: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25);
        }

        .center-box table {
            width: 100%;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 1.5rem;
            overflow: hidden;
            margin-top: 2.5rem;
            box-shadow: 0 16px 60px rgba(0, 0, 0, 0.05);
        }

        .center-box thead {
            background: rgba(255, 255, 255, 0.85);
            font-weight: 700;
            color: #0f172a;
        }

        .center-box th,
        .center-box td {
            padding: 1rem 1.2rem;
            font-size: 0.95rem;
            text-align: center;
            color: #1e293b;
        }

        .edit-button {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.22);
            color: #2563eb;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            border-radius: 1rem;
            backdrop-filter: blur(6px);
            transition: 0.3s ease;
        }

        .edit-button:hover {
            background: linear-gradient(to right, #3b82f6, #2563eb);
            color: white;
            transform: scale(1.05);
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.4);
        }

        #toggleForm {
            background: linear-gradient(to right, #22c55e, #16a34a);
            padding: 0.6rem 1.6rem;
            font-weight: 700;
            border-radius: 1rem;
            color: white;
            font-size: 1.05rem;
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.3);
            transition: 0.3s ease;
        }

        #toggleForm:hover {
            transform: scale(1.05);
            background: linear-gradient(to right, #15803d, #166534);
        }

        /* Güncelle Butonu */
        .center-box button,
        .x-primary-button {
            background: linear-gradient(to right, #0ea5e9, #3b82f6);
            color: white;
            font-weight: 600;
            padding: 0.7rem 1.8rem;
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.25);
            border: none;
            transition: 0.3s ease;
        }

        .center-box button:hover,
        .x-primary-button:hover {
            transform: scale(1.05);
            background: linear-gradient(to right, #2563eb, #1d4ed8);
        }

        /* Cam netliği & kutuları silme */
        .center-box > div.p-6 {
            background: rgba(255, 255, 255, 0.75) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1.5rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.08);
        }

        #formArea > div,
        .center-box > .bg-white,
        .center-box > .dark\:bg-gray-800,
        .center-box > div.bg-white,
        .center-box > div.shadow-sm,
        .center-box > div.sm\:rounded-lg {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            border-radius: 0 !important;
        }

        main,
        #app,
        html body > div {
            background: transparent !important;
        }

        .submit-animated {
            width: 150px;
            height: 45px;
            border-radius: 50px;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            border: none;
            position: relative;
            overflow: hidden;
            font-size: 14px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(0, 80, 160, 0.2);
        }

        .submit-animated:hover {
            background: #1746c1;
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
            width: 15px;
            height: 15px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
        }

        @keyframes extend {
            0% { width: 200px; height: 50px; border-radius: 50px; }
            50% { background: #22c55e; }
            100% { width: 60px; height: 60px; border-radius: 50%; background: #22c55e; }
        }

        @keyframes disappear {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }

        @keyframes appear {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        select {
            background-color: #f5f5f5; 
            color: #1e293b !important;
            border-radius: 1rem;
            padding: 0.8rem 1rem;
            border: 1px solid #d1d5db;
            appearance: none;
        }

        select option {
            background-color: white;
            color: #1e293b;
        }
    </style>

    <div class="py-6">
        <div class="center-box">

            <!-- Sağ üstte Kayıt Ekle butonu -->
            <div class="flex justify-end mb-4">
                <button id="toggleForm" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Kayıt Ekle</button>
            </div>

            <!-- Form Alanı -->
            <div id="formArea" class="{{ isset($editVisit) ? '' : 'hidden' }}">
                <div class="p-6 mb-6">
                    <form method="POST" action="{{ isset($editVisit) ? route('security.update', $editVisit->id) : route('security.store') }}">
                        @csrf
                        @if(isset($editVisit))
                            @method('PUT')
                        @endif

                        <h3 class="text-lg font-medium mb-4">Ziyaretçi Bilgileri</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tc_no" :value="'T.C. Kimlik No'" />
                                <x-text-input id="tc_no" name="tc_no" type="text" maxlength="11"
                                    value="{{ old('tc_no', $editVisit->visitor->tc_no ?? '') }}"
                                    required class="mt-1 block w-full"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11)"
                                    onblur="getVisitorData()" />
                                @error('tc_no')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div>
                                <x-input-label for="name" :value="'Ad Soyad'" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    autocomplete="off"
                                    value="{{ isset($editVisit) ? $editVisit->visitor->name : old('name') }}" required />
                                @error('name')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <h3 class="text-lg font-medium mt-6 mb-4">Ziyaret Bilgisi</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>
                                <x-input-label for="phone" :value="'Telefon'" />
                                <input id="phone" name="phone" type="text" class="mt-1 block w-full" list="phone_list"
                                    autocomplete="off"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\d{4})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4').slice(0, 14)" 
                                    placeholder="0500 000 00 00"
                                    value="{{ isset($editVisit) ? $editVisit->phone : old('phone') }}" />
                                @error('phone')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                                <datalist id="phone_list"></datalist>
                            </div>

                            <div>
                                <x-input-label for="plate" :value="'Plaka'" />
                                <input name="plate" id="plate" type="text" class="mt-1 block w-full uppercase"
                                    list="plate_list"
                                    autocomplete="off"
                                    maxlength="20"
                                    value="{{ isset($editVisit) ? $editVisit->plate : old('plate') }}" />
                                @error('plate')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                                <datalist id="plate_list"></datalist>
                            </div>
                        

                            <div>
                                <x-input-label for="person_to_visit" :value="'Ziyaret Edilen Kişi'" />
                                <select name="person_to_visit" id="person_to_visit"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-white text-black"
                                    style="color: black !important; background-color: white !important;" required>
                                    <option value="">Kişi Seçiniz</option>
                                    @foreach($people as $person)
                                        <option value="{{ $person->person_name }}"
                                            @selected(old('person_to_visit', $editVisit->person_to_visit ?? '') == $person->person_name)>
                                            {{ $person->person_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('person_to_visit')
                                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mt-4 md:mt-0">
                                <x-input-label for="purpose" :value="'Ziyaret Sebebi'" />
                                <select name="purpose" id="purpose"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-white text-black"
                                    style="color: black !important; background-color: white !important;" required>
                                    <option value="">Sebep Seçiniz</option>
                                    @foreach($reasons as $reason)
                                        <option value="{{ $reason->reason }}"
                                            @selected(old('purpose', $editVisit->purpose ?? '') == $reason->reason)>
                                            {{ $reason->reason }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('purpose')
                                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <!-- <x-primary-button>
                                {{ isset($editVisit) ? 'GÜNCELLE' : 'KAYDET' }}
                            </x-primary-button> -->
                            <button type="button" class="submit-animated" id="submit-button">
                                <span>{{ isset($editVisit) ? 'GÜNCELLE' : 'KAYDET' }}</span>
                                <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="✓">
                            </button>
                            @if(isset($editVisit))
                                <a href="{{ route('security.create') }}" class="text-blue-500 hover:underline">İptal</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- GÜNLÜK ZİYARETÇİ LİSTESİ PANELİ -->
            <div class="glass-panel">
                <h2 class="text-xl font-bold mb-4">Bugünün Ziyaretçi Listesi</h2>
                <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700 dark:text-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                        <tr>
                            <th class="px-4 py-2">Ad Soyad</th>
                            <th class="px-4 py-2">T.C.</th>
                            <th class="px-4 py-2">Telefon</th>
                            <th class="px-4 py-2">Plaka</th>
                            <th class="px-4 py-2">Giriş Saati</th>
                            <th class="px-4 py-2">Ziyaret Sebebi</th>
                            <th class="px-4 py-2">Ziyaret Edilen</th>
                            <th class="px-4 py-2">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300 dark:divide-gray-600">
                        @forelse ($visits as $visit)
                            <tr class="group">
                                <td class="px-4 py-2">{{ $visit->visitor->name }}</td>
                                <td class="px-4 py-2">{{ $visit->visitor->tc_no }}</td>
                                <td class="px-4 py-2">{{ $visit->phone }}</td>
                                <td class="px-4 py-2">{{ $visit->plate }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($visit->entry_time)->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2">{{ $visit->purpose }}</td>
                                <td class="px-4 py-2">{{ $visit->person_to_visit }}</td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ route('security.edit', $visit->id) }}" class="edit-button">Düzenle</a>
                                </td>

                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-2 text-center">Bugün kayıtlı ziyaretçi yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- JS: Form Aç/Kapa -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const toggleFormButton = document.getElementById('toggleForm');
                    const formArea = document.getElementById('formArea');

                    if (toggleFormButton && formArea) {
                        toggleFormButton.addEventListener('click', function (e) {
                            e.preventDefault();
                            formArea.classList.toggle('hidden');
                            if (!formArea.classList.contains('hidden')) {
                                formArea.scrollIntoView({ behavior: 'smooth' });
                            }
                        });
                    }

                    // Kaydet butonunu animasyonlu olarak submit et (1 saniye sonra)
                    const submitButton = document.getElementById('submit-button');
                    if (submitButton) {
                        const form = submitButton.closest('form');
                        submitButton.addEventListener('click', function () {
                            submitButton.focus(); // animasyonu tetikle
                            setTimeout(() => {
                                form.submit();     // 1 saniye sonra submit et
                            }, 1000); // buton animasyon süresi
                        });
                    }
                });
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const tcInput = document.querySelector('input[name="tc_no"]');
                    const nameInput = document.querySelector('input[name="name"]');
                    const phoneInput = document.querySelector('input[name="phone"]');
                    const plateInput = document.querySelector('input[name="plate"]');
                    const phoneList = document.getElementById('phone_list');
                    const plateList = document.getElementById('plate_list');

                    tcInput.addEventListener('change', function () {
                        const tc = tcInput.value.trim();

                        if (tc.length !== 11) return;

                        fetch(`/security/visitor-by-tc/${tc}`)
                            .then(res => res.json())
                            .then(data => {
                                if (!data) return;

                                // Ad Soyad alanını doldur
                                nameInput.value = data.name;

                                // Telefon datalist güncelle
                                phoneList.innerHTML = '';
                                data.phones.forEach(phone => {
                                    const option = document.createElement('option');
                                    option.value = phone;
                                    phoneList.appendChild(option);
                                });

                                // Plaka datalist güncelle
                                plateList.innerHTML = '';
                                data.plates.forEach(plate => {
                                    const option = document.createElement('option');
                                    option.value = plate;
                                    plateList.appendChild(option);
                                });

                                // // Eğer input boşsa en son plakayı otomatik getir
                                // if (!plateInput.value && data.plates.length > 0) {
                                //     plateInput.value = data.plates[0];
                                // }
                            });
                    });
                });
            </script>
            @if ($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const formArea = document.getElementById('formArea');
                        if (formArea && formArea.classList.contains('hidden')) {
                            formArea.classList.remove('hidden');
                            formArea.scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                </script>
            @endif


        </div>
    </div>
</x-app-layout>
