<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; font-size: 12px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0891b2; padding-bottom: 15px; margin-bottom: 20px; }
        .clinic-info h1 { font-size: 18px; color: #0891b2; margin-bottom: 4px; }
        .clinic-info p { font-size: 11px; color: #6b7280; line-height: 1.4; }
        .odo-meta { text-align: right; }
        .odo-meta h2 { font-size: 20px; color: #1f2937; }
        .odo-meta p { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 13px; font-weight: bold; color: #0891b2; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .patient-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 16px; }
        .patient-card .row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .patient-card .label { color: #6b7280; font-size: 10px; }
        .patient-card .value { font-weight: 600; }
        .odontogram-svg { text-align: center; margin: 20px 0; }
        .arch-label { font-size: 10px; color: #6b7280; text-align: center; margin: 4px 0 8px; font-weight: bold; }
        .legend { margin-top: 20px; }
        .legend-title { font-size: 11px; font-weight: bold; margin-bottom: 6px; color: #374151; }
        .legend-items { display: flex; flex-wrap: wrap; gap: 6px; }
        .legend-item { display: flex; align-items: center; gap: 4px; font-size: 9px; }
        .legend-swatch { width: 12px; height: 12px; border-radius: 2px; border: 1px solid #d1d5db; }
        table.records { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.records thead { background: #0891b2; color: white; }
        table.records th { text-align: left; padding: 6px 10px; font-size: 10px; }
        table.records td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        table.records tbody tr:nth-child(even) { background: #f9fafb; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 9px; }
        @page { margin: 20px 15px; }
    </style>
</head>
<body>
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
            <p>Estado: {{ ucfirst($odontogram->status) }}</p>
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
        <div class="odontogram-svg">
            {{-- Arcada Superior --}}
            <div class="arch-label">SUPERIOR</div>
            <svg width="640" height="70" xmlns="http://www.w3.org/2000/svg">
                @php $x = 5; @endphp
                @foreach ($upperRight as $tooth)
                    @php $surfaces = $toothMap[$tooth] ?? []; @endphp
                    @include('pdf.partials.tooth-svg', ['tooth' => $tooth, 'surfaces' => $surfaces, 'x' => $x, 'colors' => $colors])
                    @php $x += 40; @endphp
                @endforeach
                @foreach ($upperLeft as $tooth)
                    @php $surfaces = $toothMap[$tooth] ?? []; @endphp
                    @include('pdf.partials.tooth-svg', ['tooth' => $tooth, 'surfaces' => $surfaces, 'x' => $x, 'colors' => $colors])
                    @php $x += 40; @endphp
                @endforeach
            </svg>

            {{-- Arcada Inferior --}}
            <div class="arch-label" style="margin-top: 12px;">INFERIOR</div>
            <svg width="640" height="70" xmlns="http://www.w3.org/2000/svg">
                @php $x = 5; @endphp
                @foreach ($lowerRight as $tooth)
                    @php $surfaces = $toothMap[$tooth] ?? []; @endphp
                    @include('pdf.partials.tooth-svg', ['tooth' => $tooth, 'surfaces' => $surfaces, 'x' => $x, 'colors' => $colors])
                    @php $x += 40; @endphp
                @endforeach
                @foreach ($lowerLeft as $tooth)
                    @php $surfaces = $toothMap[$tooth] ?? []; @endphp
                    @include('pdf.partials.tooth-svg', ['tooth' => $tooth, 'surfaces' => $surfaces, 'x' => $x, 'colors' => $colors])
                    @php $x += 40; @endphp
                @endforeach
            </svg>
        </div>
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
                            <span>{{ ucfirst(str_replace('_', ' ', $code)) }}</span>
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
                            <td>{{ ucfirst($record->surface) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $record->diagnosis_code ?? '')) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $record->treatment_status)) }}</td>
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
