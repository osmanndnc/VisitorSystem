<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\User;
use App\Models\Visit;

class AdminController extends Controller
{
   public function index()
   {
        $visitors=Visitor::all();
        return view('admin.index',compact('visitors'));
   }

}
