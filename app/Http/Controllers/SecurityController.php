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

class SecurityController extends Controller
{
    /**
     * Günlük ziyaret kayıtlarını ve dropdown verilerini getirir.
     */
    public function create()
    {
        $visits = Visit::with('visitor')->whereDate('created_at', Carbon::today())->get();
        $people = PersonToVisit::all();
        $reasons = VisitReason::all();

        return view('security.create', compact('visits', 'people', 'reasons'));
    }

    /**
     * Yeni ziyaret kaydını oluşturur ve loglar.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tc_no' => ['required', 'string', new ValidTcNo()],
                'name' => 'required|string',
                'phone' => 'required|string',
                'plate' => 'required|string',
                'person_to_visit' => 'required|string',
                'purpose' => 'required|string',
            ]);

            $user = auth()->user();

            $visitor = Visitor::firstOrCreate(
                ['tc_no' => $validated['tc_no']],
                ['name' => $validated['name']]
            );

            Visit::create([
                'visitor_id' => $visitor->id,
                'entry_time' => now(),
                'person_to_visit' => $validated['person_to_visit'],
                'purpose' => $validated['purpose'],
                'approved_by' => $user->id,
                'phone' => $validated['phone'],
                'plate' => strtoupper($validated['plate']),
            ]);

            Log::info('Ziyaretçi kaydı oluşturuldu.', [
                'by_user_id' => $user->id,
                'by_username' => $user->username,
                'tc_no' => $validated['tc_no'],
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            return redirect()->route('security.create')->with('success', 'Ziyaretçi başarıyla kaydedildi.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Form doğrulama hatası.', [
                'by_user_id' => auth()->id(),
                'by_username' => auth()->user()->username ?? 'Anonim',
                'ip' => $request->ip(),
                'errors' => $e->errors(),
                'time' => now(),
            ]);
            throw $e;
        }
    }

    /**
     * Belirli bir ziyaret kaydı düzenlenmek üzere formda gösterilir.
     */
    public function edit($id)
    {
        $visits = Visit::with('visitor')->whereDate('created_at', Carbon::today())->get();
        $editVisit = Visit::with('visitor')->findOrFail($id);
        $people = PersonToVisit::all();
        $reasons = VisitReason::all();

        return view('security.create', compact('visits', 'editVisit', 'people', 'reasons'));
    }

    /**
     * Ziyaret kaydı güncellenir ve loglanır.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tc_no' => ['required', 'string', new ValidTcNo()],
            'name' => 'required|string',
            'phone' => 'required|string',
            'plate' => 'required|string',
            'person_to_visit' => 'required|string',
            'purpose' => 'required|string',
        ]);

        $visit = Visit::with('visitor')->findOrFail($id);

        $visit->visitor->update([
            'tc_no' => $validated['tc_no'],
            'name' => $validated['name'],
        ]);

        $visit->update([
            'entry_time' => now(),
            'person_to_visit' => $validated['person_to_visit'],
            'purpose' => $validated['purpose'],
            'phone' => $validated['phone'],
            'plate' => strtoupper($validated['plate']),
        ]);

        Log::info('Ziyaretçi kaydı güncellendi.', [
            'by_user_id' => auth()->id(),
            'by_username' => auth()->user()->username,
            'tc_no' => $validated['tc_no'],
            'ip' => $request->ip(),
            'time' => now(),
        ]);

        return redirect()->route('security.create')->with('success', 'Ziyaretçi bilgisi güncellendi.');
    }

    /**
     * TC numarası ile önceki ziyaretçi bilgilerini getirir (AJAX).
     */
    public function getVisitorData($tc)
    {
        $visitor = Visitor::where('tc_no', $tc)->first();

        if (!$visitor) {
            Log::notice('Ziyaretçi bulunamadı (getVisitorData).', [
                'tc_no' => $tc,
                'requested_by' => auth()->user()->username ?? 'Anonim',
                'ip' => request()->ip(),
                'time' => now(),
            ]);

            return response()->json(null);
        }

        $phones = Visit::where('visitor_id', $visitor->id)->pluck('phone')->unique()->values();
        $plates = Visit::where('visitor_id', $visitor->id)->pluck('plate')->unique()->values();

        Log::info('Ziyaretçi verisi getirildi (getVisitorData).', [
            'tc_no' => $tc,
            'requested_by' => auth()->user()->username ?? 'Anonim',
            'ip' => request()->ip(),
            'time' => now(),
        ]);

        return response()->json([
            'name' => $visitor->name,
            'phones' => $phones,
            'plates' => $plates,
        ]);
    }

    /*
    // İstenirse açılabilir:
    public function destroy($id)
    {
        $visit = Visit::findOrFail($id);

        Log::warning('Ziyaretçi kaydı siliniyor.', [
            'by_user_id' => auth()->id(),
            'by_username' => auth()->user()->username,
            'tc_no' => $visit->visitor->tc_no ?? 'Bilinmiyor',
            'ip' => request()->ip(),
            'time' => now(),
        ]);

        $visit->visitor->delete();
        $visit->delete();

        return redirect()->route('security.create')->with('success', 'Ziyaretçi kaydı silindi.');
    }
    */
}
