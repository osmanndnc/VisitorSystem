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
    public function create()
    {
        $visits = Visit::with('visitor')->whereDate('created_at', Carbon::today())->get();

        //Dropdownlar için liste çekiyoruz
        $people = PersonToVisit::all();
        $reasons = VisitReason::all();

        return view('security.create', compact('visits', 'people', 'reasons'));
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'tc_no' => ['required', 'string', new ValidTcNo()],
                'name' => 'required|string',
                'phone' => 'required|string',
                'plate' => 'required|string',
                'person_to_visit' => 'required|string',
                'purpose' => 'required|string',
            ]);

            $securityId = auth()->user()->id;

            $visitor = Visitor::firstOrCreate(
                ['tc_no' => $request->tc_no],
                ['name' => $request->name]
            );

            Visit::create([
                'visitor_id' => $visitor->id,
                'entry_time' => now(),
                'person_to_visit' => $request->person_to_visit,
                'purpose' => $request->purpose,
                'approved_by' => $securityId,
                'phone' => $request->phone,
                'plate' => strtoupper($request->plate),
            ]);

            Log::info('Ziyaretçi kaydı oluşturuldu.', [
                'by_user_id' => $securityId,
                'by_username' => auth()->user()->username,
                'tc_no' => $request->tc_no,
                'ip' => $request->ip(),
                'time' => now(),
            ]);

            return redirect()->route('security.create')->with('success', 'Ziyaretçi başarıyla kaydedildi.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Form doğrulama hatası.', [
                'by_user_id' => auth()->id(),
                'by_username' => auth()->user()->username ?? 'Anonim',
                'ip' => $request->ip(),
                'hatalar' => $e->errors(),
                'time' => now(),
            ]);

            throw $e; // Laravel normal hata mesajlarını göstermeye devam etsin
        }
    }

    public function edit($id)
    {
        $visits = Visit::with('visitor')->whereDate('created_at', Carbon::today())->get();
        $editVisit = Visit::with('visitor')->findOrFail($id);
        $people = PersonToVisit::all();
        $reasons = VisitReason::all();
        return view('security.create', compact('visits', 'editVisit', 'people', 'reasons'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tc_no' => ['required', 'string', new ValidTcNo()],
            'name' => 'required|string',
            'phone' => 'required|string',
            'plate' => 'required|string',
            'person_to_visit' => 'required|string',
            'purpose' => 'required|string',
        ]);

        $visit = Visit::with('visitor')->findOrFail($id);

        $visit->visitor->update([
            'name' => $request->name,
            'tc_no' => $request->tc_no,
        ]);

        $visit->update([
            'entry_time' => now(),
            'person_to_visit' => $request->person_to_visit,
            'purpose' => $request->purpose,
            'phone' => $request->phone,
            'plate' => strtoupper($request->plate),
        ]);

        Log::info('Ziyaretçi kaydı güncellendi.', [
            'by_user_id' => auth()->id(),
            'by_username' => auth()->user()->username,
            'tc_no' => $request->tc_no,
            'ip' => $request->ip(),
            'time' => now(),
        ]);

        return redirect()->route('security.create')->with('success', 'Ziyaretçi bilgisi güncellendi.');
    }

    // public function destroy($id)
    // {
    //     $visit = Visit::findOrFail($id);

    //     Log::warning('Ziyaretçi kaydı siliniyor.', [
    //         'by_user_id' => auth()->id(),
    //         'by_username' => auth()->user()->username,
    //         'tc_no' => $visit->visitor->tc_no ?? 'Bilinmiyor',
    //         'ip' => request()->ip(),
    //         'time' => now(),
    //     ]);

    //     $visit->visitor->delete();
    //     $visit->delete();

    //     return redirect()->route('security.create')->with('success', 'Ziyaretçi kaydı silindi.');
    // }

    public function getVisitorData($tc)
    {
        $visitor = Visitor::where('tc_no', $tc)->first();

        if (!$visitor) {
            // Sisteme ilk defa kayıt yapılan ziyaretçi
            Log::notice('Ziyaretçi bulunamadı (getVisitorData).', [
                'tc_no' => $tc,
                'requested_by' => auth()->user()->username ?? 'Anonim',
                'ip' => request()->ip(),
                'time' => now(),
            ]);
            return response()->json(null);
        }

        $phones = Visit::where('visitor_id', $visitor->id)
            ->pluck('phone')
            ->unique()
            ->values();

        $plates = Visit::where('visitor_id', $visitor->id)
            ->pluck('plate')
            ->unique()
            ->values();
        
        // Daha önceden sisteme kayıtlı bir ziyaretçi
        Log::info('Ziyaretçi verisi getirildi (getVisitorData).', [
            'tc_no' => $tc,
            'requested_by' => auth()->user()->username ?? 'Anonim',
            'ip' => request()->ip(),
            'time' => now(),
        ]);

        return response()->json([
            'name' => $visitor->name,
            'phones' => $phones,
            'plates' => $plates
        ]);
    }

}
