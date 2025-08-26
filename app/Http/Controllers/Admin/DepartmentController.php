<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * DepartmentController
 * 
 * Üniversite/kurum içindeki birimleri (departmanları) yönetmek için kullanılan controller.
 * Tüm CRUD (Create, Read, Update, Delete) işlemlerini içerir.
 */
class DepartmentController extends Controller
{
    /**
     * Birim listesini görüntüler.
     * GET -> /admin/departments
     */
    public function index()
    {
        $departments = Department::all();

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Yeni birim ekleme formunu gösterir.
     * GET -> /admin/departments/create
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Yeni birimi veritabanına kaydeder.
     * POST -> /admin/departments
     */
    public function store(Request $request)
    {
        // ✅ Validasyon kuralları
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        Department::create($validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Birim başarıyla eklendi.');
    }

    /**
     * Birim düzenleme formunu gösterir.
     * GET -> /admin/departments/{id}/edit
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Birim bilgisini günceller.
     * PUT/PATCH -> /admin/departments/{id}
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('departments')->ignore($department->id)
            ],
        ]);

        $department->update($validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Birim başarıyla güncellendi.');
    }

    /**
     * Birimi kalıcı olarak siler.
     * DELETE -> /admin/departments/{id}
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Birim başarıyla silindi.');
    }
}
