<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\PersonToVisit;
use App\Models\VisitReason;
use Illuminate\Support\Carbon;

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
        $request->validate([
            'name' => 'required|string',
            'tc_no' => 'required|string|size:11',
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

        return redirect()->route('security.create')->with('success', 'Ziyaretçi başarıyla kaydedildi.');
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
            'name' => 'required|string',
            'tc_no' => 'required|string|size:11',
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

        return redirect()->route('security.create')->with('success', 'Ziyaretçi bilgisi güncellendi.');
    }


    public function destroy($id)
    {
        $visit = Visit::findOrFail($id);
        $visit->visitor->delete();
        $visit->delete();

        return redirect()->route('security.create')->with('success', 'Ziyaretçi kaydı silindi.');
    }

    public function getVisitorData($tc)
    {
        $visitor = Visitor::where('tc_no', $tc)->first();

        if (!$visitor) {
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

        return response()->json([
            'name' => $visitor->name,
            'phones' => $phones,
            'plates' => $plates
        ]);
    }


    
}
