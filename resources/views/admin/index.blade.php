<x-app-layout>
    <style>
        html { zoom: 80%; }
        body { background: #f1f5f9; }
        .center-box {
            position: relative; width: 90%; max-width: 1500px; margin: 2rem auto;
            background: white; border-radius: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 2.5rem;
        }
        .page-title { text-align: center; font-size: 2.8rem; font-weight: 800; color: #003366; margin-bottom: .5rem; transition:transform .2s ease }
        .center-box:hover .page-title{ transform:translateY(-1px) }
        .active-filter-info { text-align: center; font-size: 1.2rem; color: #555; margin-bottom: 2rem; }
        .modern-btn {
            background: linear-gradient(135deg, #003366 0%, #00509e 100%); color: white; padding: .5rem 2rem;
            border-radius: 1rem; font-weight: 700; font-size: 1.1rem; box-shadow: 0 6px 15px rgba(0, 80, 158, .5);
            border: none; cursor: pointer; position: relative; transition: all .35s ease;
        }
        .modern-btn:hover { background: linear-gradient(135deg, #00509e 0%, #003366 100%); box-shadow: 0 8px 20px rgba(0,80,158,.7); transform: scale(1.07); }

        .dropdown-menu { display: none; position: absolute; top: 3rem; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg,#fff 0%,#f8fafc 100%);
            border:1px solid #e5e7eb; border-radius:1rem; box-shadow:0 20px 40px rgba(0,0,0,.1),0 10px 20px rgba(0,0,0,.05);
            width:320px; z-index:50; padding:0; backdrop-filter: blur(10px); border:1px solid rgba(255,255,255,.2);
        }
        .dropdown-menu.active{display:block}
        .dropdown-menu ul{list-style:none;padding:0;margin:0}
        .dropdown-menu li{padding:.4rem .5rem; cursor:pointer; transition:.2s; user-select:none; color:#333}
        .dropdown-menu li:hover{ background:#003366!important; color:#fff!important }
        .search-box{ padding:1rem; border-bottom:1px solid #f1f5f9; background:linear-gradient(135deg,#f8fafc 0%,#fff 100%); border-radius:1rem 1rem 0 0;}
        .search-input-wrapper{ position:relative; display:flex; align-items:center; }
        .search-icon{ position:absolute; left:12px; color:#64748b; z-index:1}
        #globalSearch{ width:100%; padding:12px 12px 12px 40px; border:2px solid #e2e8f0; border-radius:.75rem; font-size:.9rem; background:#fff; transition:.3s; box-shadow:0 2px 4px rgba(0,0,0,.05) }
        #globalSearch:focus{ outline:none; border-color:#003366; box-shadow:0 0 0 3px rgba(0,51,102,.1); transform: translateY(-1px)}
        .highlight{ background:linear-gradient(45deg,#ff6b6b,#ee5a52,#ff6b6b); background-size:200% 200%; animation:highlightPulse 1.5s ease-in-out infinite; color:#fff; font-weight:bold; padding:2px 4px; border-radius:3px; box-shadow:0 2px 4px rgba(255,107,107,.3)}
        @keyframes highlightPulse{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .highlight-row{ background:linear-gradient(45deg,#fff5f5,#ffe8e8,#fff5f5)!important; animation:rowPulse 2s ease-in-out infinite; border-left:4px solid #ff6b6b }
        @keyframes rowPulse{0%{background:linear-gradient(45deg,#fff5f5,#ffe8e8,#fff5f5)}50%{background:linear-gradient(45deg,#ffe8e8,#ffd6d6,#ffe8e8)}100%{background:linear-gradient(45deg,#fff5f5,#ffe8e8,#fff5f5)}}

        .filter-option{ display:flex; align-items:center; justify-content:space-between; padding:.50rem .8rem; cursor:pointer; transition:.3s cubic-bezier(.4,0,.2,1); user-select:none; border-radius:.5rem; margin:.2rem .5rem; position:relative; background:linear-gradient(135deg,#d1e7ff 0%,#e3f2fd 100%); color:#003366; font-weight:600; border:2px solid #003366; box-shadow:0 2px 8px rgba(0,51,102,.1)}
        .filter-option:hover{ background:linear-gradient(135deg,#e3f2fd 0%,#f3f4f6 100%); transform:translateY(-2px); box-shadow:0 8px 25px rgba(0,51,102,.15)}
        .filter-option.selected{ background:linear-gradient(135deg,#003366 0%,#00509e 100%); color:#fff; border:2px solid #003366; box-shadow:0 4px 15px rgba(0,51,102,.3)}
        .filter-option:not(.selected){ background:linear-gradient(135deg,#f8f9fa 0%,#fff 100%); color:#6c757d; font-weight:400; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,.05) }
        .filter-option input{ display:none; width:55%; margin-left:.8rem; padding:.3rem .5rem; font-size:.8rem; border:1px solid #e0e0e0ff; border-radius:.3rem; box-sizing:border-box; background:#fff; transition:border-color .3s; flex-shrink:0; height:1.8rem}
        .filter-option input:focus{ outline:none; border-color:#003366; box-shadow:0 0 0 2px rgba(28,196,129,.1)}
        .filter-option.selected input{ display:block; background:#fff; color:#374151}
        .filter-dropdown{ left:50%; transform:translateX(-50%); position:absolute; top:100%}
        .record-dropdown{ left:0; position:absolute; top:100%; transform: translateY(10px); width:100%; z-index:999}
        .dropdown-container{ position:relative; display:inline-block }
        #reportMenu.dropdown-menu{ font-size:1rem; padding:1rem }
        .clear-btn{ background:linear-gradient(135deg,#dc3545 0%,#c82333 100%); color:#fff; border:none; cursor:pointer; transition:.35s; display:inline-flex; align-items:center; gap:.5rem; padding:.4rem 1rem; font-size:1rem; font-weight:600; border-radius:.5rem; box-shadow:0 4px 12px rgba(220,53,69,.3)}
        .clear-btn:hover{ background:linear-gradient(135deg,#c82333 0%,#dc3545 100%); transform:translateY(-2px); box-shadow:0 6px 16px rgba(220,53,69,.4)}
        .clear-icon{ width:16px; height:16px; fill:currentColor; transition:.3s }
        .clear-btn:hover .clear-icon{ transform:scale(1.1); animation:clearPulse .6s ease-in-out}
        @keyframes clearPulse{0%{transform:scale(1)}50%{transform:scale(1.2)}100%{transform:scale(1.1)}}

        /* default tablo stilleri (genel) */
        table{ width:100%; }
        table th,table td{ padding:1rem; text-align:left }
        table th{ background:#f9fafb; font-weight:600 }

        .svg-button{ background:#fff; border:none; padding:10px; cursor:pointer; transition:.3s; border-radius:.6rem; box-shadow:0 2px 6px rgba(0,0,0,.08); margin-left:.75rem; vertical-align:middle }
        .svg-button:hover{ background:#f0f0f0 }
        .svg-path{ transition:stroke-width .3s; stroke-dasharray:100; stroke-dashoffset:0; stroke:#003366 }
        .svg-button:hover .svg-path{ stroke-width:2; animation:draw 500ms ease-in forwards }
        @keyframes draw{0%{stroke-dashoffset:100}100%{stroke-dashoffset:0}}

        .report-generate-button-container,.export-buttons-bottom{ display:flex; justify-content:center; margin-top:2rem; gap:1.5rem; margin-bottom:1rem}
        .report-generate-button,.export-button-bottom{
            background:linear-gradient(135deg,#003366 0%,#00509e 100%); color:#fff; padding:.75rem 2rem; border-radius:1rem; font-weight:700; font-size:1.1rem;
            box-shadow:0 6px 15px rgba(0,80,158,.5); border:none; cursor:pointer; transition:.35s; display:inline-flex; align-items:center; gap:.5rem
        }
        .report-generate-button:hover,.export-button-bottom:hover{ background:linear-gradient(135deg,#00509e 0%,#003366 100%); box-shadow:0 8px 20px rgba(0,80,158,.7); transform:scale(1.07)}
        .export-button-bottom.excel{ background:linear-gradient(135deg,#28a745 0%,#218838 100%) }
        .export-button-bottom.excel:hover{ background:linear-gradient(135deg,#218838 0%,#28a745 100%) }
        .export-button-bottom.pdf{ background:linear-gradient(135deg,#dc3545 0%,#c82333 100%) }
        .export-button-bottom.pdf:hover{ background:linear-gradient(135deg,#c82333 0%,#dc3545 100%) }
        .export-button-bottom i{ font-size:20px }
        .date-filter-inputs{ display:none }
        .date-filter-inputs input[type="date"]{ padding:.5rem; border:1px solid #ccc; border-radius:.5rem; font-size:.95rem; width:100%; box-sizing:border-box }

        /* --------- MODERN TABLO (kart görünüm + yan yana okunaklı) ---------- */
        .data-table{
            width:100%;
            border-collapse:separate !important;
            border-spacing:0 10px !important;
            table-layout:fixed;
        }

        /* TABLO BAŞLIKLARI – soft gri, kutusuz */
        .data-table thead th{
        background:#f7fafc;          /* yumuşak gri */
        color:#334155;               /* slate/duman */
        font-weight:700;
        padding:12px 14px;

        /* kutu görünümünü kaldır */
        border-radius:0;
        box-shadow:none;

        /* sadece ince alt çizgi */
        border:0;
        border-bottom:1px solid #e5e7eb;
        }
        
        .data-table tbody tr{
            background:#fff;
            box-shadow:0 6px 20px rgba(0,0,0,.06);
            transition:transform .18s ease, box-shadow .18s ease, background .18s ease;
        }
        .data-table tbody td{
            padding:14px;
            border:0;
        }
        .data-table tbody tr:hover{
            transform:translateY(-2px);
            box-shadow:0 12px 28px rgba(0,0,0,.1);
        }
        /* Sayısal kolonlar hizalı/tek satır
        .data-table tbody td:nth-child(3),
        .data-table tbody td:nth-child(4),
        .data-table tbody td:nth-child(5){
            font-variant-numeric: tabular-nums;
            letter-spacing:.2px;
            white-space:nowrap;
        } */
        /* === Tek satır + eşit satır yüksekliği + ellipsis === */
        :root{ --row-h:56px; }                    /* istersen 48–64px oynatabilirsin */

        .data-table{ table-layout: fixed; }       /* ellipsis için şart */

        .data-table th,
        .data-table td{
        white-space: nowrap !important;         /* tek satır */
        overflow: hidden !important;            /* taşanı gizle */
        text-overflow: ellipsis !important;     /* … */
        height: var(--row-h);                   /* eşit yükseklik */
        line-height: 1.1;
        vertical-align: middle;
        }

        /* İsteğe bağlı: kolon genişlikleri (8 sütunlu örneğe göre) */
        .data-table th:nth-child(1), .data-table td:nth-child(1){ width:14%; } /* Giriş Tarihi */
        .data-table th:nth-child(2), .data-table td:nth-child(2){ width:16%; } /* Ad Soyad */
        .data-table th:nth-child(3), .data-table td:nth-child(3){ width:12%; } /* T.C. No */
        .data-table th:nth-child(4), .data-table td:nth-child(4){ width:12%; } /* Telefon */
        .data-table th:nth-child(5), .data-table td:nth-child(5){ width:10%; } /* Plaka */
        .data-table th:nth-child(6), .data-table td:nth-child(6){ width:14%; } /* Ziyaret Sebebi */
        .data-table th:nth-child(7), .data-table td:nth-child(7){ width:14%; } /* Ziyaret Edilen */
        .data-table th:nth-child(8), .data-table td:nth-child(8){ width:8%;  } /* Ekleyen */


        @media (max-width: 920px){
          .data-table thead{ display:none; }
          .data-table, .data-table tbody, .data-table tr, .data-table td{ display:block; width:100%; }
          .data-table tbody tr{ border-radius:14px; padding:10px; }
          .data-table tbody td{ padding:8px 10px; }
          .data-table tbody td::before{
            content: attr(data-label);
            display:block; font-weight:700; color:#475569; margin-bottom:2px;
          }
        }

        /* --------- MODAL: soft fade + scale ---------- */
        .modal-overlay{
            position:fixed; inset:0;
            background:rgba(0,0,0,.35);
            backdrop-filter:saturate(120%) blur(2px);
            display:flex; align-items:flex-start; justify-content:center;
            padding:7vh 16px; z-index:1000;

            opacity:0; visibility:hidden; pointer-events:none;
            transition:opacity .22s ease, visibility .22s ease;
        }
        .modal-overlay.open{ opacity:1; visibility:visible; pointer-events:auto; }
        .modal-card{
            width:100%; max-width:680px;
            background:#fff; border-radius:16px; overflow:hidden;
            box-shadow:0 20px 60px rgba(0,0,0,.2);
            transform:translateY(-10px) scale(.98);
            transition:transform .22s ease;
        }
        .modal-overlay.open .modal-card{ transform:translateY(0) scale(1); }

        /* --------- CHIPS (modern mask seçimi) ---------- */
        :root{
            --chip-border:#e3e7ef; --chip-bg:#f5f7fb; --chip-on:#0b4a88;
            --chip-on-bg:linear-gradient(135deg,#0b4a88 0%,#1a73e8 100%);
        }
        .chip-toolbar{display:flex;align-items:center;justify-content:space-between;margin:8px 0 12px}
        .chip-ghost{background:transparent;border:1px dashed var(--chip-border);color:#1f2a44;padding:6px 10px;border-radius:10px;font-weight:600;cursor:pointer;transition:.2s;margin-right:8px}
        .chip-ghost:hover{border-color:var(--chip-on);color:var(--chip-on)}
        .chip-count{color:#6b7280;font-weight:600}
        .chip-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
        @media (max-width:520px){.chip-grid{grid-template-columns:1fr}}
        .chip-check{position:absolute;opacity:0;pointer-events:none}
        .chip{
          background:var(--chip-bg);border:1.5px solid var(--chip-border);border-radius:14px;
          padding:12px 14px;min-height:44px;display:flex;align-items:center;gap:10px;
          cursor:pointer;font-weight:700;color:#1f2a44;transition:.2s ease;box-shadow:0 1px 2px rgba(16,24,40,.04);position:relative;user-select:none;
        }
        .chip i{font-size:18px;opacity:.9}
        .chip .tick{
          margin-left:auto;width:22px;height:22px;border-radius:50%;background:#e9eef9;color:#4f5d7a;
          display:flex;align-items:center;justify-content:center;transform:scale(.9);transition:.2s
        }
        .chip:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(16,24,40,.08)}
        .chip-check:focus + .chip{outline:2px solid rgba(26,115,232,.35);outline-offset:2px}
        .chip-check:checked + .chip{color:#fff;border-color:transparent;background:var(--chip-on-bg);box-shadow:0 10px 20px rgba(26,115,232,.25)}
        .chip-check:checked + .chip .tick{background:#fff;color:var(--chip-on);transform:scale(1)}
        /* === Daha geniş + daha sıkı tablo (tek satır, ellipsis) === */
        :root{
        --table-font: 13px;      /* hücre yazısı */
        --table-head: 12.5px;    /* başlık yazısı */
        --pad-y: 7px;            /* hücre dikey padding */
        --pad-x: 10px;           /* hücre yatay padding */
        --row-gap: 6px;          /* satırlar arası boşluk (card boşluğu) */
        }

        /* Kutu biraz genişlesin ve gerekirse yatay scroll versin */
        .center-box{
        width: 96% !important;
        max-width: 1700px !important;
        overflow-x: auto; /* sonu kesilmesin */
        }

        /* Tablo genel yoğunluğu */
        .data-table{
        table-layout: fixed;                 /* sütunlar eşit ve tek satır */
        border-spacing: 0 var(--row-gap) !important;
        font-size: var(--table-font);
        }

        /* Başlık ve hücreler daha küçük ve yakın */
        .data-table thead th{
        font-size: var(--table-head);
        padding: var(--pad-y) var(--pad-x) !important;
        }
        .data-table th, .data-table td{
        padding: var(--pad-y) var(--pad-x) !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        height: 56px;                /* satır yüksekliği sabit, istersen 52-58 yap */
        vertical-align: middle;
        }

        /* Hafif daha sıkı kart gölgesi/boşluğu (isteğe bağlı) */
        .data-table tbody tr{
        box-shadow: 0 4px 14px rgba(0,0,0,.06);
        }

        /* --- Sütun genişliklerini ince ayar (8 kolonlu tabloya göre) --- */
        /* Giriş Tarihi | Ad Soyad | TC | Tel | Plaka | Sebep | Ziyaret Edilen | Ekleyen */
        .data-table th:nth-child(1), .data-table td:nth-child(1){ width: 170px; }
        .data-table th:nth-child(2), .data-table td:nth-child(2){ width: 220px; }
        .data-table th:nth-child(3), .data-table td:nth-child(3){ width: 140px; }
        .data-table th:nth-child(4), .data-table td:nth-child(4){ width: 135px; }
        .data-table th:nth-child(5), .data-table td:nth-child(5){ width: 110px; }
        .data-table th:nth-child(6), .data-table td:nth-child(6){ width: 170px; }
        .data-table th:nth-child(7), .data-table td:nth-child(7){ width: 170px; }
        .data-table th:nth-child(8), .data-table td:nth-child(8){ width: 140px; }

        /* Mobilde eski davranışı koru (kartlaştır) */
        @media (max-width: 920px){
        .center-box{ overflow-x: hidden; }
        .data-table{ table-layout: auto; } /* mobilde esnek kalsın */
        }

    </style>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <div class="py-6">
        <div class="center-box">
            <h2 class="page-title">Ziyaretçi Listesi</h2>
            <div class="active-filter-info"><span id="activeFilterText">Günlük Kayıtlar</span></div>

            <div class="flex justify-between items-center mb-6 relative" style="display:flex; justify-content: space-between; align-items: center;">
                <div class="relative">
                    <button id="filterBtn" type="button" class="modern-btn">Filtreleme Yap</button>
                    <div id="filterMenu" class="dropdown-menu filter-dropdown">
                        <div class="search-box">
                            <div class="search-input-wrapper">
                                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>
                                </svg>
                                <input type="text" placeholder="Tüm alanlarda ara..." id="globalSearch">
                            </div>
                        </div>
                        <ul id="filterOptions">
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
                        <div class="p-2" style="display:flex; gap:.5rem;">
                            <button id="applyFilters" class="flex-1 rounded-lg font-semibold modern-btn hover:brightness-110 transition" style="padding:.4rem 1.5rem; font-size:1rem;">Filtreyi Uygula</button>
                            <button id="clearFilters" class="clear-btn"><i class="bi bi-x-circle-fill clear-icon"></i> Temizle</button>
                        </div>
                    </div>
                </div>

                <div class="dropdown-container" style="position: relative;">
                    <button id="reportBtn" type="button" class="modern-btn">Kayıt Görüntüle</button>
                    <button id="refreshBtn" class="svg-button" aria-label="Yenile" title="Yenile">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#003366" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path class="svg-path" d="M21 12a9 9 0 1 1-2.64-6.36" /><polyline class="svg-path" points="21 3 21 9 15 9" />
                        </svg>
                    </button>
                    <div id="reportMenu" class="dropdown-menu record-dropdown">
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
                            <button id="applyDateRange" class="w-full rounded-lg font-semibold modern-btn hover:brightness-110 transition mt-2" style="padding:.4rem 1.5rem; font-size:1rem;">Uygula</button>
                        </div>
                    </div>
                </div>
            </div>

            <table class="data-table">
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
                                @php
                                    $label = match ($field) {
                                        'id' => 'ID',
                                        'entry_time' => 'Giriş Tarihi',
                                        'name' => 'Ad Soyad',
                                        'tc_no' => 'TC Kimlik No',
                                        'phone' => 'Telefon',
                                        'plate' => 'Plaka',
                                        'purpose' => 'Ziyaret Sebebi',
                                        'person_to_visit' => 'Ziyaret Edilen Kişi',
                                        'approved_by' => 'Ekleyen',
                                        default => $field,
                                    };
                                @endphp
                                <td data-label="{{ $label }}">
                                    @switch($field)
                                        @case('id') {{ $visit->id ?? '-' }} @break
                                        @case('entry_time') {{ $visit->entry_time ?? '-' }} @break
                                        @case('name') {{ $visit->visitor->name ?? '-' }} @break
                                        @case('tc_no') {{ $visit->visitor->tc_no ?? '-' }} @break
                                        @case('phone') {{ $visit->phone ?? '-' }} @break
                                        @case('plate') {{ $visit->plate ?? '-' }} @break
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
                <button id="exportUnmaskedExcelBtn" type="button" class="export-button-bottom excel"><i class="bi bi-file-earmark-excel-fill"></i> Excel</button>
                <button id="printUnmaskedBtn" type="button" class="export-button-bottom"><i class="bi bi-printer-fill"></i> Yazdır</button>
                <a id="exportUnmaskedPdfBtn" href="#" class="export-button-bottom pdf"><i class="bi bi-file-earmark-pdf-fill"></i> PDF</a>
            </div>

            <div class="report-generate-button-container">
                <button id="generateReportBtn" type="button" class="report-generate-button">Güvenli Rapor Oluştur</button>
            </div>
        </div>
    </div>

    <!-- Güvenli Rapor Modal (animasyonlu) -->
    <div id="maskModal" class="modal-overlay">
      <div class="modal-card">
        <div style="padding:16px 20px; border-bottom:1px solid #eef2f7; display:flex; align-items:center; gap:10px;">
          <strong style="font-size:18px;">Güvenli Rapor – Maskeleme Seç</strong>
          <button id="maskCloseBtn" type="button" style="margin-left:auto; border:none; background:transparent; font-size:22px; line-height:1; cursor:pointer;">×</button>
        </div>

        <div style="padding:16px 20px;">
            <div class="chip-toolbar">
                <div class="left">
                    <button type="button" class="chip-ghost" id="maskSelectAll"><i class="bi bi-check2-square"></i> Tümünü Seç</button>
                    <button type="button" class="chip-ghost" id="maskSelectNone"><i class="bi bi-square"></i> Hiçbirini Seçme</button>
                </div>
                <div class="right">
                    <span class="chip-count"><i class="bi bi-filter-square"></i> <b id="maskCount">5</b> / 5 seçili</span>
                </div>
            </div>

            <div class="chip-grid" id="maskChipGrid">
                <input id="mask_name"  class="chip-check" type="checkbox" name="mask[]" value="name" checked>
                <label for="mask_name"  class="chip"><i class="bi bi-person"></i><span>Ad Soyad</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>

                <input id="mask_tc"    class="chip-check" type="checkbox" name="mask[]" value="tc_no" checked>
                <label for="mask_tc"    class="chip"><i class="bi bi-card-text"></i><span>T.C. No</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>

                <input id="mask_phone" class="chip-check" type="checkbox" name="mask[]" value="phone" checked>
                <label for="mask_phone" class="chip"><i class="bi bi-telephone"></i><span>Telefon</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>

                <input id="mask_plate" class="chip-check" type="checkbox" name="mask[]" value="plate" checked>
                <label for="mask_plate" class="chip"><i class="bi bi-car-front"></i><span>Plaka</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>

                <input id="mask_zed"   class="chip-check" type="checkbox" name="mask[]" value="person_to_visit" checked>
                <label for="mask_zed"   class="chip"><i class="bi bi-person-check"></i><span>Ziyaret Edilen</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>
            </div>
        </div>

        <div style="padding:16px 20px; border-top:1px solid #eef2f7; display:flex; gap:10px; justify-content:flex-end;">
          <button id="maskCancelBtn" type="button" class="modern-btn" style="background:#e5e7eb; color:#111827;">Vazgeç</button>
          <button id="confirmGenerateReport" type="button" class="modern-btn">Raporu Oluştur</button>
        </div>
      </div>
    </div>

    <script>
    'use strict';

    // ==== Kısayol referanslar ====
    const filterBtn  = document.getElementById('filterBtn');
    const filterMenu = document.getElementById('filterMenu');
    const reportBtn  = document.getElementById('reportBtn');
    const reportMenu = document.getElementById('reportMenu');
    const refreshBtn = document.getElementById('refreshBtn');

    const generateReportBtn       = document.getElementById('generateReportBtn');
    const exportUnmaskedExcelBtn  = document.getElementById('exportUnmaskedExcelBtn');
    const printUnmaskedBtn        = document.getElementById('printUnmaskedBtn');
    const exportUnmaskedPdfBtn    = document.getElementById('exportUnmaskedPdfBtn');

    const dateRangeOption = document.getElementById('dateRangeOption');
    const dateRangeInputs = document.getElementById('dateRangeInputs');
    const applyDateRangeBtn = document.getElementById('applyDateRange');

    // ==== Modal ====
    const maskModal        = document.getElementById('maskModal');
    const maskCloseBtn     = document.getElementById('maskCloseBtn');
    const maskCancelBtn    = document.getElementById('maskCancelBtn');
    const maskSelectAll    = document.getElementById('maskSelectAll');
    const maskSelectNone   = document.getElementById('maskSelectNone');
    const confirmGenerateReport = document.getElementById('confirmGenerateReport');

    // CHIPS
    const maskChipGrid = document.getElementById('maskChipGrid');
    const maskCountEl  = document.getElementById('maskCount');

    function openMaskModal(){ maskModal?.classList.add('open'); }
    function closeMaskModal(){ maskModal?.classList.remove('open'); }

    function getMaskInputsNodeList() {
      return maskChipGrid ? maskChipGrid.querySelectorAll('.chip-check') : [];
    }
    function updateMaskCount() {
      if (!maskCountEl) return;
      const list = getMaskInputsNodeList();
      const selected = [...list].filter(i => i.checked).length;
      maskCountEl.textContent = selected;
    }
    function getMaskParamsFromModal(){
      const params = new URLSearchParams();
      const list = getMaskInputsNodeList();
      list.forEach(inp => { if (inp.checked) params.append('mask[]', inp.value); });
      return params;
    }
    function hydrateMaskFromUrl(){
      const url = new URL(window.location.href);
      const list = getMaskInputsNodeList();
      if (!list.length) return;
      const selected = new Set(url.searchParams.getAll('mask[]'));
      list.forEach(inp => { inp.checked = selected.size ? selected.has(inp.value) : inp.checked; });
      updateMaskCount();
    }

    maskCloseBtn?.addEventListener('click', closeMaskModal);
    maskCancelBtn?.addEventListener('click', closeMaskModal);
    maskSelectAll?.addEventListener('click', () => { getMaskInputsNodeList().forEach(i => i.checked = true); updateMaskCount(); });
    maskSelectNone?.addEventListener('click', () => { getMaskInputsNodeList().forEach(i => i.checked = false); updateMaskCount(); });
    maskChipGrid?.addEventListener('change', updateMaskCount);

    // ==== Filtre menüleri ====
    filterBtn?.addEventListener('click', () => filterMenu?.classList.toggle('active'));
    reportBtn?.addEventListener('click', () => reportMenu?.classList.toggle('active'));

    dateRangeOption?.addEventListener('click', () => {
      document.querySelectorAll('#reportMenu li').forEach(li => li.style.display = 'none');
      dateRangeInputs.style.display = 'block';
      dateRangeOption.style.display = 'block';
    });

    applyDateRangeBtn?.addEventListener('click', () => {
      const startDate = document.getElementById('start_date')?.value;
      const endDate   = document.getElementById('end_date')?.value;
      const params = new URLSearchParams(window.location.search);
      params.delete('date_filter');
      if (startDate) params.set('start_date', startDate);
      if (endDate)   params.set('end_date', endDate); else params.delete('end_date');

      document.querySelectorAll('.filter-option input').forEach(input => {
          const fieldName = input.id.replace('_value', '');
          if (input.value.trim() !== '') params.set(fieldName + '_value', input.value.trim());
          else params.delete(fieldName + '_value');
      });

      window.location.href = window.location.pathname + '?' + params.toString();
    });

    document.querySelectorAll('#reportMenu li').forEach(item => {
      item.addEventListener('click', () => {
        if (item.id === 'dateRangeOption') return;
        const type = item.dataset.type;
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.delete('start_date'); urlParams.delete('end_date'); urlParams.set('date_filter', type);

        document.querySelectorAll('.filter-option.selected input').forEach(input => {
          const fieldName = input.id.replace('_value', '');
          if (input.value.trim() !== '') urlParams.set(fieldName + '_value', input.value.trim());
          else urlParams.delete(fieldName + '_value');
        });

        const selectedFieldsFromFilter = [...document.querySelectorAll('.filter-option.selected')].map(opt => opt.getAttribute('data-field'));
        if (selectedFieldsFromFilter.length > 0) urlParams.set('filter', selectedFieldsFromFilter.join(','));
        else urlParams.delete('filter');

        window.location.href = window.location.pathname + '?' + urlParams.toString();
      });
    });

    refreshBtn?.addEventListener('click', () => {
      window.location.href = window.location.pathname + '?date_filter=daily';
    });

    // ==== Global Search ====
    document.getElementById('globalSearch')?.addEventListener('input', (e) => {
      const searchTerm = e.target.value.toLowerCase().trim();
      document.querySelectorAll('.highlight').forEach(el => { el.outerHTML = el.innerHTML; });
      document.querySelectorAll('.highlight-row').forEach(row => { row.classList.remove('highlight-row'); });

      const tableRows = document.querySelectorAll('tbody tr');
      tableRows.forEach(row => {
          const cells = row.querySelectorAll('td'); let hasMatch = false;
          cells.forEach(cell => {
              const cellText = cell.textContent;
              if (searchTerm && cellText.toLowerCase().includes(searchTerm)) {
                  const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                  cell.innerHTML = cellText.replace(regex, '<span class="highlight">$1</span>'); hasMatch = true;
              }
          });
          if (hasMatch) row.classList.add('highlight-row');
      });
      if (searchTerm === '') tableRows.forEach(row => row.classList.remove('highlight-row'));
    });

    // ==== Temizle ====
    document.getElementById('clearFilters')?.addEventListener('click', () => {
      document.querySelectorAll('.filter-option input').forEach(input => input.value = '');
      document.querySelectorAll('.filter-option').forEach(option => option.classList.add('selected'));
      const gs = document.getElementById('globalSearch'); if (gs) gs.value = '';
      document.querySelectorAll('.highlight').forEach(el => { el.outerHTML = el.innerHTML; });
      document.querySelectorAll('.highlight-row').forEach(row => row.classList.remove('highlight-row'));
      window.location.href = window.location.pathname + '?date_filter=daily';
    });

    // ==== Başlangıç durumları ====
    window.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.filter-option').forEach(option => option.classList.add('selected'));

      const urlParams = new URLSearchParams(window.location.search);
      const filterParam = urlParams.get('filter');
      if (filterParam) {
          const filters = filterParam.split(',');
          document.querySelectorAll('.filter-option').forEach(option => option.classList.add('selected'));
          document.querySelectorAll('.filter-option').forEach(option => {
              const field = option.getAttribute('data-field');
              if (!filters.includes(field)) {
                  option.classList.remove('selected');
                  const input = option.querySelector('input');
                  if (input) { input.style.display = 'none'; input.value = ''; }
              }
          });
      }

      const dateFilterParam = urlParams.get('date_filter');
      const startDateParam  = urlParams.get('start_date');
      const endDateParam    = urlParams.get('end_date');
      const activeFilterText = document.getElementById('activeFilterText');

      if (startDateParam) {
          const start = new Date(startDateParam).toLocaleDateString('tr-TR');
          const end = endDateParam ? new Date(endDateParam).toLocaleDateString('tr-TR') : 'Bugün';
          if (activeFilterText) activeFilterText.textContent = `(${start} - ${end} Aralığı)`;
      } else if (dateFilterParam) {
          const el = document.querySelector(`#reportMenu li[data-type="${dateFilterParam}"]`);
          if (el && activeFilterText) activeFilterText.textContent = `(${el.textContent} Kayıtlar)`;
      } else {
          if (activeFilterText) activeFilterText.textContent = '(Günlük Kayıtlar)';
      }

      updateMaskCount();
    });

    // ==== Filtre satırı tıklama ====
    document.querySelectorAll('.filter-option').forEach(option => {
      option.addEventListener('click', (e) => {
          if (e.target.tagName.toLowerCase() === 'input') return;
          option.classList.toggle('selected');
          const input = option.querySelector('input');
          if (input) {
            input.style.display = option.classList.contains('selected') ? 'block' : 'none';
            if (!option.classList.contains('selected')) input.value = '';
          }
      });
    });

    // ==== Filtreleri uygula ====
    document.getElementById('applyFilters')?.addEventListener('click', () => {
      const selectedOptions = [...document.querySelectorAll('.filter-option.selected')];
      if (selectedOptions.length === 0) { alert('Lütfen en az bir filtre seçiniz.'); return; }

      const params = new URLSearchParams(window.location.search);
      const selectedFields = selectedOptions.map(opt => opt.getAttribute('data-field'));
      params.set('filter', selectedFields.join(','));

      selectedOptions.forEach(opt => {
          const field = opt.getAttribute('data-field');
          const input = opt.querySelector('input');
          if (input && input.value.trim() !== '') params.set(field + '_value', input.value.trim());
          else params.delete(field + '_value');
      });

      const dateFilterParam = new URLSearchParams(window.location.search).get('date_filter');
      const startDateParam  = new URLSearchParams(window.location.search).get('start_date');
      const endDateParam    = new URLSearchParams(window.location.search).get('end_date');
      if (startDateParam) { params.set('start_date', startDateParam); params.delete('date_filter'); }
      if (endDateParam) params.set('end_date', endDateParam);
      if (dateFilterParam && !startDateParam) params.set('date_filter', dateFilterParam);
      else if (!startDateParam) params.set('date_filter', 'daily');

      window.location.href = window.location.pathname + '?' + params.toString();
    });

    // ==== Dışarı tıklayınca menüleri kapat ====
    document.addEventListener('click', e => {
      if (!filterBtn?.contains(e.target) && !filterMenu?.contains(e.target)) filterMenu?.classList.remove('active');
      if (!reportBtn?.contains(e.target) && !reportMenu?.contains(e.target) && !dateRangeInputs?.contains(e.target)) {
          reportMenu?.classList.remove('active');
          if (dateRangeInputs) dateRangeInputs.style.display = 'none';
          document.querySelectorAll('#reportMenu li').forEach(li => li.style.display = 'block');
      }
    });

    // ==== Export ortak paramlar ====
    function getCommonExportParams(isReportPage = false) {
      const urlParams = new URLSearchParams(window.location.search);
      const exportParams = new URLSearchParams();

      const allFields = ['id','entry_time','name','tc_no','phone','plate','purpose','person_to_visit','approved_by'];
      const filterParam = urlParams.get('filter');
      let selectedFields = filterParam ? filterParam.split(',') : allFields;
      if (isReportPage) selectedFields = selectedFields.filter(field => field !== 'id');

      selectedFields.forEach(field => {
          exportParams.append('fields[]', field);
          const value = urlParams.get(field + '_value');
          if (value) exportParams.set(field + '_value', value);
      });

      const dateFilterParam = urlParams.get('date_filter');
      const startDateParam  = urlParams.get('start_date');
      const endDateParam    = urlParams.get('end_date');
      if (startDateParam) exportParams.set('start_date', startDateParam);
      if (endDateParam)   exportParams.set('end_date', endDateParam);
      if (dateFilterParam) exportParams.set('date_filter', dateFilterParam);
      exportParams.set('sort_order', 'desc');

      return exportParams;
    }

    // ==== Güvenli Rapor (modal aç) ====
    generateReportBtn?.addEventListener('click', () => { hydrateMaskFromUrl(); openMaskModal(); });

    // ==== Modal onay: maskeleri ekle ve rapora git ====
    confirmGenerateReport?.addEventListener('click', () => {
      const reportParams = getCommonExportParams(true);
      const maskParams   = getMaskParamsFromModal();
      for (const [k,v] of maskParams.entries()) reportParams.append(k, v);

      if (![...maskParams.keys()].length) {
          if (!confirm('Hiçbir alan maskelenmeyecek. Devam etmek istiyor musun?')) return;
      }
      window.location.href = `/admin/generate-report?` + reportParams.toString();
    });

    // ==== Excel/PDF export (maskesiz) ====
    exportUnmaskedExcelBtn?.addEventListener('click', () => {
      const exportParams = getCommonExportParams(false);
      exportParams.set('unmasked','true');
      window.location.href = `/report/export?` + exportParams.toString();
    });
    exportUnmaskedPdfBtn?.addEventListener('click', () => {
      const pdfParams = getCommonExportParams(false);
      window.location.href = `/admin/export-pdf-unmasked?` + pdfParams.toString();
    });

    // --- YAZDIR: sade tablo şablonu
    function printTableLikeReport(opts = {}) {
        const table = document.querySelector('table');
        if (!table) return alert('Yazdırılacak tablo bulunamadı.');

        const {
        titleText = 'Ziyaretçi Raporu',
        rangeSelector = null,
        dateLocale = 'tr-TR',
        compact = true // <<< küçük font + dar padding
        } = opts;

        const todayStr = new Date().toLocaleDateString(dateLocale, {
        day: '2-digit', month: '2-digit', year: 'numeric'
        });

        let finalTitle = titleText;
        if (rangeSelector) {
        const r = document.querySelector(rangeSelector)?.innerText.trim();
        if (r) finalTitle += ' ' + r;
        }

        // Compact boyutlar
        const MARGIN = compact ? '0.7cm' : '1cm';
        const TITLE_FS = compact ? '18px' : '24px';
        const CELL_FS  = compact ? '9px'  : '10px';
        const PAD      = compact ? '3px'  : '5px';

        const html = `
        <html>
            <head>
            <meta charset="utf-8" />
            <title>Yazdır - ${finalTitle}</title>
            <style>
                @page { size: A4 portrait; margin: ${MARGIN}; }
                * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                body { margin: ${MARGIN}; font-family: Arial, Helvetica, sans-serif; }
                h2.page-title {
                text-align: center; font-size: ${TITLE_FS}; font-weight: bold;
                color: #003366; margin-bottom: 14px;
                }
                .date { text-align: right; font-size: 9px; margin-bottom: 4px; color:#333; }
                table {
                width: 100%; border-collapse: collapse; border-spacing: 0;
                table-layout: fixed; word-break: break-word; hyphens: auto;
                }
                thead th {
                background-color: #003366; color: #ffffff;
                padding: ${PAD}; border: 1px solid #ccc; font-size: ${CELL_FS}; vertical-align: top;
                }
                tbody td {
                padding: ${PAD}; border: 1px solid #ccc; font-size: ${CELL_FS}; vertical-align: top;
                }
                tr { page-break-inside: avoid; }
            </style>
            </head>
            <body>
            <div class="date">${todayStr}</div>
            <h2 class="page-title">${finalTitle}</h2>
            ${table.outerHTML}
            </body>
        </html>
        `;

        const w = window.open('', '_blank');
        w.document.open();
        w.document.write(html);
        w.document.close();
        w.focus();
        w.print();
        setTimeout(() => { try { w.close(); } catch(e){} }, 300);
    }

    // Rapor sayfasındaki buton
    document.getElementById('printReportBtn')?.addEventListener('click', () => {
        const h2 = document.querySelector('h2');
        const titleOnly = (h2?.childNodes[0]?.textContent || 'Ziyaretçi Raporu').trim();
        printTableLikeReport({
        titleText: titleOnly,
        rangeSelector: 'h2 span',
        compact: true
        });
    });

    // Liste sayfasındaki buton
    document.getElementById('printUnmaskedBtn')?.addEventListener('click', () => {
        printTableLikeReport({ titleText: 'Ziyaretçi Listesi', compact: true });
    });
    </script>
</x-app-layout>
