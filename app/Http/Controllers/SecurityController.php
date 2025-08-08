<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\PersonToVisit;
use App\Models\VisitReason;
use Illuminate\Support\Carbon;
use App\Rules\ValidTcNo;
use Illuminate\Support\Facades\Log;
use Throwable;

class SecurityController extends Controller
{
    /**
     * Günlük ziyaret kayıtlarını ve dropdown verilerini getirir.
     */
    public function create()
    {
        $visits  = Visit::with('visitor')->whereDate('created_at', Carbon::today())->get();
        $people  = PersonToVisit::all();
        $reasons = VisitReason::all();

        Log::channel('security')->info('Sayfa açıldı', $this->logContext([
            'action'  => 'security_create_view',
            'status'  => 'success',
            'message' => 'Güvenlik ziyaret oluşturma sayfası görüntülendi',
            'count'   => $visits->count(),
        ]));

        return view('security.create', compact('visits', 'people', 'reasons'));
    }

    /**
     * Yeni ziyaret kaydını oluşturur ve loglar.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tc_no'          => ['required', 'string', new ValidTcNo()],
                'name'           => 'required|string',
                'phone'          => 'required|string',
                'plate'          => 'required|string',
                'person_to_visit'=> 'required|string',
                'purpose'        => 'required|string',
            ]);

            $user = auth()->user();

            $visitor = Visitor::firstOrCreate(
                ['tc_no' => $validated['tc_no']],
                ['name'  => $validated['name']]
            );

            $visit = Visit::create([
                'visitor_id'     => $visitor->id,
                'entry_time'     => now(),
                'person_to_visit'=> $validated['person_to_visit'],
                'purpose'        => $validated['purpose'],
                'approved_by'    => $user->id,
                'phone'          => $validated['phone'],
                'plate'          => strtoupper($validated['plate']),
            ]);

            Log::channel('security')->info('Ziyaret kaydı oluşturuldu', $this->logContext([
                'action'     => 'visit_store',
                'status'     => 'success',
                'message'    => 'Yeni ziyaret kaydı eklendi',
                'record_id'  => $visit->id,
                'tc_no'      => $validated['tc_no'],
                'person_to_visit' => $validated['person_to_visit'],
            ]));

            return redirect()->route('security.create')->with('success', 'Ziyaretçi başarıyla kaydedildi.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('security')->warning('Form doğrulama hatası', $this->logContext([
                'action'  => 'visit_store',
                'status'  => 'failed',
                'message' => 'Validasyon başarısız',
                'errors'  => $e->errors(),
            ]));
            throw $e;
        } catch (Throwable $e) {
            Log::channel('security')->error('Ziyaret kaydı oluşturulamadı', $this->logContext([
                'action'  => 'visit_store',
                'status'  => 'failed',
                'message' => $e->getMessage(),
            ]));
            throw $e;
        }
    }

    /**
     * Belirli bir ziyaret kaydı düzenlenmek üzere formda gösterilir.
     */
    public function edit($id)
    {
        $visits    = Visit::with('visitor')->whereDate('created_at', Carbon::today())->get();
        $editVisit = Visit::with('visitor')->findOrFail($id);
        $people    = PersonToVisit::all();
        $reasons   = VisitReason::all();

        Log::channel('security')->info('Ziyaret düzenleme açıldı', $this->logContext([
            'action'    => 'visit_edit_view',
            'status'    => 'success',
            'message'   => 'Ziyaret düzenleme formu görüntülendi',
            'record_id' => $id,
        ]));

        return view('security.create', compact('visits', 'editVisit', 'people', 'reasons'));
    }

