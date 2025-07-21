<x-app-layout>
    <div class="container py-5" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <h2 class="mb-4 text-center" style="
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            font-size: 2.8rem;
            color: #003366;
            text-shadow: none;
            font-style: normal;
            margin-bottom: 2rem;
        ">Ziyaretçi Raporu</h2>

        @if ($data->isEmpty())
            <div class="alert alert-warning text-center" role="alert">
                Gösterilecek veri bulunamadı.
            </div>
        @else
            <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                <div class="table-responsive" style="max-width: 90%;">
                    <table class="table table-striped table-hover table-bordered shadow-sm">
                        <thead style="background-color: #003366; color: #ffffff;">
                            <tr>
                                <th class="text-center" style="min-width: 80px;">ID</th>
                                <th class="text-center" style="min-width: 150px;">Onaylayan</th>
                                @foreach ($fieldsForBlade as $field)
                                    <th class="text-center" style="min-width: 150px;">
                                        @switch($field)
                                            @case('entry_time') Giriş Tarihi @break
                                            @case('name') Ad-Soyad @break
                                            @case('tc_no') T.C. No @break
                                            @case('phone') Telefon @break
                                            @case('plate') Plaka @break
                                            @case('purpose') Ziyaret Sebebi @break
                                            @case('person_to_visit') Ziyaret Edilen Kişi @break
                                            @default {{ ucfirst(str_replace('_', ' ', $field)) }}
                                        @endswitch
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $row)
                                <tr>
                                    <td class="text-center" style="white-space: normal; min-width: 80px;">{{ $row['id'] ?? '-' }}</td>
                                    <td class="text-center" style="white-space: normal; min-width: 150px;">{{ $row['approved_by'] ?? '-' }}</td>
                                    @foreach ($fieldsForBlade as $field)
                                        <td class="text-center" style="white-space: normal; min-width: 150px;">{{ $row[$field] ?? '-' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- DEĞİŞİKLİK BURADA: Butonların bulunduğu div'e ek margin sınıfları eklendi --}}
                <div class="mt-5 d-flex justify-content-center print-hidden" style="gap: 3rem;"> {{-- gap 3rem olarak bırakıldı --}}
                    {{-- Excel'e Aktar butonu güncellendi --}}
                    <a href="{{ route('report.export', ['fields' => implode(',', $fieldsForBlade)]) }}"
                       class="btn shadow-sm d-flex align-items-center me-3" {{-- me-3 ile sağa margin eklendi --}}
                       style="background-color: #28a745; color: #ffffff; border-radius: 8px; padding: 0.5rem 1rem; font-size: 1rem; font-weight: bold; height: 40px;">
                        <i class="bi bi-file-earmark-excel me-2"></i> Excel'e Aktar
                    </a>
                    {{-- Yazdır butonu güncellendi --}}
                    <button onclick="window.print()"
                            class="btn shadow-sm d-flex align-items-center ms-3" {{-- ms-3 ile sola margin eklendi --}}
                            style="background-color: #003366; color: #ffffff; border-radius: 8px; padding: 0.5rem 1rem; font-size: 1rem; font-weight: bold; height: 40px;">
                        <i class="bi bi-printer me-2"></i> Yazdır
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Yazdırma sırasında butonları gizlemek için stil --}}
    <style>
        @media print {
            .print-hidden {
                display: none !important;
            }
            body {
                background-color: #fff !important;
            }
            .container {
                box-shadow: none !important;
            }
            thead[style] {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .print-hidden a, .print-hidden button {
                display: none !important;
            }
        }
    </style>
</x-app-layout>