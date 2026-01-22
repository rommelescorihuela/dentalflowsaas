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
