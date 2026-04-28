@php
    $url = \App\Filament\App\Resources\Patients\PatientResource::getUrl('odontograms.view', [
        'patient' => $odontogram->patient_id,
        'odontogram' => $odontogram->id,
    ]);
@endphp

<a href="{{ $url }}" class="text-primary-600 hover:text-primary-700 underline">
    {{ $odontogram->name ?? 'Odontogram #' . $odontogram->id }}
</a>
