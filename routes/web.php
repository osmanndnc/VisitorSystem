<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Route::get('/security', [SecurityController::class, 'index'])->name('security.index');

//Güvenlik için
Route::middleware(['auth'])->group(function () {
    Route::get('/security/create', [SecurityController::class, 'create'])->name('security.create');
    Route::post('/security/store', [SecurityController::class, 'store'])->name('security.store');
});
// Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

// Route::post('/admin/fields', [AdminController::class, 'fields'])->name('admin.fields');

//Admin için
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/fields', [AdminController::class, 'fields'])->name('admin.fields');
});

require __DIR__.'/auth.php';    
