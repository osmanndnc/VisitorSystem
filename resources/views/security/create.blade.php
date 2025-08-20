<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="csrf-token" content="{{ csrf_token() }}">

@if (session('success'))
  <div class="mb-4 text-green-600">
      {{ session('success') }}
  </div>
@endif

@php
    // View içinde tekrar eden hesaplamalar tek yerde tutuluyor.
    $isEdit     = isset($editVisit);
    $formAction = $isEdit ? route('security.update', $editVisit->id) : route('security.store'); // web.php {id} kullanıyor
    $cancelUrl  = route('security.create');
@endphp

<x-app-layout>
    <style>
        /* ====== 1) SAYFA GENEL ====== */
        html { zoom: 80% }
        body{
            margin:0; padding:0;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(180deg,#f7f9fc 0%, #f0f4f9 55%, #edf1f6 100%);
            background-attachment: fixed; background-size: cover;
        }

        /* ====== 2) MODAL ALT YAPI ====== */
        .backdrop{
            position: fixed; inset: 0;
            background: rgba(0,0,0,.45);
            -webkit-backdrop-filter: blur(2px);
            backdrop-filter: blur(2px);
            z-index: 60;
            opacity: 0; pointer-events: none; transition: opacity .25s ease;
        }
        .backdrop.show{ opacity: 1; pointer-events: auto; }

        .modal{
            position: fixed; inset: 0; display: grid; place-items: center;
            z-index: 70; opacity: 0; pointer-events: none;
            transition: opacity .25s ease, transform .25s ease;
        }
        .modal.show{ opacity: 1; pointer-events: auto; }

        .modal-card{
            width: min(960px, 92vw);
            max-height: 90vh; overflow-y: auto;
            background: #fff; border-radius: 18px;
            border: 1px solid rgba(0,0,0,.06);
            box-shadow: 0 22px 60px rgba(0,0,0,.18);
        }
        .modal-header{
            position: sticky; top: 0; z-index: 1;
            background: #fff; border-bottom: 1px solid #e5e7eb;
            padding: 16px 20px; text-align: center;
        }
        .modal-title{ font-size: 20px; font-weight: 700; color:#111827 }
        .modal-close{
            position: absolute; top: 12px; right: 12px;
            width: 36px; height: 36px; border-radius: 50%;
            border: none; background: rgba(17,24,39,.06); color:#374151;
            cursor: pointer; transition: .2s;
        }
        .modal-close:hover{ background: rgba(17,24,39,.12); transform: rotate(90deg); }
        .modal-body{ padding: 18px 20px 6px }
        .modal-footer{ display:none } /* buton altı çizgiyi tamamen kaldırıyoruz */

        /* ====== 3) SAYFA KARTI & TABLO ====== */
        .center-box{
            max-width:1300px; margin:3rem auto; padding:2.8rem 3rem; border-radius:2rem;
            background: rgba(255,255,255,.65);
            backdrop-filter: blur(30px) saturate(160%);
            -webkit-backdrop-filter: blur(30px) saturate(160%);
            border:1px solid rgba(255,255,255,.3);
            box-shadow:0 40px 80px rgba(0,0,0,.1);
            transition: all .4s ease;
        }
        .center-box h2,.center-box h3{ font-size:1.9rem; font-weight:800; margin-bottom:1.8rem; color:#0f172a }
        .center-box label{ font-weight:600; color:#1e293b }

        /* ====== 4) FORM ELEMANLARI (tek stil: pill) ====== */
        .modal-card input,
        .modal-card select,
        .modal-card textarea,
        .center-box input,
        .center-box select,
        .center-box textarea{
            width:100%; padding:.9rem 1.3rem; margin-top:.4rem;
            border-radius:9999px; font-size:1rem;
            background:#f8fafc; color:#1e293b;
            border:1px solid #d1d5db; box-shadow: inset 0 1px 3px rgba(0,0,0,.04);
            transition: all .25s ease;
        }
        .modal-card input:focus,
        .modal-card select:focus,
        .center-box input:focus,
        .center-box select:focus{
            outline:none; background:#fff; border-color:#22c55e;
            box-shadow:0 0 0 4px rgba(34,197,94,.18);
        }
        .modal-card input::placeholder{ color:#94a3b8 }

        /* ====== 5) TABLO ====== */
        .center-box table{
            width:100%; border-collapse: separate !important;
            border-spacing: 0 !important; border: 0 !important;
            background: rgba(255,255,255,.96);
            border-radius: 22px; overflow: hidden;
            margin-top: 2.2rem; box-shadow: none !important;
        }
        .center-box thead th{
            background:#f3f4f6; color:#374151; font-weight:700; border:0 !important;
        }
        .center-box th,.center-box td{ padding:1rem 1.2rem; font-size:.95rem; text-align:center; color:#1e293b; border:0 !important; }
        .center-box tbody tr + tr td{ box-shadow: inset 0 -1px 0 rgba(226,232,240,.6); }
        .center-box tbody tr:last-child td{ box-shadow:none; }

        .edit-button{
            background: rgba(59,130,246,.08);
            border:1px solid rgba(59,130,246,.22);
            color:#2563eb; font-weight:600; padding:.6rem 1.2rem;
            border-radius:9999px; backdrop-filter: blur(6px); transition:.3s;
        }
        .edit-button:hover{
            background:linear-gradient(to right,#3b82f6,#2563eb);
            color:#fff; transform:scale(1.05);
            box-shadow:0 8px 18px rgba(59,130,246,.4)
        }

        /* ====== 6) Kayıt Ekle butonu ====== */
        #toggleForm{
            background: linear-gradient(135deg, #16a34a, #22c55e);
            padding:.65rem 1.6rem; font-weight:800; border-radius:9999px;
            color:#fff; font-size:1.02rem; letter-spacing:.2px;
            box-shadow:0 10px 24px rgba(34,197,94,.28);
            transition:.25s;
        }
        #toggleForm:hover{ transform: translateY(-1px) scale(1.02); background: linear-gradient(135deg,#15803d,#16a34a) }

        /* ====== 7) Submit animasyonu ====== */
        .submit-animated{
            width:170px; height:48px; border-radius:9999px;
            background: linear-gradient(135deg,#1d4ed8,#2563eb); border:none;
            position:relative; overflow:hidden; font-size:14px; font-weight:700; color:#fff; cursor:pointer;
            transition:all .25s ease; box-shadow:0 8px 20px rgba(0,80,160,.2);
        }
        .submit-animated:hover{ background:#1746c1 }
        .submit-animated:focus{ animation:extend 1s ease-in-out forwards }
        .submit-animated:focus span{ animation:disappear 1s ease-in-out forwards }
        .submit-animated:focus img{ animation:appear 1s ease-in-out forwards }
        .submit-animated img{ position:absolute; width:15px; height:15px; top:50%; left:50%; transform:translate(-50%,-50%); opacity:0 }
        @keyframes extend{ 0%{width:170px;height:48px;border-radius:9999px} 50%{background:#22c55e} 100%{width:60px;height:60px;border-radius:50%;background:#22c55e} }
        @keyframes disappear{ 0%{opacity:1} 100%{opacity:0} }
        @keyframes appear{ 0%{opacity:0} 100%{opacity:1} }
    </style>

    <div class="py-6" 
         data-edit="{{ $isEdit ? '1' : '0' }}" 
         data-cancel-url="{{ $cancelUrl }}">
        <div class="center-box">
            <!-- Sağ üstte Kayıt Ekle -->
            <div class="flex justify-end mb-4">
                <button id="toggleForm" type="button">Kayıt Ekle</button>
            </div>

            <!-- GÜNLÜK ZİYARETÇİ LİSTESİ -->
            <div class="glass-panel">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Bugünün Ziyaretçi Listesi</h2>
                <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
                    <thead class="text-left">
                        <tr>
                            <th class="px-4 py-2">Ad Soyad</th>
                            <th class="px-4 py-2">T.C.</th>
                            <th class="px-4 py-2">Telefon</th>
                            <th class="px-4 py-2">Plaka</th>
                            <th class="px-4 py-2">Giriş Saati</th>
                            <th class="px-4 py-2">Ziyaret Sebebi</th>
                            <th class="px-4 py-2">Ziyaret Edilen</th>
                            <th class="px-4 py-2">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @forelse ($visits as $visit)
                            <tr class="group">
                                <td class="px-4 py-2">{{ $visit->visitor->name }}</td>
                                <td class="px-4 py-2">{{ $visit->visitor->tc_no }}</td>
                                <td class="px-4 py-2">{{ $visit->phone }}</td>
                                <td class="px-4 py-2">{{ $visit->plate }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($visit->entry_time)->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2">{{ $visit->purpose }}</td>
                                <td class="px-4 py-2">{{ $visit->person_to_visit }}</td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ route('security.edit', $visit->id) }}" class="edit-button">Düzenle</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-2 text-center">Bugün kayıtlı ziyaretçi yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- === BACKDROP + MODAL (erişilebilirlik için role/aria) === -->
        <div id="visit-backdrop" class="backdrop" aria-hidden="true"></div>

        <div id="visit-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="visit-modal-title">
            <div class="modal-card">
                <div class="modal-header">
                    <button class="modal-close" type="button" id="close-modal" aria-label="Kapat">&times;</button>
                    <div id="visit-modal-title" class="modal-title">
                        {{ $isEdit ? 'Ziyaret Kaydı Güncelle' : 'Yeni Ziyaret Kaydı' }}
                    </div>
                </div>

                <div class="modal-body">
                    <!-- === FORM === -->
                    <form method="POST" action="{{ $formAction }}" id="visit-form">
                        @csrf
                        @if($isEdit) @method('PUT') @endif

                        <h3 class="text-lg font-semibold mb-4">Ziyaretçi Bilgileri</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- T.C. -->
                            <div>
                                <x-input-label for="tc_no" :value="'T.C. Kimlik No'" />
                                <x-text-input id="tc_no" name="tc_no" type="text" maxlength="11"
                                    value="{{ old('tc_no', $editVisit->visitor->tc_no ?? '') }}"
                                    required class="mt-1 block w-full"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)"
                                    onblur="getVisitorData()" />
                                @error('tc_no') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Ad Soyad -->
                            <div>
                                <x-input-label for="name" :value="'Ad Soyad'" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    autocomplete="off"
                                    value="{{ $isEdit ? $editVisit->visitor->name : old('name') }}" required />
                                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold mt-6 mb-4">Ziyaret Bilgisi</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Telefon -->
                            <div>
                                <x-input-label for="phone" :value="'Telefon'" />
                                <input id="phone" name="phone" type="text" class="mt-1 block w-full" list="phone_list"
                                    autocomplete="off"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').replace(/(\d{4})(\d{3})(\d{2})(\d{2})/,'$1 $2 $3 $4').slice(0,14)"
                                    placeholder="0500 000 00 00"
                                    value="{{ $isEdit ? $editVisit->phone : old('phone') }}" />
                                @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                                <datalist id="phone_list"></datalist>
                            </div>

                            <!-- Plaka (opsiyonel) -->
                            <div>
                                <x-input-label for="plate" :value="'Plaka (opsiyonel)'" />
                                <input name="plate" id="plate" type="text" class="mt-1 block w-full uppercase"
                                    list="plate_list" autocomplete="off" maxlength="20"
                                    value="{{ $isEdit ? $editVisit->plate : old('plate') }}" />
                                @error('plate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                                <datalist id="plate_list"></datalist>
                            </div>

                            <!-- Ziyaret Edilen -->
                            <div>
                                <x-input-label for="person_to_visit" :value="'Ziyaret Edilen Kişi'" />
                                <select name="person_to_visit" id="person_to_visit" required>
                                    <option value="">Kişi Seçiniz</option>
                                    @foreach($people as $person)
                                        <option value="{{ $person->person_name }}"
                                            @selected(old('person_to_visit', $editVisit->person_to_visit ?? '') == $person->person_name)>
                                            {{ $person->person_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('person_to_visit') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Sebep -->
                            <div class="mt-4 md:mt-0">
                                <x-input-label for="purpose" :value="'Ziyaret Sebebi'" />
                                <select name="purpose" id="purpose" required>
                                    <option value="">Sebep Seçiniz</option>
                                    @foreach($reasons as $reason)
                                        <option value="{{ $reason->reason }}"
                                            @selected(old('purpose', $editVisit->purpose ?? '') == $reason->reason)>
                                            {{ $reason->reason }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('purpose') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <button type="button" class="submit-animated" id="submit-button">
                                <span>{{ $isEdit ? 'GÜNCELLE' : 'KAYDET' }}</span>
                                <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="✓">
                            </button>

                            @if($isEdit)
                                <a href="{{ $cancelUrl }}" class="text-blue-600 hover:underline">İptal</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ====== JS: davranışlar tek yerde (SRP) ====== -->
    <script>
    (function () {
        const $  = (sel, ctx=document) => ctx.querySelector(sel);
        const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

        const root        = document.currentScript.closest('div.py-6');
        const isEdit      = root?.dataset.edit === '1';
        const cancelUrl   = root?.dataset.cancelUrl;

        const backdrop    = $('#visit-backdrop');
        const modal       = $('#visit-modal');
        const openBtn     = $('#toggleForm');
        const closeBtn    = $('#close-modal');
        const submitBtn   = $('#submit-button');
        const form        = $('#visit-form');

        function openModal()  { backdrop.classList.add('show'); modal.classList.add('show'); }
        function closeModal() { backdrop.classList.remove('show'); modal.classList.remove('show'); }

        function handleClose() {
            if (isEdit) {
                window.location.href = cancelUrl; // İptal ile aynı
            } else {
                closeModal();
            }
        }

        // --- Event binding
        if (openBtn)  openBtn.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', handleClose);
        if (backdrop) backdrop.addEventListener('click', handleClose);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') handleClose(); });

        // --- Animasyonlu submit
        if (submitBtn && form) {
            submitBtn.addEventListener('click', function () {
                submitBtn.focus(); // animasyonu tetiklemek için
                setTimeout(() => form.submit(), 1000);
            });
        }

        // --- Page-load: edit modunda veya validasyon hatasında modal otomatik açılsın
        @if ($isEdit || $errors->any())
            openModal();
        @endif

        // === TC değişince geçmiş veriyi getir (global fonksiyon ismi korunuyor) ===
        window.getVisitorData = function () {
            const tcInput = $('#tc_no');
            if (!tcInput) return;
            const tc = (tcInput.value || '').trim();
            if (tc.length !== 11) return;

            fetch(`/security/visitor-by-tc/${tc}`)
                .then(res => res.json())
                .then(data => {
                    if (!data) return;

                    const nameInput = $('#name');
                    const phoneList = $('#phone_list');
                    const plateList = $('#plate_list');

                    if (nameInput && !nameInput.value) {
                        nameInput.value = data.name || '';
                    }

                    if (phoneList) {
                        phoneList.innerHTML = '';
                        (data.phones || []).forEach(p => {
                            const opt = document.createElement('option');
                            opt.value = p; phoneList.appendChild(opt);
                        });
                    }
                    if (plateList) {
                        plateList.innerHTML = '';
                        (data.plates || []).forEach(pl => {
                            const opt = document.createElement('option');
                            opt.value = pl; plateList.appendChild(opt);
                        });
                    }
                })
                .catch(() => {});
        };

        // TC alanı change’de de çalışsın
        const tcInput = $('#tc_no');
        if (tcInput) tcInput.addEventListener('change', window.getVisitorData);
    })();
    </script>
</x-app-layout>
