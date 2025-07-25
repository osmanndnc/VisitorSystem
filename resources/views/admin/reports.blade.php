<x-app-layout>
    <div class="container py-5" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        @php
            $reportTitle = '';
            switch ($dateFilter ?? '') {
                case 'daily': $reportTitle = 'Günlük'; break;
                case 'monthly': $reportTitle = 'Aylık'; break;
                case 'yearly': $reportTitle = 'Yıllık'; break;
                default: $reportTitle = 'Tüm'; break;
            }
        @endphp

        <h2 class="mb-4 text-center" style="
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            font-size: 2.8rem;
            color: #003366;
            text-shadow: none;
            font-style: normal;
            margin-bottom: 2rem;
        ">
            {{ $reportTitle }} Ziyaretçi Raporu
        </h2>

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
                                <th class="text-center" style="min-width: 150px;">Ekleyen</th>
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
                                    <td class="text-center">{{ $row['id'] ?? '-' }}</td>
                                    <td class="text-center">{{ $row['approved_by'] ?? '-' }}</td>
                                    @foreach ($fieldsForBlade as $field)
                                        <td class="text-center">{{ $row[$field] ?? '-' }}</td>
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

                    <button id="printReportBtn"
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
            .print-hidden { display: none !important; }
            body { background-color: #fff !important; }
            .container { box-shadow: none !important; }
            thead[style] {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    </style>

    <script>
        document.getElementById('printReportBtn').addEventListener('click', () => {
            const table = document.querySelector('table');
            if (!table) {
                alert('Yazdırılacak tablo bulunamadı.');
                return;
            }

            const originalHTML = document.body.innerHTML;

            let printHTML = '<html><head><title>Yazdır - Ziyaretçi Raporu</title>';

            document.querySelectorAll('style').forEach(style => {
                printHTML += style.outerHTML;
            });

            document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                printHTML += link.outerHTML;
            });

            printHTML += `
                <style>
                    body { margin: 1cm; font-family: Arial, sans-serif; }
                    h2.page-title {
                        text-align: center;
                        font-size: 24px;
                        font-weight: bold;
                        color: #003366;
                        margin-bottom: 20px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        table-layout: fixed;
                        word-wrap: break-word;
                    }
                    th, td {
                        padding: 5px;
                        border: 1px solid #ccc;
                        font-size: 10px;
                        vertical-align: top;
                    }
                    thead th {
                        background-color: #003366;
                        color: #ffffff;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    @page {
                        margin: 1cm;
                    }
                </style>
            </head><body>`;

            const today = new Date();
            const dateStr = today.toLocaleDateString('tr-TR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            printHTML += `<div style="text-align:right; font-size:10px; margin-bottom:5px;">${dateStr}</div>`;
            printHTML += `<h2 class="page-title">{{ $reportTitle }} Ziyaretçi Raporu</h2>`;
            printHTML += `<div style="margin-top:20px;">${table.outerHTML}</div>`;
            printHTML += '</body></html>';

            document.body.innerHTML = printHTML;
            window.print();

            setTimeout(() => {
                document.body.innerHTML = originalHTML;
                window.location.reload();
            }, 500);
        });
    </script>
</x-app-layout>
