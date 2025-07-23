@if (session('success'))
    <div class="mb-4 text-green-600">
        {{ session('success') }}
    </div>
@endif
<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

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
                             <x-text-input id="tc_no" name="tc_no" type="text" 
                                maxlength="11" pattern="[0-9]{11}" 
                                class="mt-1 block w-full" required 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11)"
                                value="{{ isset($editVisit) ? $editVisit->visitor->tc_no : old('tc_no') }}" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="'Telefon'" />
                             <x-text-input id="phone" name="phone" type="text" 
                                class="mt-1 block w-full" required 
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

                                <x-text-input id="plate_city" name="plate_city" type="text" class="mt-1 w-16 uppercase"
                                    maxlength="6" required value="{{ old('plate_city', $plateParts[0]) }}" />

                                <x-text-input id="plate_letters" name="plate_letters" type="text" class="mt-1 w-20 uppercase"
                                    maxlength="6" required value="{{ old('plate_letters', $plateParts[1]) }}" />

                                <x-text-input id="plate_number" name="plate_number" type="text" class="mt-1 w-24 uppercase"
                                    maxlength="10" required value="{{ old('plate_number', $plateParts[2]) }}" />
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium mt-6 mb-4">Ziyaret Bilgisi</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="entry_time" :value="'Giriş Saati'" />
                            <x-text-input id="entry_time" name="entry_time" type="datetime-local" 
                                class="mt-1 block w-full" 
                                value="{{ isset($editVisit) ? $editVisit->entry_time : now()->format('Y-m-d\TH:i') }}" 
                                readonly />
                        </div>
                        <div>
                            <x-input-label for="person_to_visit" :value="'Ziyaret Edilen Kişi'" />
                            <x-text-input id="person_to_visit" name="person_to_visit" type="text" class="mt-1 block w-full"
                                value="{{ isset($editVisit) ? $editVisit->person_to_visit : old('person_to_visit') }}" required />
                        </div>

                        <div class="mt-4">
                            <label for="purpose" class="block font-medium text-sm text-gray-700">Ziyaret Sebebi</label>
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

                <!-- Listele Butonu -->
                <div class="mt-10 text-center">
                    <button id="toggleList" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Listele</button>
                </div>

                <!-- Günlük Ziyaretçi Listesi -->
                <div id="dailyList" class="mt-6 hidden">
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
                                    <td class="px-4 py-2">{{ $visit->entry_time }}</td>
                                    <td class="px-4 py-2">{{ $visit->purpose }}</td>
                                    <td class="px-4 py-2">{{ $visit->person_to_visit }}</td>
                                    <td class="px-4 py-2 text-sm text-right whitespace-nowrap">
                                        <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition">
                                            <a href="{{ route('security.edit', $visit->id) }}" class="text-blue-600 hover:underline">Düzenle</a>
                                            <form method="POST" action="{{ route('security.destroy', $visit->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Emin misin?')">Kaldır</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-2 text-center">Bugün kayıtlı ziyaretçi yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- JS Toggle -->
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const btn = document.getElementById('toggleList');
                        const list = document.getElementById('dailyList');
                        btn.addEventListener('click', function (e) {
                            e.preventDefault();
                            list.classList.toggle('hidden');
                        });
                    });
                </script>

            </div>
        </div>
    </div>
</x-app-layout>
