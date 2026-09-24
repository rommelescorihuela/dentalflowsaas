<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; font-size: 12px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1f9e8f; padding-bottom: 15px; margin-bottom: 20px; }
        .clinic-info h1 { font-size: 18px; color: #1f9e8f; margin-bottom: 4px; }
        .clinic-info p { font-size: 11px; color: #6b7280; line-height: 1.4; }
        .odo-meta { text-align: right; }
        .odo-meta h2 { font-size: 20px; color: #1f2937; }
        .odo-meta p { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 13px; font-weight: bold; color: #1f9e8f; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .patient-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 16px; }
        .patient-card .row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .patient-card .label { color: #6b7280; font-size: 10px; }
        .patient-card .value { font-weight: 600; }
        .arch-label { font-size: 10px; color: #6b7280; text-align: center; margin: 4px 0 8px; font-weight: bold; }
        .arch { width: 100%; border-collapse: collapse; margin: 0 0 4px; }
        .arch td.tooth-cell { text-align: center; vertical-align: top; padding: 0 1px; border: none; }
        .tooth-num { font-size: 7px; font-weight: bold; color: #4b5563; line-height: 1.2; }
        table.tooth { width: 24px; margin: 0 auto; border-collapse: collapse; }
        table.tooth td { padding: 0; border: 0.5px solid #9ca3af; }
        table.tooth td.s-top, table.tooth td.s-bottom { height: 5px; }
        table.tooth td.s-left, table.tooth td.s-center, table.tooth td.s-right { height: 11px; }
        table.tooth td.s-left, table.tooth td.s-right { width: 4px; }
        table.tooth td.s-center { width: 16px; font-size: 7px; font-weight: bold; color: #ffffff; line-height: 11px; }
        .legend { margin-top: 20px; }
        .legend-title { font-size: 11px; font-weight: bold; margin-bottom: 6px; color: #374151; }
        .legend-items { display: flex; flex-wrap: wrap; gap: 6px; }
        .legend-item { display: flex; align-items: center; gap: 4px; font-size: 9px; }
        .legend-swatch { width: 12px; height: 12px; border-radius: 2px; border: 1px solid #d1d5db; }
        table.records { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.records thead { background: #1f9e8f; color: white; }
        table.records th { text-align: left; padding: 6px 10px; font-size: 10px; }
        table.records td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        table.records tbody tr:nth-child(even) { background: #f9fafb; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 9px; }
        @page { margin: 20px 15px; }
    </style>
</head>
<body>
    @php
        $statusLabels = [
            'draft' => 'Borrador',
            'in_progress' => 'En progreso',
            'completed' => 'Completado',
        ];
        $diagnosisLabels = [
            'caries' => 'Caries', 'filled' => 'Obturación', 'endodontic' => 'Endodoncia',
            'missing' => 'Ausente', 'healthy' => 'Sano', 'crown' => 'Corona',
            'prophylaxis' => 'Profilaxis', 'sealant' => 'Sellante', 'fluoride' => 'Flúor',
            'inlay' => 'Incrustación', 'scaling' => 'Detartraje', 'gingivectomy' => 'Gingivectomía',
            'flap_surgery' => 'Cirugía de colgajo', 'surgical_extraction' => 'Extracción quirúrgica',
            'wisdom_tooth' => 'Cordal', 'implant' => 'Implante', 'implant_crown' => 'Corona sobre implante',
            'braces_metal' => 'Brackets metálicos', 'braces_aesthetic' => 'Brackets estéticos',
            'retainer_fixed' => 'Contención fija', 'retainer_removable' => 'Contención removible',
            'crown_pfm' => 'Corona metal-porcelana', 'crown_zirconia' => 'Corona de zirconio',
            'bridge' => 'Puente', 'partial_denture' => 'Prótesis parcial', 'full_denture' => 'Prótesis total',
            'whitening' => 'Blanqueamiento', 'veneer_composite' => 'Carilla de composite',
            'veneer_ceramic' => 'Carilla cerámica', 'consultation' => 'Consulta',
            'xray_periapical' => 'Radiografía periapical', 'xray_panoramic' => 'Radiografía panorámica',
            'cbct' => 'Tomografía CBCT',
        ];
        $surfaceLabels = [
            'top' => 'Superior', 'bottom' => 'Inferior', 'left' => 'Izquierda',
            'right' => 'Derecha', 'center' => 'Centro', 'root' => 'Raíz',
        ];
        $treatmentLabels = [
            'planned' => 'Planificado', 'pending' => 'Pendiente', 'in_progress' => 'En progreso',
            'completed' => 'Completado', 'cancelled' => 'Cancelado',
        ];
        $label = fn ($map, $key) => $map[$key] ?? ucfirst(str_replace('_', ' ', (string) $key));
    @endphp
    <div class="header">
        <div class="clinic-info">
            @if ($logo)
                <img src="{{ $logo }}" height="50" style="margin-bottom: 8px;">
            @endif
            <h1>{{ $clinic->name }}</h1>
        </div>
        <div class="odo-meta">
            <h2>Odontograma #{{ $odontogram->id }}</h2>
            <p>Fecha: {{ $odontogram->date->format('d/m/Y') }}</p>
            <p>Estado: {{ $label($statusLabels, $odontogram->status) }}</p>
            @if ($odontogram->name)
                <p>{{ $odontogram->name }}</p>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Datos del Paciente</div>
        <div class="patient-card">
            <div class="row">
                <span class="label">Nombre:</span>
                <span class="value">{{ $patient->name }}</span>
            </div>
            @if ($patient->rut)
                <div class="row">
                    <span class="label">Documento:</span>
                    <span class="value">{{ $patient->rut }}</span>
                </div>
            @endif
            @if ($patient->phone)
                <div class="row">
                    <span class="label">Teléfono:</span>
                    <span class="value">{{ $patient->phone }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Diagrama Dental</div>
        @foreach ([
            ['label' => 'ARCADA SUPERIOR', 'teeth' => array_merge($upperRight, $upperLeft)],
            ['label' => 'ARCADA INFERIOR', 'teeth' => array_merge($lowerRight, $lowerLeft)],
        ] as $arch)
            <div class="arch-label">{{ $arch['label'] }}</div>
            <table class="arch">
                <tr>
                    @foreach ($arch['teeth'] as $tooth)
                        @php
                            $surfaces = $toothMap[$tooth] ?? [];
                            $surfaceColor = fn ($key) => isset($surfaces[$key]) && isset($colors[$surfaces[$key]]) ? $colors[$surfaces[$key]] : '#ffffff';
                            $isMissing = in_array($surfaces['center'] ?? null, ['missing', 'surgical_extraction', 'wisdom_tooth'], true);
                        @endphp
                        <td class="tooth-cell">
                            <div class="tooth-num">{{ $tooth }}</div>
                            <table class="tooth">
                                <tr><td colspan="3" class="s-top" style="background-color: {{ $surfaceColor('top') }};"></td></tr>
                                <tr>
                                    <td class="s-left" style="background-color: {{ $surfaceColor('left') }};"></td>
                                    <td class="s-center" style="background-color: {{ $surfaceColor('center') }};">{{ $isMissing ? 'X' : '' }}</td>
                                    <td class="s-right" style="background-color: {{ $surfaceColor('right') }};"></td>
                                </tr>
                                <tr><td colspan="3" class="s-bottom" style="background-color: {{ $surfaceColor('bottom') }};"></td></tr>
                            </table>
                        </td>
                    @endforeach
                </tr>
            </table>
        @endforeach
    </div>

    {{-- Leyenda de colores --}}
    @php $usedCodes = collect($toothMap)->flatten()->unique(); @endphp
    @if ($usedCodes->isNotEmpty())
        <div class="legend">
            <div class="legend-title">Leyenda de diagnósticos:</div>
            <div class="legend-items">
                @foreach ($usedCodes as $code)
                    @if (isset($colors[$code]))
                        <div class="legend-item">
                            <div class="legend-swatch" style="background: {{ $colors[$code] }};"></div>
                            <span>{{ $label($diagnosisLabels, $code) }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tabla de registros clínicos --}}
    @if ($odontogram->clinicalRecords->isNotEmpty())
        <div class="section" style="margin-top: 20px;">
            <div class="section-title">Registros Clínicos</div>
            <table class="records">
                <thead>
                    <tr>
                        <th>Diente</th>
                        <th>Superficie</th>
                        <th>Diagnóstico</th>
                        <th>Estado</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($odontogram->clinicalRecords as $record)
                        <tr>
                            <td>{{ $record->tooth_number }}</td>
                            <td>{{ $label($surfaceLabels, $record->surface) }}</td>
                            <td>{{ $label($diagnosisLabels, $record->diagnosis_code ?? '') }}</td>
                            <td>{{ $label($treatmentLabels, $record->treatment_status) }}</td>
                            <td>{{ $record->notes ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($odontogram->notes)
        <div style="background: #fefce8; border-left: 3px solid #eab308; padding: 10px 14px; margin-top: 16px; font-size: 11px; color: #422006;">
            <strong>Notas:</strong> {{ $odontogram->notes }}
        </div>
    @endif

    <div class="footer">
        <p>Documento generado por DentalFlow — {{ $clinic->name }}</p>
        <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
