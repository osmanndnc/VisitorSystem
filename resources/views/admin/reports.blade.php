@php $pdfMode = $pdfMode ?? false; @endphp
<x-app-layout>
    <div class="container py-5" style="
        background-color: #ffffff;
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        width: 90%;
        max-width: 1500px;
        margin: 2rem auto;
        padding: 2.5rem;
    ">
        @php
            // mask[] => ['name','tc_no',...]  veya  mask[name]=on => ['name'=>'on', ...]
            $maskInput  = (array) request()->input('mask', []);

            // Dizi değerlerini "alan adları"na normalle
            $maskFields = collect($maskInput)->map(function ($v, $k) {
                return is_string($k) ? $k : $v; // anahtar string ise onu al, değilse değeri al
            })->filter()->values()->all();

            // Başlıkta gösterilecek liste
            $maskedList = collect($maskFields)->map(function ($f) {
                return match ($f) {
                    'name'            => 'Ad Soyad',
                    'tc_no'           => 'T.C. No',
                    'phone'           => 'Telefon',
                    'plate'           => 'Plaka',
                    'department'      => 'Ziyaret Edilen Birim',
                    'person_to_visit' => 'Ziyaret Edilen',
                    default           => $f,
                };
            })->implode(', ');

            // Satır içi hızlı kontrol için set
            $maskSet = array_flip($maskFields);
        @endphp

        <h2 class="mb-4 text-center" style="
            font-weight: bold;
            font-size: 2.8rem;
            color: #003366;
            margin-bottom: 1rem;
        ">
            {{ $reportTitle ? $reportTitle . ' ' : '' }} Ziyaretçi Raporu
            @if ($reportRange)
                <span style="display: block; font-size: 1.2rem; font-weight: normal; margin-top: 5px; color: #555;">({{ $reportRange }})</span>
            @endif
        </h2>

        @if(!empty($maskFields))
            <div class="text-center" style="color:#6b7280; margin-top:-10px; margin-bottom: 20px;">
                Maskelenen alanlar: <strong>{{ $maskedList }}</strong>
            </div>
        @endif

        @if (empty($data) || (is_object($data) && method_exists($data,'isEmpty') && $data->isEmpty()))
            <div class="alert alert-warning text-center" role="alert">
                Gösterilecek veri bulunamadı.
            </div>
        @else
            <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                <div class="table-responsive" style="max-width: 90%;">
                    <table lang="tr" class="table table-striped table-hover table-bordered shadow-sm report-tight">
                        <thead style="background-color: #003366; color: #ffffff;">
                            <tr>
                                <th class="text-center" style="min-width: 80px;">Kayıt No</th>
                                @foreach ($fieldsForBlade as $field)
                                    <th class="text-center" style="min-width: 150px;">
                                        @switch($field)
                                            @case('entry_time') Giriş Tarihi @break
                                            @case('name') Ad-Soyad @break
                                            @case('tc_no') T.C. No @break
                                            @case('phone') Telefon @break
                                            @case('plate') Plaka @break
                                            @case('purpose') Ziyaret Sebebi @break
                                            @case('department') Ziyaret Edilen Birim @break
                                            @case('person_to_visit') Ziyaret Edilen Kişi @break
                                            @case('approved_by') Ekleyen @break
                                            @default {{ ucfirst(str_replace('_', ' ', $field)) }}
                                        @endswitch
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    @foreach ($fieldsForBlade as $field)
                                        @php
                                            $val = $row[$field] ?? '-';
                                            $shouldMask = isset($maskMap[$field]); // URL'de mask[field] varsa

                                            if ($shouldMask && $val !== '-') {
                                                switch ($field) {
                                                    case 'name':
                                                    case 'person_to_visit':
                                                        $val = \App\Helpers\MaskHelper::maskName((string)$val);
                                                        break;
                                                    case 'tc_no':
                                                        $val = \App\Helpers\MaskHelper::maskTc((string)$val);
                                                        break;
                                                    case 'phone':
                                                        $val = \App\Helpers\MaskHelper::maskPhone((string)$val);
                                                        break;
                                                    case 'plate':
                                                        $val = method_exists(\App\Helpers\MaskHelper::class, 'maskPlate')
                                                            ? \App\Helpers\MaskHelper::maskPlate((string)$val)
                                                            : (preg_match('/^\d{2}\s+[A-ZÇĞİÖŞÜ]{1,3}\s+\d{2,4}$/u', strtoupper(trim((string)$val)))
                                                                ? preg_replace('/^(\d{2})\s+[A-ZÇĞİÖŞÜ]{1,3}\s+\d{2,4}$/u', '$1 ** ****', strtoupper(trim((string)$val)))
                                                                : $val);
                                                        break;
                                                    case 'department':
                                                        // Departman adını maskele (ilk 2 harf + ***)
                                                        if (strlen($val) > 2) {
                                                            $val = substr($val, 0, 2) . '***';
                                                        } else {
                                                            $val = $val . '***';
                                                        }
                                                        break;
                                                    default:
                                                        // entry_time, purpose, approved_by vb. maskelenmiyor
                                                        break;
                                                }
                                            }
                                        @endphp
                                        <td class="text-center">{{ $val }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 d-flex flex-wrap justify-content-center print-hidden"
                    style="gap: 1.5rem 2rem; padding: 1rem 0;">
                    {{-- Query tüm parametreleri (mask dahil) taşıyor --}}
                    <a href="{{ route('report.export', request()->query()) }}"
                       class="custom-btn btn-excel">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </a>
                    <a href="{{ route('report.maskedPdf', request()->query()) }}"
                       target="_blank"
                       class="custom-btn btn-pdf">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>

                    <button id="printReportBtn" class="custom-btn btn-print">
                        <i class="bi bi-printer"></i> Yazdır
                    </button>

                    <button id="showChartBtn" class="custom-btn btn-chart">
                        <i class="bi bi-graph-up-arrow"></i> Grafik
                    </button>
                </div>

                <div id="reportChartContainer" class="mt-5" style="width: 90%; display: none; position: relative;"
                     data-chart='@json($chartData ?? [])'
                     data-filter="{{ $dateFilter ?? '' }}">
                    <canvas id="reportChart"></canvas>
                    <button id="downloadPdfBtn" class="custom-btn btn-pdf"
                        style="display: none; position: absolute; bottom: -20px; right: 5px; z-index: 10; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                        <i class="bi bi-file-earmark-pdf"></i> Grafik PDF İndir
                    </button>
                </div>

            </div>
        @endif
    </div>

<style>
    html {
        zoom: 80%;
        height: 100%;
        scroll-behavior: smooth;
    }
    body {
        margin: 0;
        padding: 0;
        min-height: 100%;
        background: linear-gradient(to bottom right, #f8fafc, #e2e8f0);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }

    /* Container */
    .container {
        background: #ffffff;
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        padding: 3rem;
        transition: all .3s ease;
    }
    .container:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }

    /* Başlık */
    h2 {
        font-weight: 800;
        font-size: 2.5rem;
        color: #003366;
        text-align: center;
        margin-bottom: 1rem;
        letter-spacing: -0.5px;
    }
    h2 span {
        font-size: 1.1rem;
        font-weight: 400;
        color: #6b7280;
    }

    /* Tablo */
    .table {
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        border-collapse: collapse;
        width: 100%;
    }

    /* Başlık satırı */
    .table thead th {
        background: linear-gradient(90deg, #003366, #004080);
        color: #fff;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.4px;
        padding: 8px 10px;
        white-space: nowrap;
    }

    /* Hücreler */
    .table td,
    .table th {
        vertical-align: middle;
        text-align: center;
        padding: 7px 10px;
        font-size: 0.85rem;
        line-height: 1.3;
        border: 1px solid #e5e7eb;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    /* Satır hover */
    .table tbody tr:hover {
        background: #f8fafc;
        transition: background 0.15s ease;
    }

    /* Butonlar */
    .custom-btn {
        height: 44px;
        min-width: 150px;
        padding: 0 1.5rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 12px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin: 0.5rem;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: all 0.3s ease;
        color: white;
    }
    .custom-btn i { font-size: 1.2rem; }

    /* Renkler */
    .btn-excel { background: #28a745; }
    .btn-excel:hover { background: #218838; transform: scale(1.05); }

    .btn-pdf { background: #dc3545; }
    .btn-pdf:hover { background: #b02a37; transform: scale(1.05); }

    .btn-print { background: #003366; }
    .btn-print:hover { background: #002244; transform: scale(1.05); }

    .btn-chart { background: #ffc107; color: #343a40; }
    .btn-chart:hover { background: #e0a800; color: #212529; transform: scale(1.05); }

    /* Grafik container */
    #reportChartContainer {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        margin-top: 2rem;
    }

    /* Mobil uyum */
    @media (max-width: 768px) {
        h2 { font-size: 1.8rem; }
        .table-responsive { max-width: 100%; }
        .custom-btn { min-width: 120px; font-size: 0.9rem; }
        .container { padding: 1.5rem; }
    }
</style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
    // --- YAZDIR (A4 Landscape, kompakt tablo, başlık tekrarı) ---
    document.getElementById('printReportBtn')?.addEventListener('click', () => {
      const table = document.querySelector('table');
      if (!table) return alert('Yazdırılacak tablo bulunamadı.');

      const h2 = document.querySelector('h2');
      const titleOnly = (h2?.childNodes[0]?.textContent || 'Ziyaretçi Raporu').trim();
      const range = document.querySelector('h2 span')?.innerText.trim() || '';
      const finalTitle = range ? `${titleOnly} ${range}` : titleOnly;

      const today = new Date();
      const dateStr = today.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });

      const w = window.open('', '_blank');
      w.document.open();
      w.document.write(`
        <html lang="tr">
        <head>
          <meta charset="UTF-8" />
          <title>Yazdır - ${finalTitle}</title>
          <style>
            @page { size: A4 landscape; margin: 8mm; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            html, body { height: auto; }
            body { margin:0; font-family: 'Segoe UI', Arial, Helvetica, sans-serif; }

            .head {
              display:flex; align-items:center; justify-content:space-between;
              margin: 0 0 6px 0; padding: 0 2mm;
            }
            .date { font-size: 9px; color:#555; }
            .title { font-size: 16px; font-weight: 800; color:#003366; margin:0 auto; text-align:center; }

            table { width:100%; border-collapse: collapse; table-layout: fixed; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tr { page-break-inside: avoid; }

            thead th {
              background: #003366; color:#fff;
              font-size: 9px; font-weight: 700; letter-spacing:.2px;
              padding: 4px 5px; white-space: nowrap; border:1px solid #cfd6e0;
              text-transform: uppercase;
            }
            tbody td {
              font-size: 8.5px; line-height: 1.25;
              padding: 3px 4px; white-space: nowrap;
              overflow: hidden; text-overflow: ellipsis; border:1px solid #e1e6ef;
            }
            /* Sütun ipuçları (isteğe bağlı) */
            td:nth-child(2){ width: 120px; }  /* giriş tarihi */
            td:nth-child(3){ width: 140px; }  /* ad-soyad */
            td:nth-child(7){ width: 95px;  }  /* ziyaret sebebi */
            td:nth-child(8){ width: 140px; }  /* ziyaret edilen */
            td:nth-child(9){ width: 110px; }  /* ekleyen */
          </style>
        </head>
        <body>
          <div class="head">
            <div class="date">${dateStr}</div>
            <h2 class="title">${finalTitle}</h2>
            <div style="width:90px"></div>
          </div>
          ${table.outerHTML}
        </body>
        </html>
      `);
      w.document.close();
      w.focus();
      w.print();
      setTimeout(() => { try { w.close(); } catch(e){} }, 300);
    });

    // --- Grafik (değiştirilmedi) ---
    document.getElementById('showChartBtn')?.addEventListener('click', () => {
        const chartContainer = document.getElementById('reportChartContainer');
        const downloadPdfBtn = document.getElementById('downloadPdfBtn');
        if (chartContainer.style.display === 'none') {
            chartContainer.style.display = 'block';
            downloadPdfBtn.style.display = 'flex';
            drawChart();
            setTimeout(() => {
                chartContainer.scrollIntoView({ behavior: 'smooth', block: 'end' });
            }, 500);
        } else {
            chartContainer.style.display = 'none';
            downloadPdfBtn.style.display = 'none';
        }
    });

    function drawChart() {
        const ctx = document.getElementById('reportChart').getContext('2d');
        const chartContainer = document.getElementById('reportChartContainer');
        let chartData = JSON.parse(chartContainer.dataset.chart || '[]');
        let dateFilter = chartContainer.dataset.filter || '';

        let labels = [], counts = [], chartTitle = '', xAxisLabel = '';

        if (dateFilter === 'daily') {
            chartTitle = 'Günlük Ziyaretçi Sayıları (Saatlere Göre)';
            xAxisLabel = 'Saat';
            labels = chartData.map(item => `${item.label}:00`);
            counts = chartData.map(item => item.count);
        } else if (dateFilter === 'monthly') {
            chartTitle = 'Aylık Ziyaretçi Sayıları (Günlere Göre)';
            xAxisLabel = 'Gün';
            labels = chartData.map(item => item.label);
            counts = chartData.map(item => item.count);
        } else if (dateFilter === 'yearly') {
            chartTitle = 'Yıllık Ziyaretçi Sayıları (Aylara Göre)';
            xAxisLabel = 'Ay';
            labels = chartData.map(item => {
                const monthNames = ["Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık"];
                return monthNames[item.label - 1];
            });
            counts = chartData.map(item => item.count);
        } else {
            chartTitle = 'Tüm Zamanların Ziyaretçi Sayıları (Yıllara Göre)';
            xAxisLabel = 'Yıl';
            labels = chartData.map(item => item.label);
            counts = chartData.map(item => item.count);
        }

        if (window.myReportChart) window.myReportChart.destroy();

        window.myReportChart = new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets: [{ label: 'Ziyaretçi Sayısı', data: counts, backgroundColor: 'rgba(0, 51, 102, 0.7)', borderColor: 'rgba(0, 51, 102, 1)', borderWidth: 1 }] },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: chartTitle, font: { size: 18, weight: 'bold' }, color: '#003366' },
                    tooltip: { mode: 'index', intersect: false, callbacks: { label: (c)=> `${c.dataset.label}: ${c.raw}` } }
                },
                scales: {
                    x: { title: { display: true, text: xAxisLabel, font: { size: 14 } }, ticks: { autoSkip: false, maxRotation: 45, minRotation: 0 } },
                    y: { title: { display: true, text: 'Ziyaretçi Sayısı', font: { size: 14 } }, beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    document.getElementById('downloadPdfBtn')?.addEventListener('click', () => {
        const chartCanvas = document.getElementById('reportChart');
        if (!chartCanvas || !window.myReportChart) return alert('Grafik henüz oluşturulmadı veya bulunamadı!');

        html2canvas(chartCanvas, { scale: 2 }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');

            const pdfWidth = doc.internal.pageSize.getWidth();
            const pdfHeight = doc.internal.pageSize.getHeight();
            const margin = 15;
            const imgWidth = pdfWidth - (2 * margin);
            const imgHeight = canvas.height * imgWidth / canvas.width;

            let currentY = margin;

            const today = new Date();
            const dateStr = today.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            doc.setFontSize(10); doc.setTextColor(100);
            doc.text(`Rapor Tarihi: ${dateStr}`, pdfWidth - margin, currentY, { align: 'right' });
            currentY += 15;

            if (imgHeight > pdfHeight - currentY - margin) { doc.addPage(); currentY = margin; }
            doc.addImage(imgData, 'PNG', margin, currentY, imgWidth, imgHeight);

            let reportTypeForFileName = "{{ $dateFilter ?? 'all' }}";
            let fileNamePrefix = '';
            switch (reportTypeForFileName) {
                case 'daily': fileNamePrefix = 'Gunluk-Ziyaretci-Grafigi'; break;
                case 'monthly': fileNamePrefix = 'Aylik-Ziyaretci-Grafigi'; break;
                case 'yearly': fileNamePrefix = 'Yillik-Ziyaretci-Grafigi'; break;
                default: fileNamePrefix = 'Tum-Ziyaretci-Grafigi'; break;
            }
            doc.save(`${fileNamePrefix}-${today.toISOString().slice(0,10)}.pdf`);
        }).catch(() => alert('Grafik PDF olarak indirilirken bir hata oluştu.'));
    });
    </script>
</x-app-layout>