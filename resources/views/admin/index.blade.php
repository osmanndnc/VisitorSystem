<x-app-layout>
    <head>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
        
        <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.dataTables.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    </head>

    <style>
        html { zoom: 80%; }
        body { background: #f1f5f9; }
        .main-content-container { display: flex; gap: 1.5rem; align-items: flex-start; }
        .filters-panel { flex: 0 0 280px; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #f1f5f9; padding: 16px; height: fit-content; position: sticky; top: 2rem; max-height: 75vh; overflow-y: auto; }
        .table-section { flex: 1; min-width: 0; position: relative; }
        .center-box { overflow: visible !important; position: relative; width: 95%; max-width: 1800px; margin: 2rem auto; background: white; border-radius: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 3rem; }
        .page-title { text-align: center; font-size: 2.8rem; font-weight: 800; color: #003366; margin-bottom: .5rem; transition:transform .2s ease }
        .center-box:hover .page-title { transform:translateY(-1px) }
        .active-filter-info { text-align: center; font-size: 1.2rem; color: #555; margin-bottom: 2rem; }
        .modern-btn { background: linear-gradient(135deg, #003366 0%, #00509e 100%); color: white; padding: .5rem 2rem; border-radius: 1rem; font-weight: 700; font-size: 1.1rem; box-shadow: 0 6px 15px rgba(0, 80, 158, .5); border: none; cursor: pointer; position: relative; transition: all .35s ease; }
        .modern-btn:hover { background: linear-gradient(135deg, #00509e 0%, #003366 100%); box-shadow: 0 8px 20px rgba(0,80,158,.7); transform: scale(1.07); }
        .column-filter-section { padding: 16px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); position: relative; }
        .column-filter-section h4 { margin: 0 0 16px 0; color: #334155; font-size: 16px; font-weight: 600; text-align: center; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; cursor: pointer; user-select: none; transition: all 0.3s ease; }
        .column-filter-section h4:hover { color: #003366; border-bottom-color: #003366; }
        .column-filter-section h4::after { content: "▼"; margin-left: 8px; font-size: 12px; transition: transform 0.3s ease; }
        .column-filter-section.expanded h4::after { transform: rotate(180deg); }
        .column-checkboxes { display: none; grid-template-columns: 1fr; gap: 8px; margin-bottom: 16px; animation: slideDown 0.3s ease; }
        .column-filter-section.expanded .column-checkboxes { display: grid; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .column-checkbox { display: flex; align-items: center; justify-content: space-between; cursor: pointer; font-size: 12px; font-weight: 500; padding: 10px 12px; background: white; border: 2px solid #e2e8f0; border-radius: 8px; transition: all 0.3s ease; position: relative; user-select: none; height: auto; min-height: 44px; box-sizing: border-box; word-wrap: break-word; white-space: normal; line-height: 1.3; }
        .column-checkbox:hover { background: #f1f5f9; border-color: #003366; transform: translateY(-2px); box-shadow: 4px 12px rgba(0,0,0,0.1); }
        .column-checkbox.selected { background: #003366; color: white; border-color: #003366; box-shadow: 4px 12px rgba(0,51,102,0.3); }
        .column-checkbox.selected::after { content: "✓"; position: absolute; right: 12px; font-weight: bold; color: white; font-size: 16px; }
        .show-all-columns-btn { display: none; width: 100%; padding: 12px 16px; background: #f1f5f9; border: 2px solid #e2e8f0; border-radius: 8px; color: #334155; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-align: center; }
        .column-filter-section.expanded .show-all-columns-btn { display: block; }
        .show-all-columns-btn:hover { background: #e2e8f0; border-color: #003366; color: #003366; transform: translateY(-1px); }
        .filter-section { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
        .filter-section:last-child { border-bottom: none; margin-bottom: 0; }
        .filter-section h4 { margin: 0 0 8px 0; font-size: 14px; font-weight: 700; color: #334155; }
        .filter-input { width: 100%; padding: 6px 8px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 12px; background: white; transition: all 0.3s; margin-bottom: 8px; }
        .filter-input:focus { outline: none; border-color: #003366; box-shadow: 0 0 0 3px rgba(0,51,102,0.1); }
        .filter-buttons { display: flex; gap: 8px; margin-top: 12px; }
        .filter-apply { flex: 1; background: linear-gradient(135deg, #003366 0%, #00509e 100%); color: white; border: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 12px; }
        .filter-apply:hover { background: linear-gradient(135deg, #00509e 0%, #003366 100%); transform: translateY(-1px); }
        .filter-clear { background: #e5e7eb; color: #374151; border: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 12px; }
        .filter-clear:hover { background: #d1d5db; }
        .dropdown-container { position: relative; z-index: 1000; }
        .dropdown-menu { display: none; position: absolute; top: 3.5rem; left: 0; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid rgba(0, 51, 102, 0.1); border-radius: 16px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 10px 20px rgba(0, 0, 0, 0.1); width: 250px; z-index: 99999 !important; padding: 0; backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2); animation: dropdownSlide 0.3s ease-out; overflow: hidden; }
        @keyframes dropdownSlide { from { opacity: 0; transform: translateY(-10px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .dropdown-menu.active { display: block; }
        .dropdown-menu ul { list-style: none; padding: 0; margin: 0; }
        .dropdown-menu li { padding: 16px 20px; cursor: pointer; transition: all 0.3s ease; user-select: none; color: #334155; font-weight: 500; font-size: 14px; border-bottom: 1px solid rgba(0, 51, 102, 0.05); position: relative; display: flex; align-items: center; gap: 12px; }
        .dropdown-menu li:last-child { border-bottom: none; }
        .dropdown-menu li:hover { background: linear-gradient(135deg, #003366 0%, #00509e 100%); color: #fff; transform: translateX(8px); box-shadow: 0 4px 12px rgba(0, 51, 102, 0.3); }
        .dropdown-menu li::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(135deg, #003366 0%, #00509e 100%); transform: scaleY(0); transition: transform 0.3s ease; }
        .dropdown-menu li:hover::before { transform: scaleY(1); }
        .date-filter-inputs { display: none; padding: 20px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-top: 1px solid rgba(0, 51, 102, 0.1); }
        .date-filter-inputs label { display: block; font-weight: 600; color: #334155; margin-bottom: 8px; font-size: 13px; }
        .date-input-wrapper { position: relative; display: flex; align-items: center; }
        .date-input-wrapper input[type="date"] { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; background: white; transition: all 0.3s ease; margin-bottom: 16px; box-sizing: border-box; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); }
        .date-input-wrapper input[type="date"]:focus { outline: none; border-color: #003366; box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.1); transform: translateY(-2px); }
        #applyDateRange { width: 100%; padding: 14px 20px; background: linear-gradient(135deg, #003366 0%, #00509e 100%); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0, 51, 102, 0.3); margin-top: 8px; }
        #applyDateRange:hover { background: linear-gradient(135deg, #00509e 0%, #003366 100%); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 51, 102, 0.4); }
        #dateRangeOption::after { content: ""; margin-left: auto; font-size: 16px; }
        .highlight{ background:linear-gradient(45deg,#ff6b6b,#ee5a52,#ff6b6b); background-size:200% 200%; animation:highlightPulse 1.5s ease-in-out infinite; color:#fff; font-weight:bold; padding:2px 4px; border-radius:3px; box-shadow:0 2px 4px rgba(255,107,107,.3)}
        @keyframes highlightPulse{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .highlight-row{ background:linear-gradient(45deg,#fff5f5,#ffe8e8,#fff5f5)!important; animation:rowPulse 2s ease-in-out infinite; border-left:4px solid #ff6b6b }
        @keyframes rowPulse{0%{background:linear-gradient(45deg,#fff5f5,#ffe8e8,#fff5f5)}50%{background:linear-gradient(45deg,#ffe8e8,#ffd6d6,#ffe8e8)}100%{background:linear-gradient(45deg,#fff5f5,#ffe8e8,#fff5f5)}}
        .clear-btn{ background:linear-gradient(135deg,#dc3545 0%,#c82333 100%); color:#fff; border:none; cursor:pointer; transition:.35s; display:inline-flex; align-items:center; gap:.5rem; padding:.4rem 1rem; font-size:1rem; font-weight:600; border-radius:.5rem; box-shadow:0 4px 12px rgba(220,53,69,.3)}
        .clear-btn:hover{ background:linear-gradient(135deg,#c82333 0%,#dc3545 100%); transform:translateY(-2px); box-shadow:0 6px 16px rgba(220,53,69,.4)}
        .clear-icon{ width:16px; height:16px; fill:currentColor; transition:.3s }
        .clear-btn:hover .clear-icon{ transform:scale(1.1); animation:clearPulse .6s ease-in-out}
        @keyframes clearPulse{0%{transform:scale(1)}50%{transform:scale(1.2)}100%{transform:scale(1.1)}}
        table{ width:100%; }
        table th,table td{ padding:1rem; text-align:left }
        table th{ background:#f9fafb; font-weight:600 }
        .svg-button{ background:#fff; border:none; padding:10px; cursor:pointer; transition:.3s; border-radius:.6rem; box-shadow:0 2px 6px rgba(0,0,0,.08); margin-left:.75rem; vertical-align:middle }
        .svg-button:hover{ background:#f0f0f0 }
        .svg-path{ transition:stroke-width .3s; stroke-dasharray:100; stroke-dashoffset:0; stroke:#003366 }
        .svg-button:hover .svg-path{ stroke-width:2; animation:draw 500ms ease forwards }
        @keyframes draw{0%{stroke-dashoffset:100}100%{stroke-dashoffset:0}}
        .report-generate-button-container,.export-buttons-bottom{ display:flex; justify-content:center; margin-top:2rem; gap:1.5rem; margin-bottom:1rem}
        .report-generate-button,.export-button-bottom{ background:linear-gradient(135deg,#003366 0%,#00509e 100%); color:#fff; padding:.75rem 2rem; border-radius:1rem; font-weight:700; font-size:1.1rem; box-shadow:0 6px 15px rgba(0,80,158,.5); border:none; cursor:pointer; transition:.35s; display:inline-flex; align-items:center; gap:.5rem }
        .report-generate-button:hover,.export-button-bottom:hover{ background:linear-gradient(135deg,#00509e 0%,#003366 100%); box-shadow:0 8px 20px rgba(0,80,158,.7); transform:scale(1.07)}
        .export-button-bottom.excel{ background:linear-gradient(135deg,#28a745 0%,#218838 100%) }
        .export-button-bottom.excel:hover{ background:linear-gradient(135deg,#218838 0%,#28a745 100%) }
        .export-button-bottom.pdf{ background:linear-gradient(135deg,#dc3545 0%,#c82333 100%) }
        .export-button-bottom.pdf:hover{ background:linear-gradient(135deg,#c82333 0%,#dc3545 100%) }
        .export-button-bottom i{ font-size:20px }
        .dataTables_wrapper { margin-top: 1.5rem; overflow: visible !important; clear: both; }
        .dataTables_wrapper .dataTables_length { float: left; margin-bottom: 1rem; }
        .dataTables_wrapper .dataTables_length select { padding: 6px 12px; border: 2px solid #e2e8f0; border-radius: 8px; background: white; font-size: 14px; margin: 0 0.5rem; transition: all 0.3s; }
        .dataTables_wrapper .dataTables_length select:focus { outline: none; border-color: #003366; box-shadow: 0 0 0 3px rgba(0,51,102,0.1); }
        .dataTables_wrapper .dataTables_filter { float: right; text-align: right; margin-bottom: 1rem; }
        .dataTables_wrapper .dataTables_filter input { padding: 8px 16px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px; width: 280px; margin-left: 0.5rem; transition: all 0.3s; }
        .dataTables_wrapper .dataTables_filter input:focus { outline: none; border-color: #003366; box-shadow: 0 0 0 3px rgba(0,51,102,0.1); }
        .dataTables_wrapper .dataTables_info { float: left; font-size: 14px; color: #64748b; padding: 15px 0; margin-top: 1rem; }
        .dataTables_wrapper .dataTables_paginate { float: right; margin-top: 1rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 8px 12px; margin: 0 2px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; color: #374151; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #003366; color: white; border-color: #003366; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #003366; color: white; border-color: #003366; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { color: #9ca3af; cursor: not-allowed; background: #f9fafb; }
        .dataTables_wrapper .dataTables_processing { background: rgba(0,51,102,0.9); color: white; border-radius: 8px; padding: 20px; font-size: 16px; }
        .data-table { width: 100% !important; min-width: 1200px; border-collapse: separate !important; border-spacing: 0 6px !important; margin-top: 1rem !important; clear: both; table-layout: fixed; }
        .data-table th:nth-child(1), .data-table td:nth-child(1) { width: 120px; min-width: 120px; max-width: 120px; }
        .data-table th:nth-child(2), .data-table td:nth-child(2) { width: 160px; min-width: 160px; max-width: 160px; }
        .data-table th:nth-child(3), .data-table td:nth-child(3) { width: 130px; min-width: 130px; max-width: 130px; }
        .data-table th:nth-child(4), .data-table td:nth-child(4) { width: 120px; min-width: 120px; max-width: 120px; }
        .data-table th:nth-child(5), .data-table td:nth-child(5) { width: 100px; min-width: 100px; max-width: 100px; }
        .data-table th:nth-child(6), .data-table td:nth-child(6) { width: 140px; min-width: 140px; max-width: 140px; }
        .data-table th:nth-child(7), .data-table td:nth-child(7) { width: 180px; min-width: 180px; max-width: 180px; }
        .data-table th:nth-child(8), .data-table td:nth-child(8) { width: 160px; min-width: 160px; max-width: 160px; }
        .data-table th:nth-child(9), .data-table td:nth-child(9) { width: 120px; min-width: 120px; max-width: 120px; }
        .data-table thead th { background: #f7fafc; color: #334155; font-weight: 700; padding: 12px 8px; border-radius: 0; box-shadow: none; border: 0; border-bottom: 2px solid #e5e7eb; position: relative; cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 13px; }
        .data-table tbody tr { background: #fff; box-shadow: 0 4px 14px rgba(0,0,0,.06); transition: transform .18s ease, box-shadow .18s ease, background .18s ease; }
        .data-table tbody td { padding: 12px 8px; border: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle; text-align: left; max-width: 0; font-size: 12px; line-height: 1.4; }
        .data-table th:nth-child(7), .data-table td:nth-child(7) { word-wrap: break-word; white-space: normal; min-height: 60px; vertical-align: top; padding-top: 16px; padding-bottom: 16px; }
        .data-table td[data-label="Ziyaret Edilen Birim"]:hover::after { content: attr(data-label); position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); background: #333; color: white; padding: 8px 12px; border-radius: 6px; font-size: 12px; white-space: nowrap; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
        .data-table thead .sorting:after, .data-table thead .sorting_asc:after, .data-table thead .sorting_desc:after { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #6b7280; }
        .data-table thead .sorting:after { content: "⇅"; }
        .data-table thead .sorting_asc:after { content: "↑"; color: #003366; }
        .data-table thead .sorting_desc:after { content: "↓"; color: #003366; }
        .data-table .empty-cell { text-align: center; padding: 40px 8px; color: #64748b; font-style: italic; }
        @media (max-width: 1400px) { .main-content-container { flex-direction: column; } .filters-panel { position: static; margin-bottom: 2rem; } .data-table th:nth-child(7), .data-table td:nth-child(7) { width: 160px; min-width: 160px; max-width: 160px; } }
        @media (max-width: 1200px) { .data-table th:nth-child(7), .data-table td:nth-child(7) { width: 140px; min-width: 140px; max-width: 140px; } }
        @media (max-width: 920px) { .center-box { overflow-x: hidden; width: 95%; } .data-table { table-layout: auto; min-width: auto; } .data-table thead { display: none; } .data-table, .data-table tbody, .data-table tr, .data-table td { display: block; width: 100%; } .data-table tbody tr { border-radius: 14px; padding: 10px; margin-bottom: 10px; } .data-table tbody td { padding: 8px 10px; } .data-table tbody td::before { content: attr(data-label); display: block; font-weight: 700; color: #475569; margin-bottom: 2px; } }
        .modal-overlay{ position:fixed; inset:0; background:rgba(0,0,0,.35); backdrop-filter:saturate(120%) blur(2px); display:flex; align-items:flex-start; justify-content:center; padding:7vh 16px; z-index:1000; opacity:0; visibility:hidden; pointer-events:none; transition:opacity .22s ease, visibility .22s ease; }
        .modal-overlay.open{ opacity:1; visibility:visible; pointer-events:auto; }
        .modal-card{ width:100%; max-width:680px; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.2); transform:translateY(-10px) scale(.98); transition:transform .22s ease; }
        .modal-overlay.open .modal-card{ transform:translateY(0) scale(1); }
        :root{ --chip-border:#e3e7ef; --chip-bg:#f5f7fb; --chip-on:#0b4a88; --chip-on-bg:linear-gradient(135deg,#0b4a88 0%,#1a73e8 100%); }
        .chip-toolbar{display:flex;align-items:center;justify-content:space-between;margin:8px 0 12px}
        .chip-ghost{background:transparent;border:1px dashed var(--chip-border);color:#1f2a44;padding:6px 10px;border-radius:10px;font-weight:600;cursor:pointer;transition:.2s;margin-right:8px}
        .chip-ghost:hover{border-color:var(--chip-on);color:var(--chip-on)}
        .chip-count{color:#6b7280;font-weight:600}
        .chip-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
        @media (max-width:520px){.chip-grid{grid-template-columns:1fr}}
        .chip-check{position:absolute;opacity:0;pointer-events:none}
        .chip{ background:var(--chip-bg);border:1.5px solid var(--chip-border);border-radius:14px; padding:12px 14px;min-height:44px;display:flex;align-items:center;gap:10px; cursor:pointer;font-weight:700;color:#1f2a44;transition:.2s ease;box-shadow:0 1px 2px rgba(16,24,40,.04);position:relative;user-select:none; }
        .chip i{font-size:18px;opacity:.9}
        .chip .tick{ margin-left:auto;width:22px;height:22px;border-radius:50%;background:#e9eef9;color:#4f5d7a; display:flex;align-items:center;justify-content:center;transform:scale(.9);transition:.2s }
        .chip:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(16,24,40,.08)}
        .chip-check:focus + .chip{outline:2px solid rgba(26,115,232,.35);outline-offset:2px}
        .chip-check:checked + .chip{color:#fff;border-color:transparent;background:var(--chip-on-bg);box-shadow:0 10px 20px rgba(26,115,232,.25)}
        .chip-check:checked + .chip .tick{background:#fff;color:var(--chip-on);transform:scale(1)}
        .custom-select-wrapper { position: relative; width: 100%; }
        .custom-select-input { cursor: text; background-color: #fff; position: relative; z-index: 2; }
        .custom-select-arrow { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 10px; pointer-events: auto; z-index: 3; transition: transform 0.2s; cursor: pointer; padding: 5px; }
        .custom-select-arrow:hover { color: #003366; transform: translateY(-50%) scale(1.1); }
        .custom-select-dropdown { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #d1d5db; border-radius: 8px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); z-index: 100 !important; display: none; max-height: 300px; overflow: hidden; margin-top: 4px; }
        .custom-select-search { padding: 12px; border-bottom: 1px solid #e2e8f0; }
        .search-input { width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .custom-select-options { max-height: 200px; overflow-y: auto; }
        .custom-select-option { padding: 10px 12px; cursor: pointer; transition: background-color 0.2s; font-size: 14px; color: #374151; }
        .custom-select-option:hover { background-color: #f3f4f6; }
        .custom-select-option:first-child { border-top-left-radius: 8px; border-top-right-radius: 8px; }
        .custom-select-option:last-child { border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; }
    </style>

    <div class="py-6">
        <div class="center-box">
            <h2 class="page-title">Ziyaretçi Listesi</h2>
            <div class="active-filter-info"><span id="activeFilterText">Günlük Kayıtlar</span></div>

            <div class="main-content-container">
                <div class="filters-panel">
                    <div class="column-filter-section" id="columnFilterSection">
                        <h4 onclick="toggleColumnSection()">Sütun Seçimi</h4>
                        <div class="column-checkboxes">
                            <div class="column-checkbox selected" data-column="0">Giriş Tarihi</div>
                            <div class="column-checkbox selected" data-column="1">Ad Soyad</div>
                            <div class="column-checkbox selected" data-column="2">TC No</div>
                            <div class="column-checkbox selected" data-column="3">Telefon</div>
                            <div class="column-checkbox selected" data-column="4">Plaka</div>
                            <div class="column-checkbox selected" data-column="5">Ziyaret Sebebi</div>
                            <div class="column-checkbox selected" data-column="6">Ziyaret Edilen Birim</div>
                            <div class="column-checkbox selected" data-column="7">Ziyaret Edilen</div>
                            <div class="column-checkbox selected" data-column="8">Ekleyen</div>
                        </div>
                        <button class="show-all-columns-btn" onclick="showAllColumns()">Tüm Sütunları Göster</button>
                    </div>
                                        
                    <h3 style="margin: 20px 0 16px 0; font-size: 16px; font-weight: 700; color: #334155; text-align: center;">Filtreleme</h3>
                    
                    <div class="filter-section">
                        <h4>Giriş Tarihi</h4>
                        <input type="date" class="filter-input" id="filter_start_date" placeholder="Başlangıç Tarihi">
                        <input type="date" class="filter-input" id="filter_end_date" placeholder="Bitiş Tarihi">
                    </div>

                    <div class="filter-section">
                        <h4>Ad Soyad</h4>
                        <input type="text" class="filter-input" id="filter_name" placeholder="Ad Soyad ara...">
                    </div>

                    <div class="filter-section">
                        <h4>TC Kimlik No</h4>
                        <input type="text" class="filter-input" id="filter_tc_no" placeholder="TC Kimlik No ara...">
                    </div>

                    <div class="filter-section">
                        <h4>Telefon</h4>
                        <input type="text" class="filter-input" id="filter_phone" placeholder="Telefon ara...">
                    </div>

                    <div class="filter-section">
                        <h4>Üniversite Birimi</h4>
                        <input type="text" class="filter-input" id="filter_unit" placeholder="Birim ara...">
                    </div>
                    <div class="filter-section">
                        <h4>Mevki/Unvan</h4>
                        <div class="custom-select-wrapper">
                            <input type="text" class="filter-input custom-select-input" id="filter_title" placeholder="Unvan ara veya seç..." autocomplete="off">
                            <div class="custom-select-arrow">▼</div>
                            <div class="custom-select-dropdown" id="titleDropdown">
                                <div class="custom-select-search">
                                    <input type="text" placeholder="Ara..." class="search-input" id="titleSearch">
                                </div>
                                <div class="custom-select-options" id="titleOptions">
                                    <div class="custom-select-option" data-value="Prof">Profesör</div>
                                    <div class="custom-select-option" data-value="Dr">Doktor</div>
                                    <div class="custom-select-option" data-value="Doç">Doçent</div>
                                    <div class="custom-select-option" data-value="Öğr.Gör">Öğretim Görevlisi</div>
                                    <div class="custom-select-option" data-value="Arş.Gör">Araştırma Görevlisi</div>
                                    <div class="custom-select-option" data-value="Teknisyen">Teknisyen</div>
                                    <div class="custom-select-option" data-value="Uzman">Uzman</div>
                                    <div class="custom-select-option" data-value="Memur">Memur</div>
                                    <div class="custom-select-option" data-value="Öğrenci">Öğrenci</div>
                                    <div class="custom-select-option" data-value="Diğer">Diğer</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h4>Plaka</h4>
                        <input type="text" class="filter-input" id="filter_plate" placeholder="Plaka ara...">
                    </div>

                    <div class="filter-section">
                        <h4>Ziyaret Sebebi</h4>
                        <div class="custom-select-wrapper">
                            <input type="text" class="filter-input custom-select-input" id="filter_purpose" placeholder="Sebep ara veya seç..." autocomplete="off">
                            <div class="custom-select-arrow">▼</div>
                            <div class="custom-select-dropdown" id="purposeDropdown">
                                <div class="custom-select-search">
                                    <input type="text" placeholder="Ara..." class="search-input" id="purposeSearch">
                                </div>
                                <div class="custom-select-options" id="purposeOptions">
                                    <div class="custom-select-option" data-value="iş">İş</div>
                                    <div class="custom-select-option" data-value="ziyaret">Ziyaret</div>
                                    <div class="custom-select-option" data-value="toplantı">Toplantı</div>
                                    <div class="custom-select-option" data-value="tez_danışmanlığı">Tez Danışmanlığı</div>
                                    <div class="custom-select-option" data-value="proje">Proje</div>
                                    <div class="custom-select-option" data-value="seminer">Seminer</div>
                                    <div class="custom-select-option" data-value="konferans">Konferans</div>
                                    <div class="custom-select-option" data-value="diğer">Diğer</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h4>Ziyaret Edilen Birim</h4>
                        <input type="text" class="filter-input" id="filter_department" placeholder="Birim ara...">
                    </div>

                    <div class="filter-section">
                        <h4>Ziyaret Edilen Kişi</h4>
                        <input type="text" class="filter-input" id="filter_person_to_visit" placeholder="Kişi ara...">
                    </div>

                    <div class="filter-section">
                        <h4>Ekleyen</h4>
                        <input type="text" class="filter-input" id="filter_approved_by" placeholder="Ekleyen kişi ara...">
                    </div>

                    <div class="filter-buttons">
                        <button type="button" class="filter-apply" id="applyFilters">Filtreyi Uygula</button>
                        <button type="button" class="filter-clear" id="clearFilters">Temizle</button>
                    </div>
                </div>

                <div class="table-section">
                    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
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
                                    <button id="backToMenuBtn" type="button" style="color: #dc3545; font-size: 0.9rem; padding: 0.1rem 0.1rem;">Geri</button>
                                    <label for="start_date">Başlangıç Tarihi:</label>
                                    <div class="date-input-wrapper">
                                        <input type="date" id="start_date" value="{{ request('start_date') }}">
                                    </div>
                                    <label for="end_date">Bitiş Tarihi:</label>
                                    <div class="date-input-wrapper">
                                        <input type="date" id="end_date" value="{{ request('end_date') }}">
                                    </div>
                                    <div style="display: flex; justify-content: space-between; gap: 10px; margin-top: 8px;">
                                        <button id="applyDateRange" class="modern-btn" style="flex: 1;">Uygula</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table id="visitorTable" class="data-table">
                        <thead>
                            <tr>
                                <th>Giriş Tarihi</th>
                                <th>Ad Soyad</th>
                                <th>TC Kimlik No</th>
                                <th>Telefon</th>
                                <th>Plaka</th>
                                <th>Ziyaret Sebebi</th>
                                <th>Ziyaret Edilen Birim</th>
                                <th>Ziyaret Edilen Kişi</th>
                                <th>Ekleyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visits as $visit)
                                <tr>
                                    <td data-label="Giriş Tarihi">{{ $visit->entry_time ? \Carbon\Carbon::parse($visit->entry_time)->format('d.m.Y H:i') : '-' }}</td>
                                    <td data-label="Ad Soyad">{{ $visit->visitor->name ?? '-' }}</td>
                                    <td data-label="TC Kimlik No">{{ $visit->visitor->tc_no ?? '-' }}</td>
                                    <td data-label="Telefon">{{ $visit->phone ?? '-' }}</td>
                                    <td data-label="Plaka">{{ $visit->plate ?? '-' }}</td>
                                    <td data-label="Ziyaret Sebebi">{{ $visit->purpose ?? '-' }}</td>
                                    <td data-label="Ziyaret Edilen Birim">{{ $visit->visitor->department->name ?? '-' }}</td>
                                    <td data-label="Ziyaret Edilen Kişi">{{ $visit->person_to_visit ?? '-' }}</td>
                                    <td data-label="Ekleyen">{{ $visit->approver->ad_soyad ?? $visit->approved_by ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="empty-cell">Kayıt bulunamadı</td>
                                </tr>
                            @endforelse
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
        </div>
    </div>

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
                        <span class="chip-count"><i class="bi bi-filter-square"></i> <b id="maskCount">6</b> / 6 seçili</span>
                    </div>
                </div>

                <div class="chip-grid" id="maskChipGrid">
                    <input id="mask_name" class="chip-check" type="checkbox" name="mask[]" value="name" checked>
                    <label for="mask_name" class="chip"><i class="bi bi-person"></i><span>Ad Soyad</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>
                    <input id="mask_tc" class="chip-check" type="checkbox" name="mask[]" value="tc_no" checked>
                    <label for="mask_tc" class="chip"><i class="bi bi-card-text"></i><span>T.C. No</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>
                    <input id="mask_phone" class="chip-check" type="checkbox" name="mask[]" value="phone" checked>
                    <label for="mask_phone" class="chip"><i class="bi bi-telephone"></i><span>Telefon</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>
                    <input id="mask_plate" class="chip-check" type="checkbox" name="mask[]" value="plate" checked>
                    <label for="mask_plate" class="chip"><i class="bi bi-car-front"></i><span>Plaka</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>
                    <input id="mask_department" class="chip-check" type="checkbox" name="mask[]" value="department" checked>
                    <label for="mask_department" class="chip"><i class="bi bi-building"></i><span>Ziyaret Edilen Birim</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>
                    <input id="mask_zed" class="chip-check" type="checkbox" name="mask[]" value="person_to_visit" checked>
                    <label for="mask_zed" class="chip"><i class="bi bi-person-check"></i><span>Ziyaret Edilen</span><span class="tick"><i class="bi bi-check-lg"></i></span></label>
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
        let table;

        document.addEventListener('DOMContentLoaded', () => {
            updatePageTitleFromURL();
            initHybridDropdowns();
            
            table = $('#visitorTable').DataTable({
                responsive: true,
                language: {
                    "decimal": "", "emptyTable": "Tabloda herhangi bir veri mevcut değil", "info": "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor", "infoEmpty": "Kayıt yok", "infoFiltered": "(_MAX_ kayıttan bulunan)", "infoPostFix": "", "thousands": ".", "lengthMenu": "_MENU_ kayıt göster", "loadingRecords": "Yükleniyor...", "processing": "İşleniyor...", "search": "Ara:", "zeroRecords": "Eşleşen kayıt bulunamadı",
                    "paginate": { "first": "İlk", "last": "Son", "next": "Sonraki", "previous": "Önceki" },
                    "aria": { "sortAscending": ": artan sıralamayı etkinleştir", "sortDescending": ": azalan sıralamayı etkinleştir" }
                },
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Tümü"]],
                order: [[0, 'desc']], info: true, searching: true, ordering: true, paging: true, stateSave: true, dom: 'lfrtip'
            });
            
            document.querySelectorAll('.column-checkbox').forEach(checkbox => {
                checkbox.addEventListener('click', function() {
                    const columnIndex = parseInt(this.dataset.column);
                    this.classList.toggle('selected');
                    
                    if (table) {
                        const isVisible = this.classList.contains('selected');
                        table.column(columnIndex).visible(isVisible);
                        if (hasActiveFilters()) {
                            applyCustomFilters();
                        }
                        table.columns.adjust().draw();
                    }
                    updateActiveColumnsInfo();
                });
            });
            document.querySelectorAll('.column-checkbox').forEach(checkbox => { checkbox.classList.add('selected'); });
            updateActiveColumnsInfo();
            document.getElementById('filter_start_date')?.addEventListener('change', applyCustomFilters);
            document.getElementById('filter_end_date')?.addEventListener('change', applyCustomFilters);
        });

        function updatePageTitleFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            const dateFilter = urlParams.get('date_filter');
            const startDate = urlParams.get('start_date');
            const endDate = urlParams.get('end_date');
            if (dateFilter) { updateActiveFilterText(dateFilter); }
            else if (startDate || endDate) { updateActiveFilterText('custom'); }
            else { updateActiveFilterText('daily'); }
        }

        function updateActiveFilterText(type) {
            const activeFilterText = document.getElementById('activeFilterText');
            if (!activeFilterText) return;
            let text = '';
            switch (type) {
                case 'all': text = 'Tüm Kayıtlar'; break;
                case 'daily': text = 'Günlük Kayıtlar'; break;
                case 'monthly': text = 'Aylık Kayıtlar'; break;
                case 'yearly': text = 'Yıllık Kayıtlar'; break;
                case 'custom': text = 'Özel Tarih Aralığı'; break;
                default: text = 'Günlük Kayıtlar';
            }
            activeFilterText.textContent = text;
        }
        
        function toggleColumnSection() {
            const section = document.getElementById('columnFilterSection');
            section.classList.toggle('expanded');
        }

        function initHybridDropdowns() {
            initHybridDropdown('filter_title', 'titleDropdown', 'titleSearch', 'titleOptions');
            initHybridDropdown('filter_purpose', 'purposeDropdown', 'purposeSearch', 'purposeOptions');
        }

        function initHybridDropdown(inputId, dropdownId, searchId, optionsId) {
            const wrapper = document.getElementById(inputId).closest('.custom-select-wrapper');
            const input = wrapper.querySelector('.custom-select-input');
            const dropdown = wrapper.querySelector('.custom-select-dropdown');
            const search = wrapper.querySelector('.search-input');
            const options = Array.from(wrapper.querySelectorAll('.custom-select-option'));
            const arrow = wrapper.querySelector('.custom-select-arrow');

            function toggleDropdown(e) {
                e.stopPropagation();
                const isVisible = dropdown.style.display === 'block';
                document.querySelectorAll('.custom-select-dropdown').forEach(d => {
                    d.style.display = 'none';
                    const parentWrapper = d.closest('.custom-select-wrapper');
                    if (parentWrapper) {
                        const parentArrow = parentWrapper.querySelector('.custom-select-arrow');
                        if (parentArrow) {
                            parentArrow.style.transform = 'translateY(-50%) rotate(0deg)';
                        }
                    }
                });
                if (!isVisible) {
                    dropdown.style.display = 'block';
                    arrow.style.transform = 'translateY(-50%) rotate(180deg)';
                    search.value = '';
                    filterHybridOptions('');
                    setTimeout(() => search.focus(), 50);
                }
            }
            input.addEventListener('click', toggleDropdown);
            arrow.addEventListener('click', toggleDropdown);
            wrapper.addEventListener('click', e => {
                if (e.target.matches('.custom-select-wrapper')) { toggleDropdown(e); }
            });
            search.addEventListener('input', e => { filterHybridOptions(e.target.value); });
            function filterHybridOptions(term) {
                const t = term.toLowerCase();
                options.forEach(o => {
                    const txt = o.textContent.toLowerCase();
                    o.style.display = txt.includes(t) ? 'block' : 'none';
                });
            }
            options.forEach(o => {
                o.addEventListener('click', () => {
                    const text = o.textContent;
                    input.value = text;
                    dropdown.style.display = 'none';
                    arrow.style.transform = 'translateY(-50%) rotate(0deg)';
                    applyCustomFilters();
                });
            });
            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    dropdown.style.display = 'none';
                    arrow.style.transform = 'translateY(-50%) rotate(0deg)';
                }
            });
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    dropdown.style.display = 'none';
                    applyCustomFilters();
                }
            });
        }

        function hasActiveFilters() {
            const filterInputs = document.querySelectorAll('.filter-input, .custom-select-input');
            for (let input of filterInputs) {
                if (input.value && input.value.trim() !== '') { return true; }
            }
            return false;
        }

        function showAllColumns() {
            if (!table) return;
            table.columns().every(function() { this.visible(true); });
            table.columns.adjust().draw();
            document.querySelectorAll('.column-checkbox').forEach(cb => { cb.classList.add('selected'); });
            updateActiveColumnsInfo();
            if (hasActiveFilters()) { applyCustomFilters(); }
        }

        function updateActiveColumnsInfo() {
            if (!table) return;
            const visibleColumns = [];
            table.columns().every(function(index) {
                if (this.visible()) {
                    const columnName = getColumnName(index);
                    if (columnName) { visibleColumns.push(columnName); }
                }
            });
            const activeFilterText = document.getElementById('activeFilterText');
            if (activeFilterText) {
                if (visibleColumns.length < 9) { activeFilterText.textContent = `Gösterilen Sütunlar: ${visibleColumns.join(', ')}`; }
                else { updatePageTitleFromURL(); }
            }
        }

        function getColumnName(index) {
            const columnNames = { 0: 'Giriş Tarihi', 1: 'Ad Soyad', 2: 'TC No', 3: 'Telefon', 4: 'Plaka', 5: 'Ziyaret Sebebi', 6: 'Ziyaret Edilen Birim', 7: 'Ziyaret Edilen', 8: 'Ekleyen' };
            return columnNames[index] || '';
        }
        
        function applyCustomFilters() {
            if (!table) return;
            table.rows().every(function() { this.node().style.display = ''; });
            const nameFilter = document.getElementById('filter_name')?.value.toLowerCase().trim();
            const tcFilter = document.getElementById('filter_tc_no')?.value.toLowerCase().trim();
            const phoneFilter = document.getElementById('filter_phone')?.value.toLowerCase().trim();
            const plateFilter = document.getElementById('filter_plate')?.value.toLowerCase().trim();
            const purposeFilter = document.getElementById('filter_purpose')?.value.toLowerCase().trim();
            const departmentFilter = document.getElementById('filter_department')?.value.toLowerCase().trim();
            const personFilter = document.getElementById('filter_person_to_visit')?.value.toLowerCase().trim();
            const unitFilter = document.getElementById('filter_unit')?.value.toLowerCase().trim();
            const titleFilter = document.getElementById('filter_title')?.value.toLowerCase().trim();
            const approvedByFilter = document.getElementById('filter_approved_by')?.value.toLowerCase().trim();
            const startDate = document.getElementById('filter_start_date')?.value;
            const endDate = document.getElementById('filter_end_date')?.value;
            const visibleColumns = [];
            table.columns().every(function(index) { if (this.visible()) { visibleColumns.push(index); } });
            const columnMap = { 'entry_time': visibleColumns.indexOf(0) >= 0 ? visibleColumns.indexOf(0) : -1, 'name': visibleColumns.indexOf(1) >= 0 ? visibleColumns.indexOf(1) : -1, 'tc_no': visibleColumns.indexOf(2) >= 0 ? visibleColumns.indexOf(2) : -1, 'phone': visibleColumns.indexOf(3) >= 0 ? visibleColumns.indexOf(3) : -1, 'plate': visibleColumns.indexOf(4) >= 0 ? visibleColumns.indexOf(4) : -1, 'purpose': visibleColumns.indexOf(5) >= 0 ? visibleColumns.indexOf(5) : -1, 'department': visibleColumns.indexOf(6) >= 0 ? visibleColumns.indexOf(6) : -1, 'person_to_visit': visibleColumns.indexOf(7) >= 0 ? visibleColumns.indexOf(7) : -1, 'approved_by': visibleColumns.indexOf(8) >= 0 ? visibleColumns.indexOf(8) : -1 };
            let hiddenRows = 0;
            table.rows().every(function() {
                const row = this.node();
                const cells = row.cells;
                let shouldHide = false;
                if (nameFilter && columnMap.name >= 0 && cells[columnMap.name] && !cells[columnMap.name].textContent.toLowerCase().includes(nameFilter)) { shouldHide = true; }
                if (tcFilter && columnMap.tc_no >= 0 && cells[columnMap.tc_no] && !cells[columnMap.tc_no].textContent.toLowerCase().includes(tcFilter)) { shouldHide = true; }
                if (phoneFilter && columnMap.phone >= 0 && cells[columnMap.phone] && !cells[columnMap.phone].textContent.toLowerCase().includes(phoneFilter)) { shouldHide = true; }
                if (plateFilter && columnMap.plate >= 0 && cells[columnMap.plate] && !cells[columnMap.plate].textContent.toLowerCase().includes(plateFilter)) { shouldHide = true; }
                if (purposeFilter && columnMap.purpose >= 0 && cells[columnMap.purpose]) {
                    const cellContent = cells[columnMap.purpose].textContent.toLowerCase();
                    if (!cellContent.includes(purposeFilter)) { shouldHide = true; }
                }
                if (departmentFilter && columnMap.department >= 0 && cells[columnMap.department] && !cells[columnMap.department].textContent.toLowerCase().includes(departmentFilter)) { shouldHide = true; }
                if (personFilter && columnMap.person_to_visit >= 0 && cells[columnMap.person_to_visit] && !cells[columnMap.person_to_visit].textContent.toLowerCase().includes(personFilter)) { shouldHide = true; }
                if (unitFilter && columnMap.unit >= 0 && cells[columnMap.unit] && !cells[columnMap.unit].textContent.toLowerCase().includes(unitFilter)) { shouldHide = true; }
                if (titleFilter && columnMap.person_to_visit >= 0 && cells[columnMap.person_to_visit]) {
                    const personToVisit = cells[columnMap.person_to_visit].textContent.toLowerCase();
                    if (personToVisit && personToVisit !== '-') { if (!personToVisit.includes(titleFilter)) { shouldHide = true; } }
                }
                if (approvedByFilter && columnMap.approved_by >= 0 && cells[columnMap.approved_by] && !cells[columnMap.approved_by].textContent.toLowerCase().includes(approvedByFilter)) { shouldHide = true; }
                
                // Tarih filtreleme mantığı
                if ((startDate || endDate) && columnMap.entry_time >= 0) {
                    const entryDateCell = cells[columnMap.entry_time];
                    if (entryDateCell && entryDateCell.textContent.trim() !== '-') {
                        const cellDateStr = entryDateCell.textContent.trim().split(' ')[0]; // Sadece tarihi al
                        const [day, month, year] = cellDateStr.split('.');
                        const entryDate = new Date(`${year}-${month}-${day}`);
                        const filterStartDate = startDate ? new Date(startDate) : null;
                        const filterEndDate = endDate ? new Date(endDate) : null;

                        if (filterStartDate && entryDate < filterStartDate) {
                            shouldHide = true;
                        }
                        if (filterEndDate && entryDate > filterEndDate) {
                            shouldHide = true;
                        }
                    }
                }

                if (shouldHide) { row.style.display = 'none'; hiddenRows++; }
            });
            const totalRows = table.rows().count();
            const visibleRows = totalRows - hiddenRows;
            if (table.info) { table.info(`${visibleRows} kayıttan ${visibleRows} tanesi gösteriliyor`); }
            updateActiveFilterInfo();
        }

        function updateActiveFilterInfo() {
            const activeFilters = [];
            if (document.getElementById('filter_name')?.value) activeFilters.push('Ad Soyad');
            if (document.getElementById('filter_tc_no')?.value) activeFilters.push('TC Kimlik No');
            if (document.getElementById('filter_phone')?.value) activeFilters.push('Telefon');
            if (document.getElementById('filter_plate')?.value) activeFilters.push('Plaka');
            if (document.getElementById('filter_purpose')?.value) activeFilters.push('Ziyaret Sebebi');
            if (document.getElementById('filter_department')?.value) activeFilters.push('Ziyaret Edilen Birim');
            if (document.getElementById('filter_person_to_visit')?.value) activeFilters.push('Ziyaret Edilen Kişi');
            if (document.getElementById('filter_unit')?.value) activeFilters.push('Üniversite Birimi');
            if (document.getElementById('filter_title')?.value) activeFilters.push('Mevki/Unvan');
            if (document.getElementById('filter_approved_by')?.value) activeFilters.push('Ekleyen');
            if (document.getElementById('filter_start_date')?.value || document.getElementById('filter_end_date')?.value) { activeFilters.push('Tarih Aralığı'); }
            const filterText = activeFilters.length > 0 ? `Aktif Filtreler: ${activeFilters.join(', ')}` : 'Günlük Kayıtlar';
            document.getElementById('activeFilterText').textContent = filterText;
        }

        const applyFiltersBtn = document.getElementById('applyFilters');
        const clearFiltersBtn = document.getElementById('clearFilters');
        applyFiltersBtn?.addEventListener('click', applyCustomFilters);
        clearFiltersBtn?.addEventListener('click', () => {
            const filterInputs = document.querySelectorAll('.filter-input, .custom-select-input');
            filterInputs.forEach(input => { input.value = ''; });
            if (table) { 
                table.rows().every(function() { this.node().style.display = ''; }); 
                // Sütun seçimlerini de temizle
                table.columns().every(function() { this.visible(true); });
                document.querySelectorAll('.column-checkbox').forEach(cb => { cb.classList.add('selected'); });
                table.columns.adjust().draw();
            }
            updateActiveFilterInfo();
        });

        function addEnterKeyListener(elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                element.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyCustomFilters();
                    }
                });
            }
        }
        addEnterKeyListener('filter_name'); addEnterKeyListener('filter_tc_no'); addEnterKeyListener('filter_phone'); addEnterKeyListener('filter_plate'); addEnterKeyListener('filter_department'); addEnterKeyListener('filter_person_to_visit'); addEnterKeyListener('filter_unit'); addEnterKeyListener('filter_approved_by');
        
        updateActiveFilterInfo();

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
        const backToMenuBtn = document.getElementById('backToMenuBtn');
        const maskModal = document.getElementById('maskModal');
        const maskCloseBtn = document.getElementById('maskCloseBtn');
        const maskCancelBtn = document.getElementById('maskCancelBtn');
        const maskSelectAll = document.getElementById('maskSelectAll');
        const maskSelectNone = document.getElementById('maskSelectNone');
        const confirmGenerateReport = document.getElementById('confirmGenerateReport');
        const maskChipGrid = document.getElementById('maskChipGrid');
        const maskCountEl = document.getElementById('maskCount');

        function openMaskModal() { maskModal?.classList.add('open'); }
        function closeMaskModal() { maskModal?.classList.remove('open'); }
        function getMaskInputsNodeList() { return maskChipGrid ? maskChipGrid.querySelectorAll('.chip-check') : []; }
        function updateMaskCount() { if (!maskCountEl) return; const list = getMaskInputsNodeList(); const selected = [...list].filter(i => i.checked).length; maskCountEl.textContent = selected; }
        function getMaskParamsFromModal() { const params = new URLSearchParams(); const list = getMaskInputsNodeList(); list.forEach(inp => { if (inp.checked) params.append('mask[]', inp.value); }); return params; }
        function hydrateMaskFromUrl() { const url = new URL(window.location.href); const list = getMaskInputsNodeList(); if (!list.length) return; const selected = new Set(url.searchParams.getAll('mask[]')); list.forEach(inp => { inp.checked = selected.size ? selected.has(inp.value) : inp.checked; }); updateMaskCount(); }

        maskCloseBtn?.addEventListener('click', closeMaskModal);
        maskCancelBtn?.addEventListener('click', closeMaskModal);
        maskSelectAll?.addEventListener('click', () => { getMaskInputsNodeList().forEach(i => i.checked = true); updateMaskCount(); });
        maskSelectNone?.addEventListener('click', () => { getMaskInputsNodeList().forEach(i => i.checked = false); updateMaskCount(); });
        maskChipGrid?.addEventListener('change', updateMaskCount);

        reportBtn?.addEventListener('click', () => reportMenu?.classList.toggle('active'));
        dateRangeOption?.addEventListener('click', (e) => {
            e.stopPropagation();
            document.querySelectorAll('#reportMenu li').forEach(li => { li.style.display = 'none'; });
            dateRangeInputs.style.display = 'block';
            dateRangeOption.style.display = 'block';
        });

        backToMenuBtn?.addEventListener('click', () => {
            document.querySelectorAll('#reportMenu li').forEach(li => { li.style.display = 'block'; });
            dateRangeInputs.style.display = 'none';
        });

        applyDateRangeBtn?.addEventListener('click', () => {
            const startDate = document.getElementById('start_date')?.value;
            const endDate = document.getElementById('end_date')?.value;
            const params = new URLSearchParams(window.location.search);
            params.delete('date_filter');
            if (startDate) params.set('start_date', startDate);
            if (endDate) params.set('end_date', endDate); else params.delete('end_date');
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
                updateActiveFilterText(type);
                window.location.href = window.location.pathname + '?' + urlParams.toString();
            });
        });
        refreshBtn?.addEventListener('click', () => { window.location.href = window.location.pathname + '?date_filter=daily'; });
        generateReportBtn?.addEventListener('click', () => { hydrateMaskFromUrl(); openMaskModal(); });
        confirmGenerateReport?.addEventListener('click', () => {
            const reportParams = getCommonExportParams(true);
            const maskParams = getMaskParamsFromModal();
            for (const [k, v] of maskParams.entries()) reportParams.append(k, v);
            if (![...maskParams.keys()].length) { if (!confirm('Hiçbir alan maskelenmeyecek. Devam etmek istiyor musun?')) return; }
            window.location.href = `/admin/generate-report?` + reportParams.toString();
        });
        exportUnmaskedExcelBtn?.addEventListener('click', () => {
            const exportParams = getCommonExportParams(false);
            exportParams.set('unmasked', 'true');
            window.location.href = `/report/export?` + exportParams.toString();
        });
        exportUnmaskedPdfBtn?.addEventListener('click', () => {
            const pdfParams = getCommonExportParams(false);
            window.location.href = `/admin/export-pdf-unmasked?` + pdfParams.toString();
        });

        function printTableLikeReport(opts = {}) {
            const table = document.querySelector('table');
            if (!table) return alert('Yazdırılacak tablo bulunamadı.');
            const { titleText = 'Ziyaretçi Raporu', rangeSelector = null, dateLocale = 'tr-TR' } = opts;
            const todayStr = new Date().toLocaleDateString(dateLocale, { day: '2-digit', month: '2-digit', year: 'numeric' });
            let finalTitle = titleText;
            if (rangeSelector) {
                const r = document.querySelector(rangeSelector)?.innerText.trim();
                if (r) finalTitle += ' ' + r;
            }
            const html = `
            <html>
                <head>
                    <meta charset="utf-8" />
                    <title>Yazdır - ${finalTitle}</title>
                    <style>
                        @page { size: A4 landscape; margin: 1cm; }
                        body { font-family: Arial, sans-serif; font-size: 11px; margin:0; }
                        h2 { text-align: center; font-size: 14px; margin: 6px 0; color:#003366; }
                        .date { text-align:right; font-size:10px; margin:4px 0; }
                        table { width:100%; border-collapse: collapse; }
                        thead th { background:#003366; color:#fff; padding:4px; border:1px solid #ccc; font-size:10px; }
                        tbody td { padding:3px; border:1px solid #ccc; font-size:9px; }
                        tr { page-break-inside: avoid; }
                    </style>
                </head>
                <body>
                    <div class="date">${todayStr}</div>
                    <h2>${finalTitle}</h2>
                    ${table.outerHTML}
                </body>
            </html>`;
            const w = window.open('', '_blank');
            w.document.write(html);
            w.document.close();
            w.print();
            w.close();
        }

        document.getElementById('printUnmaskedBtn')?.addEventListener('click', () => { printTableLikeReport({ titleText: 'Ziyaretçi Listesi', compact: true }); });

        function getCommonExportParams(isReportPage = false) {
            const urlParams = new URLSearchParams(window.location.search);
            const exportParams = new URLSearchParams();
            const allFields = ['entry_time', 'name', 'tc_no', 'phone', 'plate', 'purpose', 'department', 'person_to_visit', 'approved_by'];

            // Tarih filtrelerini URL'den al ve exportParams'e ekle
            const startDateParam = urlParams.get('start_date');
            const endDateParam = urlParams.get('end_date');
            if (startDateParam) {
                exportParams.set('start_date', startDateParam);
            }
            if (endDateParam) {
                exportParams.set('end_date', endDateParam);
            }
            
            const dateFilterParam = urlParams.get('date_filter');
            if (dateFilterParam) {
                exportParams.set('date_filter', dateFilterParam);
            }

            const filterParam = urlParams.get('filter');
            let selectedFields = filterParam ? filterParam.split(',') : allFields;

            

            selectedFields.forEach(field => {
                exportParams.append('fields[]', field);
                const value = urlParams.get(field + '_value');
                if (value) exportParams.set(field + '_value', value);
            });
            
            exportParams.set('sort_order', 'desc');
            return exportParams;
        }

        document.addEventListener('click', (e) => {
            if (!reportBtn?.contains(e.target) && !reportMenu?.contains(e.target)) {
                reportMenu?.classList.remove('active');
            }
        });
    </script>
</x-app-layout>