<?php

use App\Http\Controllers\ClinicSettingsController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\PatientPortalController;
use App\Http\Controllers\PdfController;
use App\Http\Middleware\InitializeTenancyBySubdomainId;
use App\Http\Middleware\SetTenancyUrlDefaults;
use App\Livewire\Auth\RegisterTenant;
use App\Livewire\PatientPortal\BookAppointment;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', RegisterTenant::class)->name('register');

Route::get('/register/success', function (Request $request) {
    if (! $request->has('tenant_id')) {
        return redirect('/');
    }

    $clinic = Clinic::with('domains')->find($request->tenant_id);
    if (! $clinic) {
        return redirect('/');
    }

    // URL generation for the tenant: prioritize subdomain if it exists and we're not on localhost
    $domain = $clinic->domains->first();
    $host = request()->getHost();
    $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1']);

    if ($domain && ! $isLocal) {
        $url = (request()->secure() ? 'https://' : 'http://').$domain->domain.'/app';
    } else {
        // Fallback to path-based identification
        $url = url('/'.$clinic->id.'/app');
    }

    return view('auth.register-success', ['clinic' => $clinic, 'url' => $url]);
})->name('register.success');

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'es'])) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('lang.switch');

// Portal Routes (signed URLs for patient access)
Route::middleware([
    'web',
    'signed',
    'throttle:portal',
    InitializeTenancyByPath::class,
])->group(function () {
    Route::get('/{tenant?}/portal/{patient}', [PatientPortalController::class, 'dashboard'])->name('portal.dashboard');
    Route::get('/{tenant?}/portal/{patient}/book', BookAppointment::class)->name('portal.book');
    Route::get('/{tenant?}/portal/{patient}/budgets/{budget}', [PatientPortalController::class, 'viewBudget'])->name('portal.budgets.view');
    Route::get('/{tenant?}/portal/{patient}/budgets/{budget}/pdf', [PdfController::class, 'downloadBudgetPortal'])->name('portal.budgets.pdf');
    Route::post('/{tenant?}/portal/{patient}/budgets/{budget}/accept', [PatientPortalController::class, 'acceptBudget'])->name('portal.budgets.accept');
    Route::post('/{tenant?}/portal/{patient}/budgets/{budget}/reject', [PatientPortalController::class, 'rejectBudget'])->name('portal.budgets.reject');
    Route::get('/{tenant?}/portal/{patient}/appointments', [PatientPortalController::class, 'appointments'])->name('portal.appointments');
    Route::post('/{tenant?}/portal/{patient}/appointments/{appointment}/cancel', [PatientPortalController::class, 'cancelAppointment'])->name('portal.appointments.cancel');
    Route::get('/{tenant?}/portal/{patient}/prescriptions', [PatientPortalController::class, 'prescriptions'])->name('portal.prescriptions');
    Route::get('/{tenant?}/portal/{patient}/medical-history', [PatientPortalController::class, 'medicalHistory'])->name('portal.medical-history');
    Route::get('/{tenant?}/portal/{patient}/payments', [PatientPortalController::class, 'payments'])->name('portal.payments');
});

Route::middleware([
    'web',
    InitializeTenancyBySubdomainId::class,
    SetTenancyUrlDefaults::class,
])->group(function () {
    Route::post('/app/clinic-settings/save', [ClinicSettingsController::class, 'save'])->name('clinic-settings.save');
    Route::get('/app/budgets/{budget}/pdf', [PdfController::class, 'downloadBudget'])->name('budgets.pdf');
    Route::get('/app/odontograms/{odontogram}/pdf', [PdfController::class, 'downloadOdontogram'])->name('odontograms.pdf');
});
