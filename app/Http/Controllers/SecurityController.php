<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitorStoreRequest;
use App\Http\Requests\VisitorUpdateRequest;
use App\Services\VisitorService;
use App\Models\Visit;
use App\Models\PersonToVisit;
use App\Models\VisitReason;
use Illuminate\Support\Carbon;

class SecurityController extends Controller
{
    protected VisitorService $visitorService;

    public function __construct(VisitorService $visitorService)
    {
        $this->visitorService = $visitorService;
    }

    /**
     * Ziyaretçi kayıt sayfasını ve bugünkü girişleri getirir.
     */
    public function create()
    {
        return view('security.create', [
            'visits' => Visit::with('visitor')->whereDate('created_at', Carbon::today())->get(),
            'people' => PersonToVisit::all(),
            'reasons' => VisitReason::all(),
        ]);
    }

    /**
     * Yeni bir ziyaretçi kaydını işler.
     */
    public function store(VisitorStoreRequest $request)
    {
        $this->visitorService->store($request->validated(), auth()->user());

        return redirect()->route('security.create')
            ->with('success', 'Ziyaretçi başarıyla kaydedildi.');
    }

    /**
     * Mevcut bir ziyaretçi kaydını düzenlemek için formu doldurur.
     */
    public function edit($id)
    {
        return view('security.create', [
            'visits' => Visit::with('visitor')->whereDate('created_at', Carbon::today())->get(),
            'editVisit' => Visit::with('visitor')->findOrFail($id),
            'people' => PersonToVisit::all(),
            'reasons' => VisitReason::all(),
        ]);
    }

    /**
     * Güncellenen ziyaretçi bilgilerini kaydeder.
     */
    public function update(VisitorUpdateRequest $request, $id)
    {
        $this->visitorService->update($id, $request->validated(), auth()->user());

        return redirect()->route('security.create')
            ->with('success', 'Ziyaretçi bilgisi güncellendi.');
    }

    /**
     * T.C. numarasına göre geçmiş ziyaret bilgilerini getirir.
     */
    public function getVisitorData($tc)
    {
        return $this->visitorService->getVisitorData($tc, auth()->user());
    }
}