    /**
     * Ziyaret kaydı güncellenir ve loglanır.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tc_no'          => ['required', 'string', new ValidTcNo()],
                'name'           => 'required|string',
                'phone'          => 'required|string',
                'plate'          => 'required|string',
                'person_to_visit'=> 'required|string',
                'purpose'        => 'required|string',
            ]);

            $visit = Visit::with('visitor')->findOrFail($id);

            // değişiklik öncesi
            $before = [
                'visitor_name' => $visit->visitor->name,
                'visitor_tc'   => $visit->visitor->tc_no,
                'phone'        => $visit->phone,
                'plate'        => $visit->plate,
                'purpose'      => $visit->purpose,
                'person_to_visit' => $visit->person_to_visit,
            ];

            $visit->visitor->update([
                'tc_no' => $validated['tc_no'],
                'name'  => $validated['name'],
            ]);

            $visit->update([
                'entry_time'     => now(),
                'person_to_visit'=> $validated['person_to_visit'],
                'purpose'        => $validated['purpose'],
                'phone'          => $validated['phone'],
                'plate'          => strtoupper($validated['plate']),
            ]);

            // değişen alanlar
            $after = [
                'visitor_name' => $visit->visitor->name,
                'visitor_tc'   => $visit->visitor->tc_no,
                'phone'        => $visit->phone,
                'plate'        => $visit->plate,
                'purpose'      => $visit->purpose,
                'person_to_visit' => $visit->person_to_visit,
            ];
            $changed = array_keys(array_diff_assoc($after, $before));

            Log::channel('security')->info('Ziyaret kaydı güncellendi', $this->logContext([
                'action'        => 'visit_update',
                'status'        => 'success',
                'message'       => 'Ziyaretçi bilgisi güncellendi',
                'record_id'     => $visit->id,
                'changed_fields'=> $changed,
                'tc_no'         => $validated['tc_no'],
            ]));

            return redirect()->route('security.create')->with('success', 'Ziyaretçi bilgisi güncellendi.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('security')->warning('Form doğrulama hatası', $this->logContext([
                'action'  => 'visit_update',
                'status'  => 'failed',
                'message' => 'Validasyon başarısız',
                'errors'  => $e->errors(),
            ]));
            throw $e;
        } catch (Throwable $e) {
            Log::channel('security')->error('Ziyaret kaydı güncellenemedi', $this->logContext([
                'action'  => 'visit_update',
                'status'  => 'failed',
                'message' => $e->getMessage(),
                'record_id' => $id,
            ]));
            throw $e;
        }
    }

    /**
     * TC numarası ile önceki ziyaretçi bilgilerini getirir (AJAX).
     */
    public function getVisitorData($tc)
    {
        $visitor = Visitor::where('tc_no', $tc)->first();

        if (!$visitor) {
            Log::channel('security')->warning('Ziyaretçi bulunamadı', $this->logContext([
                'action'  => 'visitor_lookup',
                'status'  => 'not_found',
                'message' => 'Girilen TC ile ziyaretçi yok',
                'tc_no'   => $tc,
            ]));
            return response()->json(null);
        }

        $phones = Visit::where('visitor_id', $visitor->id)->pluck('phone')->unique()->values();
        $plates = Visit::where('visitor_id', $visitor->id)->pluck('plate')->unique()->values();

        Log::channel('security')->info('Ziyaretçi geçmişi getirildi', $this->logContext([
            'action'  => 'visitor_lookup',
            'status'  => 'success',
            'message' => 'Ziyaretçi verisi getirildi',
            'tc_no'   => $tc,
            'phones'  => $phones->count(),
            'plates'  => $plates->count(),
        ]));

        return response()->json([
            'name'   => $visitor->name,
            'phones' => $phones,
            'plates' => $plates,
        ]);
    }

    /*
    // İstenirse açılabilir:
    public function destroy($id)
    {
        $visit = Visit::findOrFail($id);

        Log::channel('security')->warning('Ziyaretçi kaydı siliniyor', $this->logContext([
            'action'    => 'visit_destroy',
            'status'    => 'warning',
            'message'   => 'Ziyaret kaydı siliniyor',
            'record_id' => $id,
        ]));

        $visit->visitor->delete();
        $visit->delete();

        return redirect()->route('security.create')->with('success', 'Ziyaretçi kaydı silindi.');
    }
    */

    /**
     * Ortak log context (tüm loglara standard alanlar).
     */
    private function logContext(array $extra = []): array
    {
        $actor = auth()->user();

        return array_merge([
            'user_id'  => $actor->id ?? null,
            'username' => $actor->username ?? 'Anonim',
            'ip'       => request()->ip(),
            'time'     => now()->toDateTimeString(),
        ], $extra);
    }
}
