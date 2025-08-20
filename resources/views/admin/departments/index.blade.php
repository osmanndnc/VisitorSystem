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
        .toggle-switch { width: 80px; height: 36px; background: #f1f5f9; border-radius: 999px; border: 2px solid #e2e8f0; cursor: pointer; position: relative; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1); outline: none; margin: 0 auto; }
        .toggle-switch:hover { transform: scale(1.05); box-shadow: inset 0 2px 4px rgba(0,0,0,0.15); }
        .toggle-switch.active { background: linear-gradient(135deg, #22c55e, #16a34a); border-color: #16a34a; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 4px 12px rgba(34, 197, 94, 0.4); }
        .toggle-switch:not(.active) { background: linear-gradient(135deg, #f87171, #ef4444); border-color: #ef4444; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 4px 12px rgba(239, 68, 68, 0.4); }
        .toggle-switch .circle { position: absolute; top: 2px; left: 2px; width: 28px; height: 28px; background: #ffffff; border-radius: 50%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
        .toggle-switch.active .circle { left: 48px; box-shadow: 0 2px 8px rgba(0,0,0,0.25); }
        .toggle-switch .label { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-size: 11px; color: #ffffff; font-weight: 700; pointer-events: none; z-index: 1; text-shadow: 0 1px 2px rgba(0,0,0,0.5); letter-spacing: 0.5px; }
        .toggle-switch.readonly { cursor: not-allowed; opacity: 0.8; }
        .toggle-switch.readonly:hover { transform: none; }
        .btn-edit { background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); color: #0369a1; padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; border: 1px solid #0ea5e9; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); text-decoration: none; display: inline-block; box-shadow: 0 4px 15px rgba(14, 165, 233, 0.2); position: relative; overflow: hidden; }
        .btn-edit::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); transition: left 0.5s ease; }
        .btn-edit:hover::before { left: 100%; }
        .btn-edit:hover { background: linear-gradient(135deg, #bae6fd 0%, #7dd3fc 100%); transform: translateY(-3px); box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4); }
        .btn-edit:active { transform: translateY(-1px); }
        .btn-style905 { background:#1d4ed8; border:none; color:#fff; padding:12px 24px; font-size:16px; font-weight:600; border-radius:16px; cursor:pointer; transition:.3s; box-shadow:0 6px 12px rgba(0,0,0,.15); }
        .btn-style905:hover { background:#1746c1; transform:translateY(-2px) }
        .toggle-btn { width: 280px; padding: 16px 24px; border-radius: 20px; font-weight: 700; font-size: 15px; text-align: center; transition: all 0.4s; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; display: block; margin: 0 auto; }
        .toggle-btn.aktif { background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%); color: white; box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4); }
        .toggle-btn.aktif:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 15px 35px rgba(239, 68, 68, 0.5); }
        .toggle-btn.pasif { background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%); color: white; box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4); }
        .toggle-btn.pasif:hover { transform: translateY(-3px) scale(1.02); box-shadow:0 15px 35px rgba(16, 185, 129,0.5); }
        .toggle-btn:active { transform: translateY(-1px) scale(0.98); }
        input[type="text"], input[type="email"], input[type="password"], select { background:#fff; border:1px solid #d1d5db; padding:10px 12px; font-size:14px; border-radius:8px; box-shadow: inset 0 1px 3px rgba(0,0,0,.05); transition: border-color .3s ease; }
        input:focus, select:focus { border-color:#2563eb; outline:none; box-shadow:0 0 0 2px rgba(37,99,235,.2); }
        .submit-animated { width: 200px; height: 50px; border-radius: 50px; background: linear-gradient(135deg,#1d4ed8 0%,#2563eb 100%); border: none; position: relative; overflow: hidden; font-size: 16px; font-weight: 600; color: #fff; cursor: pointer; transition: all .3s ease; box-shadow: 0 8px 20px rgba(0,80,160,.2); }
        .submit-animated:hover { background:#1746c1; }
        .submit-animated img { position:absolute; width:26px; height:26px; top:50%; left:50%; transform:translate(-50%,-50%); opacity:0; }
        .submit-animated:focus { animation: extend 1s ease-in-out forwards; }
        .submit-animated:focus span { animation: disappear 1s ease-in-out forwards; }
        @keyframes extend { 0% { width: 200px; height: 50px; border-radius: 50px; } 50% { background: #22c55e; } 100% { width: 60px; height: 60px; border-radius: 50%; background: #22c55e; } }
        @keyframes disappear { 0% {opacity:1;} 100% {opacity:0;} }
        @keyframes appear { 0% {opacity:0;} 100% {opacity:1;} }
        .user-form-card { width: 950px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); max-height: 90vh; overflow-y: auto; margin: 0 auto; }
        .form-header { padding: 20px; text-align: center; border-bottom: 1px solid #e5e7eb; position: relative; }
        .form-title { font-size: 24px; font-weight: 700; color: #1f2937; }
        .form-body { padding: 20px; }
        .form-footer { padding: 20px; text-align: right; border-top: 1px solid #e5e7eb; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-form').forEach(form => {
                form.querySelector('.toggle-switch').addEventListener('click', function (e) {
                    e.preventDefault(); 
                    form.submit();
                });
            });
        });
    </script>
    
    <div class="py-6 max-w-7xl mx-auto">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Birimler Yönetimi</h2>
        </div>

        <div class="text-right mb-6">
            <a href="{{ route('admin.departments.create') }}" class="btn-style905" type="button">
                + Yeni Birim Ekle
            </a>
        </div>

        <div class="card overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="min-w-full table">
                    <thead>
                        <tr>
                            <th class="id-column">ID</th>
                            <th class="name-column">Birim Adı</th>
                            <th class="actions-column">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                            <tr class="border-t border-white/30 {{ !$department->is_active ? 'passive-row' : '' }}">
                                <td>{{ $department->id }}</td>
                                <td>{{ $department->name }}</td>
                                <td>
                                    <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn-edit" type="button">
                                        Düzenle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white dark:bg-gray-800">
                                <td colspan="4" class="py-4 px-6 text-center text-gray-500">Henüz bir birim eklenmemiş.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>