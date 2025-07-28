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
            background: #f1f5f9;
        }
        .edit-button {
            display: inline-block;
            padding: 8px 18px;
            background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05));
            border: 1px solid rgba(59,130,246,0.3);
            border-radius: 12px;
            color: #1d4ed8;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(59,130,246,0.1);
            transition: all 0.25s ease-in-out;
            backdrop-filter: blur(6px);
        }

        .edit-button:hover {
            transform: translateY(-1px) scale(1.04);
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 6px 15px rgba(37,99,235,0.2);
        }
        .center-box label,
        .center-box input,
        .center-box textarea,
        .center-box select {
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .center-box h3 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .center-box {
            position: relative;
            width: 90%;
            max-width: 1500px;
            margin: 2rem auto;
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 2.5rem;

            
            /* YAZIYI BÜYÜTÜYORUZ VE DENGELİ HALE GETİRİYORUZ */
            font-size: 1.05rem;
            line-height: 1.7;
            font-weight: 500;
        }

        .center-box table {
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .page-title {
            font-size: 2.8rem; /* zaten büyük */
            font-weight: 800;
            color: #003366;
            margin-bottom: 2rem;
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <form method="POST" action="{{ isset($editVisit) ? route('security.update', $editVisit->id) : route('security.store') }}">
                        @csrf
                        @if(isset($editVisit))
                            @method('PUT')
                        @endif

                        <h3 class="text-lg font-medium mb-4">Ziyaretçi Bilgileri</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="name" :value="'Ad Soyad'" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    value="{{ isset($editVisit) ? $editVisit->visitor->name : old('name') }}" required />
                            </div>

                            <div>
                                <x-input-label for="tc_no" :value="'T.C. Kimlik No'" />
                                <x-text-input id="tc_no" name="tc_no" type="text" maxlength="11" pattern="[0-9]{11}"
                                    class="mt-1 block w-full" required 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11)"
                                    value="{{ isset($editVisit) ? $editVisit->visitor->tc_no : old('tc_no') }}" />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="'Telefon'" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" required 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\d{4})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4').slice(0, 14)" 
                                    placeholder="0500 000 00 00"
                                    value="{{ isset($editVisit) ? $editVisit->visitor->phone : old('phone') }}" />
                            </div>

                            <div>
                                <x-input-label for="plate" :value="'Plaka'" />
                                <div class="flex gap-2">
                                    @php
                                        $plate = isset($editVisit) ? $editVisit->visitor->plate : '';
                                        $plateParts = array_pad(explode(' ', $plate), 3, '');
                                    @endphp

                                    <x-text-input name="plate_city" type="text" class="mt-1 w-16 uppercase"
                                        maxlength="6" required value="{{ old('plate_city', $plateParts[0]) }}" />

                                    <x-text-input name="plate_letters" type="text" class="mt-1 w-20 uppercase"
                                        maxlength="6" required value="{{ old('plate_letters', $plateParts[1]) }}" />

                                    <x-text-input name="plate_number" type="text" class="mt-1 w-24 uppercase"
                                        maxlength="10" required value="{{ old('plate_number', $plateParts[2]) }}" />
                                </div>
                            </div>
                        </div>

                        <h3 class="text-lg font-medium mt-6 mb-4">Ziyaret Bilgisi</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="person_to_visit" :value="'Ziyaret Edilen Kişi'" />
                                <x-text-input id="person_to_visit" name="person_to_visit" type="text" class="mt-1 block w-full"
                                    value="{{ isset($editVisit) ? $editVisit->person_to_visit : old('person_to_visit') }}" required />
                            </div>

                            <div class="mt-4 md:mt-0">
                                <x-input-label for="purpose" :value="'Ziyaret Sebebi'" />
                                <textarea name="purpose" id="purpose" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ isset($editVisit) ? $editVisit->purpose : old('purpose') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <x-primary-button>
                                {{ isset($editVisit) ? 'GÜNCELLE' : 'KAYDET' }}
                            </x-primary-button>
                            @if(isset($editVisit))
                                <a href="{{ route('security.create') }}" class="text-blue-500 hover:underline">İptal</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- GÜNLÜK ZİYARETÇİ LİSTESİ PANELİ -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
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
                                <td class="px-4 py-2">{{ $visit->visitor->phone }}</td>
                                <td class="px-4 py-2">{{ $visit->visitor->plate }}</td>
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
                document.addEventListener("DOMContentLoaded", function () {
                    const btn = document.getElementById('toggleForm');
                    const form = document.getElementById('formArea');
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        form.classList.toggle('hidden');
                    });
                });
            </script>

        </div>
    </div>
</x-app-layout>
