<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Person;
use Illuminate\Http\Request;

/**
 * PersonController
 * 
 * Ziyaret edilecek/ilgili kişiler için CRUD işlemlerini yönetir.
 * Her kişi bir veya birden fazla departman ile ilişkilendirilebilir.
 */
class PersonController extends Controller
{
    /**
     * Kişi listesini görüntüler.
     * GET -> /admin/persons
     */
    public function index()
    {
        $persons = Person::all();

        return view('admin.persons.index', compact('persons'));
    }

    /**
     * Yeni kişi ekleme formunu gösterir.
     * GET -> /admin/persons/create
     */
    public function create()
    {
        $departments = Department::all(); // Dropdown için departman listesi

        return view('admin.persons.create', compact('departments'));
    }

    /**
     * Yeni kişiyi veritabanına kaydeder.
     * POST -> /admin/persons
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'phone_number'  => 'nullable|string|max:20',
            'departments'   => 'required|array',
            'departments.*' => 'exists:departments,id',
        ]);

        // Kişiyi oluştur
        $person = Person::create([
            'name'         => $validated['name'],
            'phone_number' => $validated['phone_number'],
        ]);

        // Departman ilişkisini bağla (pivot tablo)
        $person->departments()->attach($validated['departments']);

        return redirect()
            ->route('admin.persons.index')
            ->with('success', 'Kişi başarıyla eklendi.');
    }

    /**
     * Kişi düzenleme formunu gösterir.
     * GET -> /admin/persons/{id}/edit
     */
    public function edit(Person $person)
    {
        $departments = Department::all();

        return view('admin.persons.edit', compact('person', 'departments'));
    }

    /**
     * Kişi bilgisini günceller veya aktif/pasif durumunu değiştirir.
     * PUT/PATCH -> /admin/persons/{id}
     */
    public function update(Request $request, Person $person)
    {
        // ✅ Eğer sadece aktif/pasif güncellemesi yapılıyorsa
        if ($request->has('is_active')) {
            $person->is_active = $request->is_active;
            $person->save();

            return redirect()
                ->back()
                ->with('success', 'Kişinin durumu başarıyla güncellendi.');
        }

        // ✅ Normal güncelleme işlemi
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'phone_number'  => 'nullable|string|max:20',
            'departments'   => 'required|array',
            'departments.*' => 'exists:departments,id',
        ]);

        $person->update([
            'name'         => $validated['name'],
            'phone_number' => $validated['phone_number'],
        ]);

        // Departman ilişkilerini güncelle
        $person->departments()->sync($validated['departments']);

        return redirect()
            ->route('admin.persons.index')
            ->with('success', 'Kişi başarıyla güncellendi.');
    }

    /**
     * Kişiyi kalıcı olarak siler.
     * DELETE -> /admin/persons/{id}
     */
    public function destroy(Person $person)
    {
        $person->delete();

        return redirect()
            ->route('admin.persons.index')
            ->with('success', 'Kişi başarıyla silindi.');
    }
}
