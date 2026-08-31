<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use App\Models\Clinic;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function show(Request $request, string $clinicId)
    {
        $clinic = Clinic::findOrFail($clinicId);

        $setting = ClinicSetting::where('clinic_id', $clinicId)->first();

        if (! $setting || ! $setting->landing_enabled) {
            abort(404);
        }

        return view('landing.show', [
            'clinic' => $clinic,
            'setting' => $setting,
        ]);
    }
}
