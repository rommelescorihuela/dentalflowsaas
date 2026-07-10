@extends('patient-portal.layout')

@section('title', 'Historia Clínica')

@section('content')
<h2 class="text-2xl font-display font-bold text-stone-800 mb-6 portal-reveal">Mi Historia Clínica</h2>

@if($medicalHistory)
<div class="space-y-6">
    <!-- Antecedentes -->
    <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden portal-reveal portal-reveal-delay-1">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-2 rounded-lg mr-3 shadow-lg shadow-primary-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                Antecedentes
            </h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($medicalHistory->antecedentes_personales)
            <div class="col-span-full">
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide mb-2">Antecedentes Personales</p>
                <p class="text-stone-700 bg-[#FFFBF5] rounded-xl p-4 border border-stone-100">{{ $medicalHistory->antecedentes_personales }}</p>
            </div>
            @endif
            @if($medicalHistory->antecedentes_familiares)
            <div class="col-span-full">
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide mb-2">Antecedentes Familiares</p>
                <p class="text-stone-700 bg-[#FFFBF5] rounded-xl p-4 border border-stone-100">{{ $medicalHistory->antecedentes_familiares }}</p>
            </div>
            @endif
            @if($medicalHistory->alergias)
            <div>
                <p class="text-xs font-semibold text-rose-600 uppercase tracking-wide mb-2">Alergias</p>
                <p class="text-stone-700 bg-rose-50 rounded-xl p-4 border border-rose-100">{{ $medicalHistory->alergias }}</p>
            </div>
            @endif
            @if($medicalHistory->medicamentos_actuales)
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide mb-2">Medicamentos Actuales</p>
                <p class="text-stone-700 bg-amber-50 rounded-xl p-4 border border-amber-100">{{ $medicalHistory->medicamentos_actuales }}</p>
            </div>
            @endif
            @if($medicalHistory->enfermedades_cronicas)
            <div class="col-span-full">
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide mb-2">Enfermedades Crónicas</p>
                <p class="text-stone-700 bg-[#FFFBF5] rounded-xl p-4 border border-stone-100">{{ $medicalHistory->enfermedades_cronicas }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Hábitos y Cirugías -->
    <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden portal-reveal portal-reveal-delay-2">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-2 rounded-lg mr-3 shadow-lg shadow-primary-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                Hábitos y Cirugías
            </h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($medicalHistory->habitos)
            <div>
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide mb-2">Hábitos</p>
                <p class="text-stone-700 bg-[#FFFBF5] rounded-xl p-4 border border-stone-100">{{ $medicalHistory->habitos }}</p>
            </div>
            @endif
            @if($medicalHistory->cirugias_previas)
            <div>
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide mb-2">Cirugías Previas</p>
                <p class="text-stone-700 bg-[#FFFBF5] rounded-xl p-4 border border-stone-100">{{ $medicalHistory->cirugias_previas }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Historia Dental -->
    @if($medicalHistory->historia_dental)
    <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden portal-reveal portal-reveal-delay-3">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-2 rounded-lg mr-3 shadow-lg shadow-primary-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                </span>
                Historia Dental
            </h3>
        </div>
        <div class="p-6">
            <p class="text-stone-700 bg-[#FFFBF5] rounded-xl p-4 border border-stone-100">{{ $medicalHistory->historia_dental }}</p>
        </div>
    </div>
    @endif

    <!-- Signos Vitales -->
    @if($medicalHistory->presion_arterial || $medicalHistory->frecuencia_cardiaca || $medicalHistory->peso || $medicalHistory->altura || $medicalHistory->grupo_sanguineo)
    <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden portal-reveal portal-reveal-delay-4">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-primary-50/50">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-2 rounded-lg mr-3 shadow-lg shadow-primary-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                </span>
                Signos Vitales
            </h3>
        </div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-5 gap-6">
            @if($medicalHistory->presion_arterial)
            <div class="text-center p-4 bg-stone-50 rounded-xl">
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide">Presión Arterial</p>
                <p class="text-xl font-bold text-stone-800 mt-1">{{ $medicalHistory->presion_arterial }}</p>
                <p class="text-xs text-stone-400">mmHg</p>
            </div>
            @endif
            @if($medicalHistory->frecuencia_cardiaca)
            <div class="text-center p-4 bg-stone-50 rounded-xl">
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide">Frec. Cardíaca</p>
                <p class="text-xl font-bold text-stone-800 mt-1">{{ $medicalHistory->frecuencia_cardiaca }}</p>
                <p class="text-xs text-stone-400">lpm</p>
            </div>
            @endif
            @if($medicalHistory->peso)
            <div class="text-center p-4 bg-stone-50 rounded-xl">
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide">Peso</p>
                <p class="text-xl font-bold text-stone-800 mt-1">{{ number_format($medicalHistory->peso, 1) }}</p>
                <p class="text-xs text-stone-400">kg</p>
            </div>
            @endif
            @if($medicalHistory->altura)
            <div class="text-center p-4 bg-stone-50 rounded-xl">
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide">Altura</p>
                <p class="text-xl font-bold text-stone-800 mt-1">{{ number_format($medicalHistory->altura, 2) }}</p>
                <p class="text-xs text-stone-400">m</p>
            </div>
            @endif
            @if($medicalHistory->grupo_sanguineo)
            <div class="text-center p-4 bg-stone-50 rounded-xl">
                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide">Grupo Sang.</p>
                <p class="text-xl font-bold text-rose-600 mt-1">{{ $medicalHistory->grupo_sanguineo }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@else
<div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-12 text-center">
    <svg class="mx-auto h-16 w-16 mb-4 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <p class="font-display font-semibold text-lg text-stone-500">Historia clínica no disponible</p>
    <p class="text-sm text-stone-400 mt-1">Tu doctor registrará tu historia clínica durante tu primera consulta.</p>
</div>
@endif
@endsection
