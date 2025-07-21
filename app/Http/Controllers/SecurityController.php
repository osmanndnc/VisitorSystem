<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Visit;

class SecurityController extends Controller
{
    // public function index()
    // {
    //     // Sadece view döndür, veri yok!
    //     return view('security.index');
    // }

    public function create()
    {
        return view('security.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'tc_no' => 'required|string',
            'phone' => 'required|string',
            'plate' => 'nullable|string',
            'entry_time' => 'required|date',
            'person_to_visit' => 'required|string',
            'purpose' => 'required|string',
        ]);

        $securityId = auth()->user()->id;
        
        $visitor = Visitor::create([
            'name' => $request->name,
            'tc_no' => $request->tc_no,
            'phone' => $request->phone,
            'plate' => $request->plate,
            'approved_by' => $securityId,
        ]);

        Visit::create([
            'visitor_id' => $visitor->id,
            'entry_time' => $request->entry_time,
            //'exit_time' => null,
            'person_to_visit' => $request->person_to_visit,
            'purpose' => $request->purpose,
            'approved_by' => $securityId,//ekledim
        ]);

        return redirect()->route('security.create')->with('success', 'Ziyaretçi başarıyla kaydedildi.');
    }
}
