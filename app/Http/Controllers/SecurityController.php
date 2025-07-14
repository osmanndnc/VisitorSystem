<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function index()
    {
        // Sadece view döndür, veri yok!
        return view('security.index');
    }
}
