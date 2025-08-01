<x-app-layout>
    <style>
        html { zoom: 80%; }
        body { background: #f1f5f9; }
        .center-box {
            position: relative;
            width: 90%;
            max-width: 1500px;
            margin: 2rem auto;
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 2.5rem;
        }
        .page-title {
            text-align: center;
            font-size: 2.8rem;
            font-weight: 800;
            color: #003366; 
            margin-bottom: 0.5rem;
        }
        .active-filter-info {
            text-align: center;
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 2rem;
        }
        .modern-btn {
            background: linear-gradient(135deg, #003366 0%, #00509e 100%);
            color: white;
            padding: 0.5rem 2rem;
            border-radius: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 6px 15px rgba(0, 80, 158, 0.5);
            border: none;
            cursor: pointer;
            position: relative;
            transition: all 0.35s ease;
        }
        .modern-btn:hover {
            background: linear-gradient(135deg, #00509e 0%, #003366 100%);
            box-shadow: 0 8px 20px rgba(0, 80, 158, 0.7);
            transform: scale(1.07);
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 3rem;
            left: 0;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            width: 250px;
            z-index: 50;
            padding: 1rem;
        }
        .dropdown-menu.active { display: block; }
        .dropdown-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .dropdown-menu li {
            padding: 0.4rem 0.5rem;
            cursor: pointer;
            transition: background 0.2s ease;
            user-select: none;
        }
        .dropdown-menu li:hover {
            background: #f3f4f6;
        }
        .filter-option.selected {
            background-color: #d1e7ff; 
            color: #003366;
            font-weight: 700;
        }
        .filter-option input, .date-filter-inputs input {
            width: 100%;
            margin-top: 0.3rem;
            padding: 0.3rem 0.4rem;
            font-size: 0.9rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.3rem;
            display: none;
            box-sizing: border-box;
        }
        .filter-option.selected input {
            display: block;
        }
        .date-filter-inputs {
            display: none;
            padding: 0.5rem;
            border-top: 1px solid #e5e7eb;
            margin-top: 0.5rem;
        }
        .date-filter-inputs input {
            display: block;
            margin-bottom: 0.5rem;
        }
        .date-filter-inputs label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        table { width: 100%; border-collapse: collapse; }
        table th, table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        table th {
            background: #f9fafb;
            font-weight: 600;
        }
        table tr:hover {
            background: #f3f4f6;
        }
        .svg-button {
            background-color: #ffffff;
            border: none;
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
            border-radius: 0.6rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            margin-left: 0.75rem;
            vertical-align: middle;
        }
        .svg-button:hover { background-color: #f0f0f0; }
        .svg-path {
            transition: stroke-width 0.3s;
            stroke-dasharray: 100;
            stroke-dashoffset: 0;
            stroke: #003366;
        }
        .svg-button:hover .svg-path {
            stroke-width: 2;
            animation: draw 500ms ease-in forwards;
        }
        @keyframes draw {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }
        .report-generate-button-container,
        .export-buttons-bottom { 
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 1.5rem; 
            margin-bottom: 1rem;
        }
        .report-generate-button,
        .export-button-bottom { 
            background: linear-gradient(135deg, #003366 0%, #00509e 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 6px 15px rgba(0, 80, 158, 0.5);
            border: none;
            cursor: pointer;
            transition: all 0.35s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .report-generate-button:hover,
        .export-button-bottom:hover {
            background: linear-gradient(135deg, #00509e 0%, #003366 100%);
            box-shadow: 0 8px 20px rgba(0, 80, 158, 0.7);
            transform: scale(1.07);
        }
        .export-button-bottom.excel { background: linear-gradient(135deg, #28a745 0%, #218838 100%); }
        .export-button-bottom.excel:hover { background: linear-gradient(135deg, #218838 0%, #28a745 100%); }
        .export-button-bottom.pdf { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
        .export-button-bottom.pdf:hover { background: linear-gradient(135deg, #c82333 0%, #dc3545 100%); }
        .export-button-bottom svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .export-button-bottom.excel svg,
        .export-button-bottom.pdf svg {
            fill: currentColor;
            stroke: none;
        }
    </style>

    <div class="py-6">
        <div class="center-box">
            <h2 class="page-title">Ziyaretçi Listesi</h2>
            <div class="active-filter-info">
                <span id="activeFilterText">Günlük Kayıtlar</span>
            </div>

            <div class="flex justify-between items-center mb-6 relative" style="display:flex; justify-content: space-between; align-items: center;">
                <div class="relative">
                    <button id="filterBtn" type="button" class="modern-btn">Filtreleme Yap</button>
                    <div id="filterMenu" class="dropdown-menu">
                        <ul id="filterOptions">
                            <li class="filter-option" data-field="id">ID
                                <input type="text" id="id_value" placeholder="ID ara" value="{{ request('id_value') }}">
                            </li>
                            <li class="filter-option" data-field="entry_time">Giriş Tarihi
                                <input type="text" id="entry_time_value" placeholder="Giriş Tarihi ara" value="{{ request('entry_time_value') }}">
                            </li>
                            <li class="filter-option" data-field="name">Ad Soyad
                                <input type="text" id="name_value" placeholder="Ad Soyad ara" value="{{ request('name_value') }}">
                            </li>
                            <li class="filter-option" data-field="tc_no">TC Kimlik No
                                <input type="text" id="tc_no_value" placeholder="TC Kimlik No ara" value="{{ request('tc_no_value') }}">
                            </li>
                            <li class="filter-option" data-field="phone">Telefon
                                <input type="text" id="phone_value" placeholder="Telefon ara" value="{{ request('phone_value') }}">
                            </li>
                            <li class="filter-option" data-field="plate">Plaka
                                <input type="text" id="plate_value" placeholder="Plaka ara" value="{{ request('plate_value') }}">
                            </li>
                            <li class="filter-option" data-field="purpose">Ziyaret Sebebi
                                <input type="text" id="purpose_value" placeholder="Ziyaret Sebebi ara" value="{{ request('purpose_value') }}">
                            </li>
                            <li class="filter-option" data-field="person_to_visit">Ziyaret Edilen Kişi
                                <input type="text" id="person_to_visit_value" placeholder="Ziyaret Edilen Kişi ara" value="{{ request('person_to_visit_value') }}">
                            </li>
                            <li class="filter-option" data-field="approved_by">Ekleyen
                                <input type="text" id="approved_by_value" placeholder="Ekleyen ara" value="{{ request('approved_by_value') }}">
                            </li>
                        </ul>
                        <div class="p-2">
                            <button id="applyFilters" class="w-full rounded-lg font-semibold modern-btn hover:brightness-110 transition" style="padding: 0.4rem 1.5rem; font-size: 1rem;">
                                Filtreyi Uygula
                            </button>
                        </div>
                    </div>
                </div>

                <div class="relative" style="display: flex; align-items: center; gap: 0.75rem;">
                    <button id="reportBtn" type="button" class="modern-btn">Kayıt Görüntüle</button>
                    <button id="refreshBtn" class="svg-button" aria-label="Yenile" title="Yenile">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                            stroke="#003366" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path class="svg-path" d="M21 12a9 9 0 1 1-2.64-6.36" />
                            <polyline class="svg-path" points="21 3 21 9 15 9" />
                        </svg>
                    </button>
                    <div id="reportMenu" class="dropdown-menu" style="left:auto; right:0;">
                        <ul>
                            <li data-type="all">Tüm Kayıtlar</li>
                            <li data-type="daily">Günlük</li>
                            <li data-type="monthly">Aylık</li>
                            <li data-type="yearly">Yıllık</li>
                            <li id="dateRangeOption">Tarih Aralığı</li>
                        </ul>
                        <div id="dateRangeInputs" class="date-filter-inputs">
                            <label for="start_date">Başlangıç:</label>
                            <input type="date" id="start_date" value="{{ request('start_date') }}">
                            <label for="end_date">Bitiş:</label>
                            <input type="date" id="end_date" value="{{ request('end_date') }}">
                            <button id="applyDateRange" class="w-full rounded-lg font-semibold modern-btn hover:brightness-110 transition mt-2" style="padding: 0.4rem 1.5rem; font-size: 1rem;">
                                Uygula
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        @foreach($fields as $field)
                            <th>
                                @switch($field)
                                    @case('id') ID @break
                                    @case('entry_time') Giriş Tarihi @break
                                    @case('name') Ad Soyad @break
                                    @case('tc_no') TC Kimlik No @break
                                    @case('phone') Telefon @break
                                    @case('plate') Plaka @break
                                    @case('purpose') Ziyaret Sebebi @break
                                    @case('person_to_visit') Ziyaret Edilen Kişi @break
                                    @case('approved_by') Ekleyen @break
                                    @default {{ $field }}
                                @endswitch
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($visits as $visit)
                        <tr>
                            @foreach($fields as $field)
                                <td>
                                    @switch($field)
                                        @case('id') {{ $visit->id ?? '-' }} @break
                                        @case('entry_time') {{ $visit->entry_time ?? '-' }} @break
                                        @case('name') {{ $visit->visitor->name ?? '-' }} @break
                                        @case('tc_no') {{ $visit->visitor->tc_no ?? '-' }} @break
                                        @case('phone') {{ $visit->visitor->phone ?? '-' }} @break
                                        @case('plate') {{ $visit->visitor->plate ?? '-' }} @break
                                        @case('purpose') {{ $visit->purpose ?? '-' }} @break
                                        @case('person_to_visit') {{ $visit->person_to_visit ?? '-' }} @break
                                        @case('approved_by') {{ $visit->approver->ad_soyad ?? $visit->approved_by ?? '-' }} @break
                                        @default {{ $visit->$field ?? '-' }}
                                    @endswitch
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="export-buttons-bottom">
                <button id="exportUnmaskedExcelBtn" type="button" class="export-button-bottom excel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-excel-fill" viewBox="0 0 16 16">
                        <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM5.884 6.68L8 9.219l2.116-2.54a.145.145 0 0 1 .328 0l.542.65-.66 1.004-.961 1.458-.178.2-.66.994-1.116 1.688a.145.145 0 0 1-.328 0L4 9.219l-2.116 2.54a.145.145 0 0 1-.328 0l-.542-.65.66-1.004.961-1.458.178-.2.66-.994 1.116-1.688a.145.145 0 0 1 .328 0z"/>
                    </svg>
                    Excel
                </button>
                <button id="printUnmaskedBtn" type="button" class="export-button-bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
                        <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zm0 12h6a1 1 0 0 1 1 1v1H4v-1a1 1 0 0 1 1-1zM3 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H3V3zm3 7a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                    </svg>
                    Yazdır
                </button>
                <a id="exportUnmaskedPdfBtn" href="#" class="export-button-bottom pdf">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-pdf-fill" viewBox="0 0 16 16">
                        <path d="M5.52 0.359L0 12.311V16h16V12.311L10.48 0.359zM8 8.163v1.895l-0.347 0.174L7.52 11h-0.082l-0.134-0.413l-0.347-0.174V8.163h-1.6V7.073h1.6V5.178h1.127V7.073H9.721v1.09h-1.6zM11 6.5h2v6h-2V6.5z"/>
                    </svg>
                    PDF
                </a>
            </div>
            <div class="report-generate-button-container">
                <button id="generateReportBtn" type="button" class="report-generate-button">Güvenli Rapor Oluştur</button>
            </div>
        </div>
    </div>

    <script>
        const filterBtn = document.getElementById('filterBtn');
        const filterMenu = document.getElementById('filterMenu');
        const reportBtn = document.getElementById('reportBtn');
        const reportMenu = document.getElementById('reportMenu');
        const refreshBtn = document.getElementById('refreshBtn');
        const generateReportBtn = document.getElementById('generateReportBtn');
        const exportUnmaskedExcelBtn = document.getElementById('exportUnmaskedExcelBtn');
        const printUnmaskedBtn = document.getElementById('printUnmaskedBtn');
        const exportUnmaskedPdfBtn = document.getElementById('exportUnmaskedPdfBtn');
        const dateRangeOption = document.getElementById('dateRangeOption');
        const dateRangeInputs = document.getElementById('dateRangeInputs');
        const applyDateRangeBtn = document.getElementById('applyDateRange');

        filterBtn.addEventListener('click', () => filterMenu.classList.toggle('active'));
        reportBtn.addEventListener('click', () => reportMenu.classList.toggle('active'));
        
        dateRangeOption.addEventListener('click', () => {
            document.querySelectorAll('#reportMenu li').forEach(li => li.style.display = 'none');
            dateRangeInputs.style.display = 'block';
            dateRangeOption.style.display = 'block';
        });

        applyDateRangeBtn.addEventListener('click', () => {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            const params = new URLSearchParams(window.location.search);
            
            params.delete('date_filter');
            params.set('start_date', startDate);
            if (endDate) {
                params.set('end_date', endDate);
            } else {
                params.delete('end_date');
            }

            document.querySelectorAll('.filter-option input').forEach(input => {
                const fieldName = input.id.replace('_value', '');
                if (input.value.trim() !== '') {
                    params.set(fieldName + '_value', input.value.trim());
                } else {
                    params.delete(fieldName + '_value');
                }
            });

            window.location.href = window.location.pathname + '?' + params.toString();
        });

        document.querySelectorAll('#reportMenu li').forEach(item => {
            item.addEventListener('click', () => {
                if (item.id === 'dateRangeOption') return;

                const type = item.dataset.type;
                const urlParams = new URLSearchParams(window.location.search);
                
                urlParams.delete('start_date');
                urlParams.delete('end_date');
                urlParams.set('date_filter', type);

                document.querySelectorAll('.filter-option.selected input').forEach(input => {
                    const fieldName = input.id.replace('_value', '');
                    if (input.value.trim() !== '') {
                        urlParams.set(fieldName + '_value', input.value.trim());
                    } else {
                        urlParams.delete(fieldName + '_value');
                    }
                });
                
                const selectedFieldsFromFilter = [...document.querySelectorAll('.filter-option.selected')].map(opt => opt.getAttribute('data-field'));
                if (selectedFieldsFromFilter.length > 0) {
                    urlParams.set('filter', selectedFieldsFromFilter.join(','));
                } else {
                    urlParams.delete('filter');
                }

                window.location.href = window.location.pathname + '?' + urlParams.toString();
            });
        });

        refreshBtn.addEventListener('click', () => {
            window.location.href = window.location.pathname + '?date_filter=daily';
        });

        document.querySelectorAll('.filter-option').forEach(option => {
            option.addEventListener('click', (e) => {
                if (e.target.tagName.toLowerCase() === 'input') return;
                option.classList.toggle('selected');
                const input = option.querySelector('input');
                if (input) {
                    input.style.display = option.classList.contains('selected') ? 'block' : 'none';
                    if (!option.classList.contains('selected')) {
                        input.value = '';
                    }
                }
            });
        });

        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const dateFilterParam = urlParams.get('date_filter');
            const startDateParam = urlParams.get('start_date');
            const endDateParam = urlParams.get('end_date');
            const activeFilterText = document.getElementById('activeFilterText');
            
            const filterParam = urlParams.get('filter');
            if (filterParam) {
                const filters = filterParam.split(',');
                filters.forEach(f => {
                    const opt = document.querySelector(`.filter-option[data-field="${f}"]`);
                    if (opt) {
                        opt.classList.add('selected');
                        const input = opt.querySelector('input');
                        if (input) {
                            input.style.display = 'block';
                        }
                    }
                });
            }
            
            if (startDateParam) {
                const start = new Date(startDateParam).toLocaleDateString('tr-TR');
                const end = endDateParam ? new Date(endDateParam).toLocaleDateString('tr-TR') : 'Bugün';
                activeFilterText.textContent = `(${start} - ${end} Aralığı)`;
            } else if (dateFilterParam) {
                const reportTypeElement = document.querySelector(`#reportMenu li[data-type="${dateFilterParam}"]`);
                if (reportTypeElement) {
                    activeFilterText.textContent = `(${reportTypeElement.textContent} Kayıtlar)`;
                }
            } else {
                activeFilterText.textContent = '(Günlük Kayıtlar)';
            }
        });

        document.getElementById('applyFilters').addEventListener('click', () => {
            const selectedOptions = [...document.querySelectorAll('.filter-option.selected')];
            if (selectedOptions.length === 0) {
                alert('Lütfen en az bir filtre seçiniz.');
                return;
            }

            const params = new URLSearchParams(window.location.search);

            const selectedFields = selectedOptions.map(opt => opt.getAttribute('data-field'));
            params.set('filter', selectedFields.join(','));

            selectedOptions.forEach(opt => {
                const field = opt.getAttribute('data-field');
                const input = opt.querySelector('input');
                if (input && input.value.trim() !== '') {
                    params.set(field + '_value', input.value.trim());
                } else {
                    params.delete(field + '_value');
                }
            });

            const dateFilterParam = new URLSearchParams(window.location.search).get('date_filter');
            const startDateParam = new URLSearchParams(window.location.search).get('start_date');
            const endDateParam = new URLSearchParams(window.location.search).get('end_date');

            if (startDateParam) {
                params.set('start_date', startDateParam);
                params.delete('date_filter');
            }
            if (endDateParam) {
                params.set('end_date', endDateParam);
            }
            if (dateFilterParam && !startDateParam) {
                params.set('date_filter', dateFilterParam);
            } else if (!startDateParam) {
                params.set('date_filter', 'daily');
            }

            window.location.href = window.location.pathname + '?' + params.toString();
        });

        document.addEventListener('click', e => {
            if (!filterBtn.contains(e.target) && !filterMenu.contains(e.target)) {
                filterMenu.classList.remove('active');
            }
            if (!reportBtn.contains(e.target) && !reportMenu.contains(e.target) && !dateRangeInputs.contains(e.target)) {
                reportMenu.classList.remove('active');
                dateRangeInputs.style.display = 'none';
                document.querySelectorAll('#reportMenu li').forEach(li => li.style.display = 'block');
            }
        });

        function getCommonExportParams(isReportPage = false) {
            const urlParams = new URLSearchParams(window.location.search);
            const exportParams = new URLSearchParams();

            const allFields = [
                'id', 'entry_time', 'name', 'tc_no', 'phone', 'plate',
                'purpose', 'person_to_visit', 'approved_by'
            ];
            
            const filterParam = urlParams.get('filter');
            let selectedFields = filterParam ? filterParam.split(',') : allFields;

            // Raporlama sayfası için 'id' filtresini her zaman kaldır
            if (isReportPage) {
                 selectedFields = selectedFields.filter(field => field !== 'id');
            }

            selectedFields.forEach(field => {
                exportParams.append('fields[]', field);
                const value = urlParams.get(field + '_value');
                if (value) {
                    exportParams.set(field + '_value', value);
                }
            });

            const dateFilterParam = urlParams.get('date_filter');
            const startDateParam = urlParams.get('start_date');
            const endDateParam = urlParams.get('end_date');

            if (startDateParam) {
                exportParams.set('start_date', startDateParam);
            }
            if (endDateParam) {
                exportParams.set('end_date', endDateParam);
            }
            if (dateFilterParam) {
                exportParams.set('date_filter', dateFilterParam);
            }

            exportParams.set('sort_order', 'desc');
            return exportParams;
        }

        generateReportBtn.addEventListener('click', () => {
            const reportParams = getCommonExportParams(true);
            window.location.href = `/admin/generate-report?` + reportParams.toString();
        });

        exportUnmaskedExcelBtn.addEventListener('click', () => {
            const exportParams = getCommonExportParams(false);
            exportParams.set('unmasked', 'true');
            window.location.href = `/report/export?` + exportParams.toString();
        });

        if (exportUnmaskedPdfBtn) {
            exportUnmaskedPdfBtn.addEventListener('click', () => {
                const pdfParams = getCommonExportParams(false);
                window.location.href = `/admin/export-pdf-unmasked?` + pdfParams.toString();
            });
        }

        printUnmaskedBtn.addEventListener('click', () => {
            const table = document.querySelector('table');
            if (table) {
                const originalBodyHTML = document.body.innerHTML;
                let printContentHtml = '<html><head><title>Ziyaretçi Listesi</title>';
                document.querySelectorAll('style').forEach(styleElement => {
                    printContentHtml += styleElement.outerHTML;
                });
                document.querySelectorAll('link[rel="stylesheet"]').forEach(linkTag => {
                    printContentHtml += linkTag.outerHTML;
                });

                printContentHtml += '<style>';
                printContentHtml += `body { margin: 1cm !important; font-family: sans-serif; }
                    table { width: 100% !important; table-layout: fixed !important; word-wrap: break-word !important; border-collapse: collapse !important; }
                    th, td { 
                        white-space: normal !important; 
                        padding: 4px !important; 
                        font-size: 8px !important; 
                        vertical-align: top !important; 
                        border: 1px solid #ccc !important; 
                        min-width: unset !important; 
                    }
                    .page-title {
                        text-align: center;
                        font-size: 24px; 
                        font-weight: bold;
                        color: #003366;
                        margin-bottom: 20px;
                    }
                    thead th {
                        background-color: #003366 !important;
                        color: #ffffff !important;
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                    }
                    @page {
                        margin: 1cm !important;
                        @top-left { content: ""; } @top-center { content: ""; } @top-right { content: ""; }
                        @bottom-left { content: ""; } @bottom-center { content: ""; } @bottom-right { content: ""; }
                    }`;
                printContentHtml += '</style>';
                printContentHtml += '</head><body>';
                printContentHtml += '<div id="printContainer">'; 
                
                const activeFilterText = document.getElementById('activeFilterText').textContent;
                const today = new Date();
                const formattedDate = today.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                printContentHtml += `<div style="text-align: right; font-size: 10px; margin-bottom: 10px;">${formattedDate}</div>`;
                printContentHtml += `<h2 class="page-title">Ziyaretçi Listesi ${activeFilterText}</h2>`;
                printContentHtml += '<div style="display: flex; justify-content: center; width: 100%; margin-top: 20px;">';
                printContentHtml += table.outerHTML; 
                printContentHtml += '</div>';
                printContentHtml += '</div>';        
                printContentHtml += '</body></html>';

                const iframe = document.createElement('iframe');
                iframe.style.display = 'none'; 
                document.body.appendChild(iframe);

                const iframeDoc = iframe.contentWindow.document;
                iframeDoc.open();
                iframeDoc.write(printContentHtml);
                iframeDoc.close();
                iframe.onload = function() {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch (error) {
                        console.error('Yazdırma hatası:', error);
                        alert('Yazdırma işlemi sırasında bir hata oluştu: ' + error.message);
                    } finally {
                        setTimeout(() => {
                            if (document.body.contains(iframe)) {
                                document.body.removeChild(iframe);
                            }
                        }, 1000);
                    }
                };
                
                setTimeout(() => {
                    if (iframe.contentWindow && iframe.contentWindow.document.readyState !== 'complete') {
                        console.warn("Iframe yüklenmedi veya geç yüklendi, manuel print deneniyor.");
                        if (iframe.contentWindow) {
                           iframe.contentWindow.focus();
                           iframe.contentWindow.print();
                       }
                        setTimeout(() => {
                            if (document.body.contains(iframe)) {
                                document.body.removeChild(iframe);
                            }
                        }, 1000);
                    }
                }, 2000);

            } else {
                alert('Yazdırılacak tablo bulunamadı.');
            }
        });
    </script>
</x-app-layout>