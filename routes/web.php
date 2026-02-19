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

    $clinic = \App\Models\Clinic::find($request->tenant_id);
    if (!$clinic) {
        return redirect('/');
    }

    // Protocol agnostic URL generation for the tenant
    $domain = $clinic->domains->first()->domain;
    $protocol = request()->secure() ? 'https://' : 'http://';
    $url = $protocol . $domain . (in_array(request()->getPort(), [80, 443]) ? '' : ':' . request()->getPort());

    return view('auth.register-success', ['clinic' => $clinic, 'url' => $url]);
})->name('register.success');

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Portal Routes (Duplicated here to ensure registration for Filament URL generation)
Route::middleware(['web', 'signed'])->group(function () {
    Route::get('/portal/{patient}', [\App\Http\Controllers\PatientPortalController::class , 'dashboard'])->name('portal.dashboard');
    Route::get('/portal/{patient}/book', \App\Livewire\PatientPortal\BookAppointment::class)->name('portal.book');
    Route::post('/portal/budgets/{budget}/accept', [\App\Http\Controllers\PatientPortalController::class , 'acceptBudget'])->name('portal.budgets.accept');
});