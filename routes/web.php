<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', \App\Livewire\Auth\RegisterTenant::class)->name('register');

Route::get('/register/success', function (\Illuminate\Http\Request $request) {
    if (!$request->has('tenant_id')) {
        return redirect('/');
    }

    $clinic = \App\Models\Clinic::with('domains')->find($request->tenant_id);
    if (!$clinic) {
        return redirect('/');
    }

    // URL generation for the tenant: prioritize subdomain if it exists and we're not on localhost
    $domain = $clinic->domains->first();
    $host = request()->getHost();
    $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1']);

    if ($domain && !$isLocal) {
        $url = (request()->secure() ? 'https://' : 'http://') . $domain->domain . '/app';
    } else {
        // Fallback to path-based identification
        $url = url('/' . $clinic->id . '/app');
    }

    return view('auth.register-success', ['clinic' => $clinic, 'url' => $url]);
})->name('register.success');

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/terms', [\App\Http\Controllers\LegalController::class, 'terms'])->name('legal.terms');
Route::get('/privacy', [\App\Http\Controllers\LegalController::class, 'privacy'])->name('legal.privacy');
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
    \Stancl\Tenancy\Middleware\InitializeTenancyByPath::class,
])->group(function () {
    Route::get('/{tenant?}/portal/{patient}', [\App\Http\Controllers\PatientPortalController::class , 'dashboard'])->name('portal.dashboard');
    Route::get('/{tenant?}/portal/{patient}/book', \App\Livewire\PatientPortal\BookAppointment::class)->name('portal.book');
    Route::post('/{tenant?}/portal/budgets/{budget}/accept', [\App\Http\Controllers\PatientPortalController::class , 'acceptBudget'])->name('portal.budgets.accept');
});