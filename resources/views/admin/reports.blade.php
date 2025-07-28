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
                            class="btn shadow-sm d-flex align-items-center me-3"
                            style="background-color: #003366; color: #ffffff; border-radius: 8px; padding: 0.5rem 1rem; font-size: 1rem; font-weight: bold; height: 40px;">
                        <i class="bi bi-printer me-2"></i> Yazdır
                    </button>

                    <button id="showChartBtn"
                            class="btn shadow-sm d-flex align-items-center me-3"
                            style="background-color: #ffc107; color: #343a40; border-radius: 8px; padding: 0.5rem 1rem; font-size: 1rem; font-weight: bold; height: 40px;">
                        <i class="bi bi-graph-up-arrow me-2"></i> Grafik Göster
                    </button>
                </div>

                <div id="reportChartContainer" class="mt-5" style="width: 90%; display: none; position: relative;">
                    <canvas id="reportChart"></canvas>

                    <button id="downloadPdfBtn"
                            style="
                                background-color: #dc3545;
                                color: white;
                                border-radius: 8px;
                                padding: 0.5rem 1rem;
                                font-size: 1rem;
                                font-weight: bold;
                                height: 40px;
                                display: none;
                                position: absolute;
                                bottom: -20px;
                                right: 5px;
                                z-index: 10;
                                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                            ">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Grafik PDF İndir
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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

        // GRAFİK
        document.getElementById('showChartBtn').addEventListener('click', () => {
            const chartContainer = document.getElementById('reportChartContainer');
            const downloadPdfBtn = document.getElementById('downloadPdfBtn');
            if (chartContainer.style.display === 'none') {
                chartContainer.style.display = 'block';
                downloadPdfBtn.style.display = 'flex';
                drawChart();
            } else {
                chartContainer.style.display = 'none';
                downloadPdfBtn.style.display = 'none';
            }
        });

        function drawChart() {
            const ctx = document.getElementById('reportChart').getContext('2d');
            let chartData = @json($chartData ?? []);
            let reportType = "{{ $dateFilter ?? 'all' }}";

            let labels = [];
            let counts = [];
            let chartTitle = '';
            let xAxisLabel = '';

            if (reportType === 'daily') {
                chartTitle = 'Günlük Ziyaretçi Sayıları (Saatlere Göre)';
                xAxisLabel = 'Saat';
                labels = chartData.map(item => `${item.label}:00`);
                counts = chartData.map(item => item.count);
            } else if (reportType === 'monthly') {
                chartTitle = 'Aylık Ziyaretçi Sayıları (Günlere Göre)';
                xAxisLabel = 'Gün';
                labels = chartData.map(item => item.label);
                counts = chartData.map(item => item.count);
            } else if (reportType === 'yearly') {
                chartTitle = 'Yıllık Ziyaretçi Sayıları (Aylara Göre)';
                xAxisLabel = 'Ay';
                labels = chartData.map(item => {
                    const monthNames = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
                    return monthNames[item.label - 1];
                });
                counts = chartData.map(item => item.count);
            } else {
                chartTitle = 'Tüm Zamanların Ziyaretçi Sayıları (Yıllara Göre)';
                xAxisLabel = 'Yıl';
                labels = chartData.map(item => item.label);
                counts = chartData.map(item => item.count);
            }

            if (window.myReportChart) {
                window.myReportChart.destroy();
            }

            window.myReportChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ziyaretçi Sayısı',
                        data: counts,
                        backgroundColor: 'rgba(0, 51, 102, 0.7)',
                        borderColor: 'rgba(0, 51, 102, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: chartTitle,
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            color: '#003366'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: xAxisLabel,
                                font: {
                                    size: 14
                                }
                            },
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Ziyaretçi Sayısı',
                                font: {
                                    size: 14
                                }
                            },
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        //GRAFİK İÇİN PDF İNDİRME
        document.getElementById('downloadPdfBtn').addEventListener('click', () => {
            const chartCanvas = document.getElementById('reportChart');

            if (!chartCanvas || !window.myReportChart) {
                alert('Grafik henüz oluşturulmadı veya bulunamadı!');
                return;
            }

            html2canvas(chartCanvas, {
                scale: 2
            }).then(canvas => {
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
                const dateStr = today.toLocaleDateString('tr-TR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                doc.setFontSize(10);
                doc.setTextColor(100);
                doc.text(`Rapor Tarihi: ${dateStr}`, pdfWidth - margin, currentY, { align: 'right' });
                currentY += 15;

                if (imgHeight > pdfHeight - currentY - margin) {
                    doc.addPage();
                    currentY = margin;
                }
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
            }).catch(error => {
                console.error('Grafik PDF olarak indirilirken hata oluştu:', error);
                alert('Grafik PDF olarak indirilirken bir hata oluştu.');
            });
        });
    </script>
</x-app-layout>
