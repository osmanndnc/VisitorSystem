<x-app-layout>
    <style>
        html { zoom: 80% }
        body { background: linear-gradient(135deg, #f5f7fa, #e4ebf1); font-family:'Segoe UI', sans-serif }
        .backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 40; opacity: 0; transition: opacity 0.3s ease; pointer-events: none; }
        .backdrop.show { opacity: 1; pointer-events: auto; }
        .modal-close { position: absolute; top: 20px; right: 20px; width: 36px; height: 36px; background: rgba(255,255,255,0.3); border: none; border-radius: 50%; font-size: 20px; cursor: pointer; transition: all 0.2s; backdrop-filter: blur(6px); color: #4b5563; }
        .modal-close:hover { background: rgba(255,255,255,0.5); transform: rotate(90deg); }
        .card, .user-detail-card, .user-form-card { border-radius: 20px; background: rgba(255, 255, 255, 0.87); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        .btn-style905 { background:#1d4ed8; border:none; color:#fff; padding:12px 24px; font-size:16px; font-weight:600; border-radius:16px; cursor:pointer; transition:.3s; box-shadow:0 6px 12px rgba(0,0,0,.15); }
        .btn-style905:hover { background:#1746c1; transform:translateY(-2px) }
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
            const submitBtn = document.getElementById('submit-button');
            if (submitBtn) {
                const form = submitBtn.closest('form');
                submitBtn.addEventListener('click', function (e) {
                    if (form.checkValidity()) {
                        e.preventDefault(); 
                        submitBtn.blur();
                        requestAnimationFrame(() => {
                            submitBtn.focus();
                            setTimeout(() => form.submit(), 1000);
                        });
                    } else {
                        form.reportValidity();
                    }
                });
            }
        });
    </script>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="user-form-card">
            <div class="form-header">
                <a href="{{ route('admin.persons.index') }}" class="modal-close">&times;</a>
                <div class="form-title">Kişi Düzenle: {{ $person->name }}</div>
            </div>
            <div class="form-body">
                <form id="person-edit-form" method="POST" action="{{ route('admin.persons.update', $person->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ad Soyad</label>
                        <input type="text" name="name" value="{{ old('name', $person->name) }}" required class="w-full">
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefon Numarası</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $person->phone_number) }}" class="w-full">
                        <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Birim(ler) Seç</label>
                        <select name="departments[]" id="departments" multiple required class="w-full h-40">
                            @php
                                $selectedDepartments = old('departments', $person->departments->pluck('id')->toArray());
                            @endphp
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ in_array($department->id, $selectedDepartments) ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Ctrl veya Cmd tuşuna basılı tutarak birden fazla birim seçebilirsiniz.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('departments')" />
                    </div>
                    <div class="flex justify-end form-footer">
                         <button type="submit" form="person-edit-form" class="submit-animated" id="submit-button">
                            <span>Güncelle</span>
                            <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="✓">
                         </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>