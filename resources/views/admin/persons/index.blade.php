<x-app-layout>
    <style>
        html { zoom: 80% }
        body { background: linear-gradient(135deg, #f5f7fa, #e4ebf1); font-family:'Segoe UI', sans-serif }
        .backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 40; opacity: 0; transition: opacity 0.3s ease; pointer-events: none; }
        .backdrop.show { opacity: 1; pointer-events: auto; }
        .modal-close { position: absolute; top: 20px; right: 20px; width: 36px; height: 36px; background: rgba(255,255,255,0.3); border: none; border-radius: 50%; font-size: 20px; cursor: pointer; transition: all 0.2s; backdrop-filter: blur(6px); color: #4b5563; }
        .modal-close:hover { background: rgba(255,255,255,0.5); transform: rotate(90deg); }
        .card, .user-detail-card, .user-form-card { border-radius: 20px; background: rgba(255, 255, 255, 0.87); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        .table thead th { background: rgba(226, 232, 240, 0.9); color: #1e293b; font-weight: 600; font-size: 16px; padding: 20px 16px; border-bottom: 2px solid rgba(203, 213, 225, 0.8); text-align: center; }
        .table tbody td { text-align: center; padding: 16px 16px; font-size: 16px; color: #475569; vertical-align: middle; }
        .table tbody tr { border-bottom: 1px solid #e2e8f0; transition: all 0.2s ease; }
        .table tbody tr:nth-child(even) { background: #fafbfc; }
        .table tbody tr.passive-row { background: #f9fafb; opacity: 0.7; }
        .id-column { width: 80px; }
        .name-column { width: 200px; }
        .username-column { width: 150px; }
        .phone-column { width: 150px; }
        .status-column { width: 120px; }
        .detail-column { width: 100px; }
        .actions-column { width: 130px; }
        .status-column { text-align: center; vertical-align: middle; }
        .toggle-container { display: flex; justify-content: center; align-items: center; width: 100%; }
        .toggle-switch { width: 80px; height: 36px; border-radius: 999px; cursor: pointer; position: relative; transition: all 0.4s; margin: 0 auto; border: 2px solid transparent; }
        .toggle-switch.active { background: linear-gradient(135deg, #22c55e, #16a34a); border-color: #16a34a; }
        .toggle-switch:not(.active) { background: linear-gradient(135deg, #f87171, #ef4444); border-color: #ef4444; }
        .toggle-switch .circle { position: absolute; top: 2px; left: 2px; width: 28px; height: 28px; background: #fff; border-radius: 50%; transition: all 0.3s; }
        .toggle-switch.active .circle { left: 48px; }
        .toggle-switch .label { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-size: 11px; color: #fff; font-weight: 700; pointer-events: none; }
        .btn-edit { background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0369a1; padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; border: 1px solid #0ea5e9; cursor: pointer; text-decoration: none; display: inline-block; transition: all .3s; }
        .btn-edit:hover { background: linear-gradient(135deg, #bae6fd, #7dd3fc); transform: translateY(-3px); }
        .btn-style905 { background:#1d4ed8; border:none; color:#fff; padding:12px 24px; font-size:16px; font-weight:600; border-radius:16px; cursor:pointer; transition:.3s; }
        .btn-style905:hover { background:#1746c1; transform:translateY(-2px) }
        input[type="text"], input[type="email"], input[type="password"], select { background:#fff; border:1px solid #d1d5db; padding:10px 12px; font-size:14px; border-radius:8px; transition: border-color .3s; }
        input:focus, select:focus { border-color:#2563eb; outline:none; box-shadow:0 0 0 2px rgba(37,99,235,.2); }
        .highlight { background-color: yellow; font-weight: bold; }
        [x-cloak] { display: none !important; }
        .search-container { position: relative; width: 33%; margin: 1rem auto; }
        .search-container input { width: 100%; padding-left: 2.5rem; padding-right: 2rem; }
        .search-container .search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
        .search-container .clear-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #9ca3af; font-weight: bold; }
        .search-container .clear-btn:hover { color: #ef4444; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-form .toggle-switch').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    this.closest('form').submit();
                });
            });

            // Dinamik arama
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearch');
            const tableRows = document.querySelectorAll('tbody tr');

            function highlightText(text, search) {
                if (!search) return text;
                const regex = new RegExp(`(${search})`, 'gi');
                return text.replace(regex, '<span class="highlight">$1</span>');
            }

            searchInput.addEventListener('keyup', function () {
                const searchValue = this.value.toLowerCase();

                tableRows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    let rowText = "";
                    let matchFound = false;

                    cells.forEach(cell => {
                        const originalText = cell.getAttribute('data-original') || cell.textContent;
                        cell.setAttribute('data-original', originalText);

                        if (!cell.querySelector('a') && !cell.querySelector('form')) {
                            if (originalText.toLowerCase().includes(searchValue) && searchValue !== "") {
                                matchFound = true;
                                cell.innerHTML = highlightText(originalText, searchValue);
                            } else {
                                cell.innerHTML = originalText;
                            }
                        }

                        rowText += originalText.toLowerCase();
                    });

                    row.style.display = rowText.includes(searchValue) || searchValue === "" ? "" : "none";
                });
            });

        
            clearBtn.addEventListener('click', function () {
                location.reload(); 
            });
        });
    </script>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Kişiler Yönetimi</h2>
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="Ara...">
                <span id="clearSearch" class="clear-btn">✖</span>
            </div>
        </div>

        <div class="text-right mb-6">
            <a href="{{ route('admin.persons.create') }}" class="btn-style905">+ Yeni Kişi Ekle</a>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table">
                    <thead>
                        <tr>
                            <th class="id-column">ID</th>
                            <th class="name-column">Ad Soyad</th>
                            <th class="username-column">Telefon</th>
                            <th class="actions-column">Birim(ler)</th>
                            <th class="status-column">Durum</th>
                            <th class="actions-column">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($persons as $person)
                            <tr class="{{ !$person->is_active ? 'passive-row' : '' }}">
                                <td>{{ $person->id }}</td>
                                <td>{{ $person->name }}</td>
                                <td>{{ $person->phone_number ?? '-' }}</td>
                                <td class="text-left">
                                    <div x-data="{ open: false }" x-cloak>
                                        @if ($person->departments->count() > 0)
                                            <div class="flex items-center">
                                                <span class="text-gray-700 text-xs font-medium mr-1 px-2.5 py-0.5 rounded-full">{{ $person->departments->first()->name }}</span>
                                                @if ($person->departments->count() > 1)
                                                    <button @click="open = !open" class="text-blue-500 hover:text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-full bg-gray-200 ml-1">
                                                        <span x-show="!open">+</span>
                                                        <span x-show="open">-</span>
                                                    </button>
                                                @endif
                                            </div>
                                            <div x-show="open" @click.away="open = false" class="flex flex-wrap items-center mt-1">
                                                @foreach ($person->departments->slice(1) as $department)
                                                    <span class="text-gray-700 text-xs font-medium mr-1 mb-1 px-2.5 py-0.5 rounded-full">{{ $department->name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <form class="toggle-form" action="{{ route('admin.persons.update', $person->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_active" value="{{ $person->is_active ? 0 : 1 }}">
                                        <button type="submit" class="toggle-switch {{ $person->is_active ? 'active' : '' }}">
                                            <div class="circle"></div>
                                            <span class="label">{{ $person->is_active ? 'Aktif' : 'Pasif' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('admin.persons.edit', $person->id) }}" class="btn-edit">Düzenle</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-500">Henüz bir kişi eklenmemiş.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
