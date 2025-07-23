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

        {{-- Filtreleme ve Sıralama Formu --}}
        <div class="d-flex justify-content-center mb-4 print-hidden"> {{-- print-hidden sınıfı eklendi --}}
            <form id="reportFilterForm" action="{{ route('admin.reports') }}" method="GET" class="d-flex flex-wrap align-items-center justify-content-center gap-3 p-3 border rounded shadow-sm" style="background-color: #f8f9fa;"> {{-- Tasarım iyileştirmeleri --}}
                
                <div class="d-flex align-items-center gap-2">
                    <label for="date_filter" class="form-label mb-0">Rapor Dönemi:</label>
                    <select name="date_filter" id="date_filter" class="form-select w-auto">
                        <option value="" {{ ($dateFilter ?? '') === '' ? 'selected' : '' }}>Tüm Kayıtlar</option>
                        <option value="daily" {{ ($dateFilter ?? '') === 'daily' ? 'selected' : '' }}>Günlük</option>
                        <option value="monthly" {{ ($dateFilter ?? '') === 'monthly' ? 'selected' : '' }}>Aylık</option>
                        <option value="yearly" {{ ($dateFilter ?? '') === 'yearly' ? 'selected' : '' }}>Yıllık</option>
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <label for="sort_order" class="form-label mb-0">Sıralama: </label>
                    <select name="sort_order" id="sort_order" class="form-select w-auto">
                        <option value="desc" {{ ($sortOrder ?? 'desc') === 'desc' ? 'selected' : '' }}>Yeniden Eskiye</option>
                        <option value="asc" {{ ($sortOrder ?? 'desc') === 'asc' ? 'selected' : '' }}>Eskiden Yeniye</option>
                    </select>
                </div>
                
                {{-- Alan seçim hidden inputları --}}
                @if (!empty($fieldsForBlade))
                    @foreach ($fieldsForBlade as $field)
                        <input type="hidden" name="fields[]" value="{{ $field }}">
                    @endforeach
                @endif
                
                {{-- "Göster" butonu kaldırıldı, JavaScript ile otomatik submit yapılacak --}}
            </form>
        </div>
        
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

                <div class="mt-5 d-flex justify-content-center print-hidden" style="gap: 2rem;">
                    <a href="{{ route('report.export', ['fields' => implode(',', $fieldsForBlade), 'date_filter' => ($dateFilter ?? ''), 'sort_order' => ($sortOrder ?? 'desc')]) }}"
                       class="btn shadow-sm d-flex align-items-center me-3"
                       style="background-color: #28a745; color: #ffffff; border-radius: 8px; padding: 0.5rem 1rem; font-size: 1rem; font-weight: bold; height: 40px;">
                        <i class="bi bi-file-earmark-excel me-2"></i> Excel'e Aktar
                    </a>
                    <button onclick="window.print()"
                            class="btn shadow-sm d-flex align-items-center ms-3"
                            style="background-color: #003366; color: #ffffff; border-radius: 8px; padding: 0.5rem 1rem; font-size: 1rem; font-weight: bold; height: 40px;">
                        <i class="bi bi-printer me-2"></i> Yazdır
                    </button>
                </div>
            </div>
        @endif
    </div>

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

    <script>
        // Sıralama seçeneği değiştiğinde formu otomatik gönder
        document.getElementById('sort_order').addEventListener('change', function() {
            document.getElementById('reportFilterForm').submit();
        });

        // Rapor Dönemi seçeneği değiştiğinde formu otomatik gönder
        document.getElementById('date_filter').addEventListener('change', function() {
            document.getElementById('reportFilterForm').submit();
        });
    </script>
</x-app-layout>