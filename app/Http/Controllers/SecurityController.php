<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Visit;
use Illuminate\Support\Carbon;

class SecurityController extends Controller
{
    public function create()
    {
        $visits = Visit::with('visitor')->whereDate('created_at', Carbon::today())->get();
        return view('security.create', compact('visits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'tc_no' => 'required|string',
            'phone' => 'required|string',
            'plate_city' => 'required|string',
            'plate_letters' => 'required|string',
            'plate_number' => 'required|string',
            'person_to_visit' => 'required|string',
            'purpose' => 'required|string',
        ]);

        $securityId = auth()->user()->id;

        $plate = strtoupper($request->plate_city . ' ' . $request->plate_letters . ' ' . $request->plate_number);

        $visitor = Visitor::firstOrCreate(
            ['tc_no' => $request->tc_no],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'plate' => $plate,
                'approved_by' => $securityId,
            ]
        );

        Visit::create([
            'visitor_id' => $visitor->id,
            'entry_time' => now(),
            'person_to_visit' => $request->person_to_visit,
            'purpose' => $request->purpose,
            'approved_by' => $securityId,
        ]);

        return redirect()->route('security.create')->with('success', 'Ziyaretçi başarıyla kaydedildi.');
    }

    public function edit($id)
    {
        $visits = Visit::with('visitor')->whereDate('created_at', Carbon::today())->get();
        $editVisit = Visit::with('visitor')->findOrFail($id);
        return view('security.create', compact('visits', 'editVisit'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'tc_no' => 'required|string|size:11',
            'phone' => 'required|string',
            'plate_city' => 'required|string',
            'plate_letters' => 'required|string',
            'plate_number' => 'required|string',
            'entry_time' => 'required|date',
            'person_to_visit' => 'required|string',
            'purpose' => 'required|string',
        ]);

        $plate = strtoupper($request->plate_city . ' ' . $request->plate_letters . ' ' . $request->plate_number);

        $visit = Visit::with('visitor')->findOrFail($id);

        $visit->visitor->update([
            'name' => $request->name,
            'tc_no' => $request->tc_no,
            'phone' => $request->phone,
            'plate' => $plate,
        ]);

        $visit->update([
            'entry_time' => $request->entry_time,
            'person_to_visit' => $request->person_to_visit,
            'purpose' => $request->purpose,
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
}
