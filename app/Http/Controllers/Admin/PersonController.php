<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $persons = Person::all();
        return view('admin.persons.index', compact('persons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all(); 

        return view('admin.persons.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'departments' => 'required|array',
            'departments.*' => 'exists:departments,id',
        ]);
        
        $person = Person::create([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
        ]);

        $person->departments()->attach($validated['departments']);

        return redirect()->route('admin.persons.index')->with('success', 'Kişi başarıyla eklendi.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person)
    {
        $departments = Department::all();

        return view('admin.persons.edit', compact('person', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Person $person)
    {
       // Pasif/Aktif yapma işlemi
        if ($request->has('is_active')) {
            $person->is_active = $request->is_active;
            $person->save();
            return redirect()->back()->with('success', 'Kişinin durumu başarıyla güncellendi.');
        }

        // Düzenleme (Update) işlemi
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'departments' => 'required|array',
            'departments.*' => 'exists:departments,id',
        ]);

        $person->update([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
        ]);
        
        $person->departments()->sync($validated['departments']);

        return redirect()->route('admin.persons.index')->with('success', 'Kişi başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        $person->delete();
        return redirect()->route('admin.persons.index')->with('success', 'Kişi başarıyla silindi.');
    }
}