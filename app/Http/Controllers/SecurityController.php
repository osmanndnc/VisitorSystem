<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitStoreRequest;
use App\Http\Requests\VisitUpdateRequest;
use App\Models\Person;
use App\Models\Visit;
use App\Models\VisitReason;
use App\Models\Department;
use App\Services\VisitService;

class SecurityController extends Controller
{
    /**
     * DIP: İş mantığı VisitService’e enjekte edilir (constructor DI).
     */
    public function __construct(private VisitService $service)
    {
        //
    }

    /**
     * Güvenlik ana form sayfası:
     * - Bugünün ziyaretlerini listeler
     * - Kişi ve sebep dropdown verilerini getirir
     */
    public function create()
    {
        $visits = Visit::with(['visitor','department'])
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->get();

        $people  = Person::orderBy('name')->get(['id', 'name']);
        $reasons = VisitReason::orderBy('reason')->get(['id', 'reason']);
        $departments = Department::orderBy('name')->get(['id', 'name']);

        return view('security.create', compact('visits', 'people', 'reasons','departments'));
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
     * Ziyaret düzenleme formu.
     * Route model binding ile Visit ve ilişkileri çözülür.
     */
    public function edit(Visit $visit)
    {
        $visits = Visit::with(['visitor','department'])
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->get();

        $editVisit = $visit->load('visitor');
        $people    = Person::orderBy('name')->get(['id', 'name']);
        $reasons   = VisitReason::orderBy('reason')->get(['id', 'reason']);
        $departments = Department::orderBy('name')->get(['id', 'name']);

        return view('security.create', compact('visits', 'editVisit', 'people', 'reasons','departments'));
    }

    /**
     * Mevcut ziyaret kaydını günceller.
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
     * AJAX: TC kimlik numarasına göre geçmiş ziyaretçi verilerini getirir.
     * - İsim, geçmiş telefonlar, plakalar (öneri amaçlı)
     */
    public function getVisitorData(string $tc)
    {
        $data = $this->service->getVisitorDataByTc($tc);
        return response()->json($data ?: null);
    }

    /**
     * AJAX: Seçilen birime bağlı kişileri getirir.
     * - Tek birime bağlı kişiler (one-to-many)
     */
    public function getPersonsByDepartment(int $id)
    {
        $department = \App\Models\Department::find($id);

        if (!$department) {
            return response()->json(['people' => []]);
        }

        // belongsToMany ile ilişki üzerinden kişiler çekiliyor
        $people = $department->persons()->orderBy('name')->get(['id', 'name']);

        return response()->json([
            'people' => $people->map(fn($p) => [
                'id'   => $p->id,
                'name' => $p->name,
            ])->values()
        ]);
    }
}
