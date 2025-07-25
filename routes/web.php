<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\ReportExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Ana sayfa yönlendirmesi
Route::get('/', function () {
    return redirect()->route('login');
});

// Çıkış (Logout) rotası
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Dashboard yönlendirmesi (Role göre yönlendirme)
Route::get('/dashboard', function () {
    return redirect(\App\Providers\RouteServiceProvider::redirectToBasedOnRole());
})->middleware(['auth', 'verified'])->name('dashboard');

// Profil işlemleri (sadece giriş yapmış kullanıcılar)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Güvenlik işlemleri (Admin Controller'dan ayrı, auth middleware'i altında)
    Route::get('/security/create', [SecurityController::class, 'create'])->name('security.create');
    Route::post('/security/store', [SecurityController::class, 'store'])->name('security.store');
    Route::get('/security/{id}/edit', [SecurityController::class, 'edit'])->name('security.edit');
    Route::put('/security/{id}', [SecurityController::class, 'update'])->name('security.update');
    Route::delete('/security/{id}', [SecurityController::class, 'destroy'])->name('security.destroy');
});


// Admin işlemleri (sadece admin rolüne sahip ve giriş yapmış kullanıcılar)
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    Route::post('/admin/fields', [AdminController::class, 'fields'])->name('admin.fields');

   
    Route::get('/admin/generate-report', [AdminReportController::class, 'generateReport'])->name('admin.generateReport');


    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports');


    Route::get('/report/export', [ReportExportController::class, 'export'])->withoutMiddleware([\App\Http\Middleware\PreventBackHistory::class])->name('report.export');

    
});

require __DIR__.'/auth.php';