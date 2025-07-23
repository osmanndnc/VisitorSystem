<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\ReportExportController;

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
    //return view('dashboard');
    return redirect(\App\Providers\RouteServiceProvider::redirectToBasedOnRole());
})->middleware(['auth', 'verified'])->name('dashboard');

// Profil işlemleri (sadece giriş yapmış kullanıcılar)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Güvenlik işlemleri (sadece giriş yapmış kullanıcılar)
Route::middleware(['auth'])->group(function () {
    Route::get('/security/create', [SecurityController::class, 'create'])->name('security.create');
    Route::post('/security/store', [SecurityController::class, 'store'])->name('security.store');
});

    Route::get('/security/{id}/edit', [SecurityController::class, 'edit'])->name('security.edit');
    Route::put('/security/{id}', [SecurityController::class, 'update'])->name('security.update');
    Route::delete('/security/{id}', [SecurityController::class, 'destroy'])->name('security.destroy');

// Admin işlemleri (sadece admin rolüne sahip ve giriş yapmış kullanıcılar)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/fields', [AdminController::class, 'fields'])->name('admin.fields');
    Route::get('/admin/reports',[AdminController::class,'reports'])->name('admin.reports');
});

// Admin Raporları
    Route::get('/report', [AdminReportController::class, 'index'])->name('admin.report.index');
    Route::post('/report/generate', [AdminReportController::class, 'generateReport'])->name('admin.report.generate');

    Route::get('/report/export', [ReportExportController::class, 'export'])->name('report.export');
    Route::post('/admin/report', [AdminReportController::class, 'generateReport'])->name('admin.report');
    
require __DIR__.'/auth.php';