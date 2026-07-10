<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinicSettingsController extends Controller
{
    public function save(Request $request)
    {
        $tenant = tenant();

        if (! $tenant) {
            $user = auth()->user();
            if ($user && $user->clinic_id) {
                $tenantModel = config('tenancy.tenant_model');
                $tenant = $tenantModel::find($user->clinic_id);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }
        }

        if (! $tenant) {
            return back()->withErrors(['tenant' => 'No se pudo identificar la clínica.']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string',
            'timezone' => 'required|string',
            'schedule_start' => 'required',
            'schedule_end' => 'required',
            'primary_color' => 'nullable|string',
        ]);

        $logoPath = $tenant->data['logo'] ?? null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        DB::table('tenants')
            ->where('id', $tenant->id)
            ->update([
                'name' => $data['name'],
                'data' => json_encode(array_merge(is_array($tenant->data) ? $tenant->data : json_decode($tenant->data ?? '{}', true), [
                    'logo' => $logoPath,
                    'currency' => $data['currency'],
                    'timezone' => $data['timezone'],
                    'schedule_start' => $data['schedule_start'],
                    'schedule_end' => $data['schedule_end'],
                    'primary_color' => $data['primary_color'] ?? '#0891b2',
                    'onboarding_step' => 4,
                ])),
            ]);

        // We can't use Filament notifications directly here easily, so we use session
        return redirect()->back()->with('success', 'Configuración guardada correctamente.');
    }
}
