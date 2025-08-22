<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitStoreRequest;
use App\Http\Requests\VisitUpdateRequest;
use App\Models\Person;        // ✅ Artık Person kullanıyoruz
use App\Models\Visit;
use App\Models\VisitReason;
use App\Services\VisitService;

class SecurityController extends Controller
{
    /**
     * Controller sadece HTTP akışını yönetir:
     * - View’a veri hazırlar
     * - Doğrulama (FormRequest) + iş mantığını (Service) delege eder
     * - View/Redirect döner
     *
     * DIP: İş mantığı VisitService’e enjekte edilir (constructor DI).
     */
    public function __construct(private VisitService $service)
    {
        //
    }

    /**
     * Günlük ziyaret kayıtları ve dropdown verileri.
     * SRP: Veriyi sadece hazırlar; iş mantığı Service katmanındadır.
     */
    public function create()
    {
        $visits  = Visit::with('visitor')
            ->whereDate('created_at', today())
            ->get();

        // Kişi ve sebep listeleri (sade alanlarla, sıralı)
        $people  = Person::query()->orderBy('name')->get(['id', 'name']);
        $reasons = VisitReason::query()->orderBy('reason')->get(['id', 'reason']);

        return view('security.create', compact('visits', 'people', 'reasons'));
    }

    /**
     * Yeni ziyaret kaydı oluşturur.
     * - Doğrulama: VisitStoreRequest
     * - İş mantığı: VisitService::store
     */
    public function store(VisitStoreRequest $request)
    {
        $this->service->store(
            data: $request->validated(),
            approvedByUserId: auth()->id()
        );

        return redirect()
            ->route('security.create')
            ->with('success', 'Ziyaretçi başarıyla kaydedildi.');
    }

    /**
     * Düzenleme formu.
     * Route Model Binding ile Visit otomatik çözülür.
     */
    public function edit(Visit $visit)
    {
        $visits     = Visit::with('visitor')->whereDate('created_at', today())->get();
        $editVisit  = $visit->load('visitor');
        $people     = Person::query()->orderBy('name')->get(['id', 'name']);
        $reasons    = VisitReason::query()->orderBy('reason')->get(['id', 'reason']);

        return view('security.create', compact('visits', 'editVisit', 'people', 'reasons'));
    }

    /**
     * Kaydı günceller.
     * - Doğrulama: VisitUpdateRequest
     * - İş mantığı: VisitService::update
     */
    public function update(VisitUpdateRequest $request, Visit $visit)
    {
        $this->service->update($visit, $request->validated());

        return redirect()
            ->route('security.create')
            ->with('success', 'Ziyaretçi bilgisi güncellendi.');
    }

    /**
     * TC ile geçmiş ziyaretçi verisi (AJAX).
     * Ziyaretçi yoksa null döner.
     */
    public function getVisitorData(string $tc)
    {
        $data = $this->service->getVisitorDataByTc($tc);
        return response()->json($data ?: null);
    }
}
