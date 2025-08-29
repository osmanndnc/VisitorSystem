<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Http\Controllers\{
    ProfileController,
    SecurityController,
    AdminController,
    AdminReportController,
    ReportExportController,
    AdminUserController,
    SecurityUserController,
    UserController
};

use App\Http\Controllers\Admin\{
    DepartmentController,
    PersonController
};

// =========================== //
//       GENEL ROUTES         //
// =========================== //

// Ana sayfa yönlendirmesi
Route::get('/', fn() => redirect()->route('login'));

// Çıkış (Logout)
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Dashboard yönlendirmesi (role bazlı)
Route::get('/dashboard', fn() => redirect(\App\Providers\RouteServiceProvider::redirectToBasedOnRole()))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// =========================== //
//     PROFİL (GENEL)         //
// =========================== //
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =========================== //
//    GÜVENLİK PANELİ         //
// =========================== //
Route::middleware(['auth', 'role:security'])->group(function () {
    Route::get('/security/create', [SecurityController::class, 'create'])->name('security.create');
    Route::post('/security/store', [SecurityController::class, 'store'])->name('security.store');
    Route::get('/security/{visit}/edit', [SecurityController::class, 'edit'])->name('security.edit');
    Route::put('/security/{visit}', [SecurityController::class, 'update'])->name('security.update');
    Route::delete('/security/{visit}', [SecurityController::class, 'destroy'])->name('security.destroy');

    // AJAX: Birime bağlı kişileri getir
    Route::get('/security/department/{id}/persons', [SecurityController::class, 'getPersonsByDepartment'])
        ->name('security.department.persons');

    // AJAX: TC ile geçmiş ziyaretçi verisi getir
    Route::get('/security/visitor-by-tc/{tc}', [SecurityController::class, 'getVisitorData'])
        ->name('security.visitor.by.tc');
});

// =========================== //
//       ADMİN PANELİ         //
// =========================== //
Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {

    // Admin giriş sayfası
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // Raporlar
    Route::get('/admin/reports',              [AdminReportController::class, 'index'])->name('admin.reports');
    Route::get('/admin/generate-report',      [AdminReportController::class, 'generateReport'])->name('admin.generateReport');
    Route::get('/admin/reports/masked-pdf',   [AdminReportController::class, 'exportMaskedPdf'])->name('report.maskedPdf');
    Route::get('/admin/export-pdf-unmasked',  [AdminController::class, 'exportPdfUnmasked'])->name('admin.exportPdfUnmasked');

    // Excel export (Özel middleware hariç tutuldu)
    Route::get('/report/export', [ReportExportController::class, 'export'])
        ->withoutMiddleware([\App\Http\Middleware\PreventBackHistory::class])
        ->name('report.export');

    // Alan filtreleme
    Route::post('/admin/fields', [AdminController::class, 'fields'])->name('admin.fields');

    // Kaynak yönetimi
    Route::resource('departments', DepartmentController::class)->names('admin.departments');
    Route::resource('persons', PersonController::class)->names('admin.persons');
});

// =========================== //
//   KULLANICI YÖNETİMİ       //
// =========================== //

// Admin kullanıcı yönetimi
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/admin-users',                [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/admin-users/{user}',         [AdminUserController::class, 'update'])->name('users.update');
    Route::patch('/admin-users/{user}/toggle',[AdminUserController::class, 'toggleStatus'])->name('users.toggle');
});

// Security kullanıcı yönetimi
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('security')->name('security.')->group(function () {
    Route::get('/security-users',               [SecurityUserController::class, 'index'])->name('users.index');
    Route::put('/security-users/{user}',        [SecurityUserController::class, 'update'])->name('users.update');
    Route::patch('/security-users/{user}/toggle',[SecurityUserController::class, 'toggle'])->name('users.toggle');
});

// Admin kullanıcı ekleme (sadece super_admin)
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/admin-users', [AdminUserController::class, 'store'])->name('users.store');
});

// Security kullanıcı ekleme
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('security')->name('security.')->group(function () {
    Route::post('/security-users', [SecurityUserController::class, 'store'])->name('users.store');
});

// =========================== //
// CSP RAPORLAMA ENDPOINTİ     //
// =========================== //
Route::post('/csp-report', function (Request $request) {
    Log::channel('csp')->warning('CSP violation', [
        'report' => $request->all(),
        'ip'     => $request->ip(),
    ]);
    return response()->noContent();
});

require __DIR__.'/auth.php';
