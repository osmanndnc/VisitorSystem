<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="center-box bg-white rounded-2xl shadow-md p-8 overflow-x-auto">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Ziyaretçi Listesi</h2>

                @php
                $fieldsList = [
                    'entry_time' => 'Giriş Tarihi',
                    'name' => 'Ad-Soyad',
                    'tc_no' => 'TC',
                    'phone' => 'Telefon Numarası',
                    'plate' => 'Plaka',
                    'purpose' => 'Ziyaret Sebebi',
                    'person_to_visit' => 'Ziyaret Edilen Kişi',
                    'approved_by' => 'Onaylayan'
                ];
                @endphp

                <form method="GET" action="{{ route('admin.reports') }}">
                    <table class="w-full min-w-[600px] text-sm text-left border-collapse bg-white">
                        <thead class="bg-gray-100 text-gray-700 font-semibold">
                            <tr>
                                <th class="py-2 px-4">ID</th>
                                @foreach($fieldsList as $field => $label)
                                    <th class="py-2 px-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            {{ $label }}
                                            <input type="checkbox" name="fields[]" value="{{ $field }}" {{ (is_array($fields) && in_array($field, $fields)) ? 'checked' : '' }} class="w-4 h-4 accent-gray-800">
                                        </label>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="text-gray-800">
                            @foreach($visits as $visit)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4">{{ $visit->id }}</td>
                                    @foreach(array_keys($fieldsList) as $field)
                                        @if(empty($fields) || in_array($field, $fields))
                                            <td class="py-2 px-4">
                                                @switch($field)
                                                    @case('entry_time')
                                                        {{ $visit->entry_time }}
                                                        @break
                                                    @case('name')
                                                        {{ $visit->visitor->name ?? '-' }}
                                                        @break
                                                    @case('tc_no')
                                                        {{ $visit->visitor->tc_no ?? '-' }}
                                                        @break
                                                    @case('phone')
                                                        {{ $visit->visitor->phone ?? '-' }}
                                                        @break
                                                    @case('plate')
                                                        {{ $visit->visitor->plate ?? '-' }}
                                                        @break
                                                    @case('purpose')
                                                        {{ $visit->purpose }}
                                                        @break
                                                    @case('person_to_visit')
                                                        {{ $visit->person_to_visit }}
                                                        @break
                                                    @case('approved_by')
                                                        @if(isset($visit->approver) && !empty($visit->approver->name))
                                                            {{ $visit->approver->name }}
                                                        @elseif(!empty($visit->approved_by))
                                                            {{ $visit->approved_by }}
                                                        @endif
                                                        @break
                                                @endswitch
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="mt-6 px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition">
                        Göster
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
