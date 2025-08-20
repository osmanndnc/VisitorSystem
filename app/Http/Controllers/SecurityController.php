<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitStoreRequest;
use App\Http\Requests\VisitUpdateRequest;
use App\Models\PersonToVisit;
use App\Models\Visit;
use App\Models\VisitReason;
use App\Services\VisitService;
use Illuminate\Support\Carbon;

class SecurityController extends Controller
{
    /**
     * Controller sadece HTTP akışını yönetir:
     * - Verileri ekrana gönderir
     * - İstekleri doğrulama katmanına (FormRequest) ve iş katmanına (Service) delege eder
     * - View/Redirect döner
     */
    public function __construct(private VisitService $service)
    {
        // Laravel otomatik DI ile VisitService'i enjekte eder.
    }

    /**
     * Günlük ziyaret kayıtlarını ve dropdown verilerini getirir.
     * SRP: Veri toplama ve işleme service katmanında; burada sadece ekrana hazırlama var.
     */
    public function create()
    {
        $visits  = Visit::with('visitor')
                    ->whereDate('created_at', Carbon::today())
                    ->get();

        $people  = PersonToVisit::all();
        $reasons = VisitReason::all();

        return view('security.create', compact('visits', 'people', 'reasons'));
    }

    /**
     * Yeni ziyaret kaydı oluşturma.
     * - Doğrulama: VisitStoreRequest
     * - İş mantığı: VisitService::store
     * - Sonuç: redirect + flash
     */
    public function store(VisitStoreRequest $request)
    {
        $visit = $this->service->store(
            data: $request->validated(),
            approvedByUserId: auth()->id()
        );

        return redirect()
            ->route('security.create')
            ->with('success', 'Ziyaretçi başarıyla kaydedildi.');
    }

    /**
     * Belirli bir ziyaret kaydı düzenlenmek üzere formda gösterilir.
     * - Route Model Binding ile Visit otomatik çözümlenir.
     */
    public function edit(Visit $visit)
    {
        $visits    = Visit::with('visitor')
                        ->whereDate('created_at', Carbon::today())
                        ->get();

        $editVisit = $visit->load('visitor');
        $people    = PersonToVisit::all();
        $reasons   = VisitReason::all();

        return view('security.create', compact('visits', 'editVisit', 'people', 'reasons'));
    }

    /**
     * Ziyaret kaydı güncelleme.
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
     * TC numarası ile önceki ziyaretçi bilgilerini getirir (AJAX).
     * - İnce uç nokta: İş mantığı service'te.
     * - Ziyaretçi yoksa null döndürür (mevcut davranış korunur).
     */
    public function getVisitorData(string $tc)
    {
        $data = $this->service->getVisitorDataByTc($tc);

        if (!$data) {
            return response()->json(null);
        }

        return response()->json($data);
    }
}
