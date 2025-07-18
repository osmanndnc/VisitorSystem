<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\User;
use App\Models\Visit;

class AdminController extends Controller
{
    // public function index()
    // {
    //     $visits = Visit::with('visitor')->latest()->get();
    //     return view('admin.index', compact('visits'));
    // }
    public function index()
    {
        $visitors = Visitor::all();
        //$visitors = Visitor::with(['visit', 'approver'])->get();
        $fields = []; // İlk açılışta tüm sütunlar gözüksün, checkboxlar boş gelsin
        return view('admin.index', compact('visitors', 'fields'));
    }

    // Filtreli tablo için (checkbox ile seçim sonrası)
    public function fields(Request $request)
    {
        $fields = $request->input('fields', []);
        $visitors = Visitor::all();
        return view('admin.index', compact('visitors', 'fields'));
    }
}