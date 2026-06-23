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
        .budget-meta { text-align: right; }
        .budget-meta h2 { font-size: 20px; color: #1f2937; }
        .budget-meta p { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-{{ $budget->status }} { }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 13px; font-weight: bold; color: #0891b2; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .patient-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 16px; }
        .patient-card .row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .patient-card .label { color: #6b7280; font-size: 10px; }
        .patient-card .value { font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead { background: #0891b2; color: white; }
        th { text-align: left; padding: 8px 12px; font-size: 11px; font-weight: 600; }
        th.right { text-align: right; }
        td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        td.right { text-align: right; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .total-row { background: #f0fdfa !important; font-weight: bold; font-size: 13px; }
        .total-row td { border-top: 2px solid #0891b2; border-bottom: none; padding: 12px; }
        .notes { background: #fefce8; border-left: 3px solid #eab308; padding: 10px 14px; margin-top: 16px; font-size: 11px; color: #422006; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 9px; }
        .expiry { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 4px; padding: 8px 12px; margin-top: 12px; font-size: 11px; color: #92400e; }
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
            @if ($clinic->schedule_start)
                <p>Horario: {{ $clinic->schedule_start }} - {{ $clinic->schedule_end }}</p>
            @endif
            @if ($clinic->timezone)
                <p>Zona horaria: {{ $clinic->timezone }}</p>
            @endif
        </div>
        <div class="budget-meta">
            <h2>Presupuesto #{{ $budget->id }}</h2>
            <p>Fecha: {{ $budget->created_at->format('d/m/Y') }}</p>
            <p>Estado: <span class="badge badge-{{ $budget->status }}">{{ ucfirst($budget->status) }}</span></p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Datos del Paciente</div>
        <div class="patient-card">
            <div class="row">
                <span class="label">Nombre:</span>
                <span class="value">{{ $budget->patient->name }}</span>
            </div>
            @if ($budget->patient->rut)
                <div class="row">
                    <span class="label">Documento:</span>
                    <span class="value">{{ $budget->patient->rut }}</span>
                </div>
            @endif
            @if ($budget->patient->phone)
                <div class="row">
                    <span class="label">Teléfono:</span>
                    <span class="value">{{ $budget->patient->phone }}</span>
                </div>
            @endif
            @if ($budget->patient->email)
                <div class="row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $budget->patient->email }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detalle del Tratamiento</div>
        <table>
            <thead>
                <tr>
                    <th>Tratamiento</th>
                    <th class="right" style="width: 60px;">Cant.</th>
                    <th class="right" style="width: 100px;">Precio Unit.</th>
                    <th class="right" style="width: 120px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($budget->items as $item)
                    <tr>
                        <td>{{ $item->treatment_name }}</td>
                        <td class="right">{{ $item->quantity }}</td>
                        <td class="right">{{ $currencySymbol }}{{ number_format($item->cost, 2, ',', '.') }}</td>
                        <td class="right">{{ $currencySymbol }}{{ number_format($item->cost * $item->quantity, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="right">TOTAL:</td>
                    <td class="right">{{ $currencySymbol }}{{ number_format($budget->total, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if ($budget->notes)
        <div class="notes">
            <strong>Notas:</strong> {{ $budget->notes }}
        </div>
    @endif

    @if ($budget->expires_at)
        <div class="expiry">
            <strong>Validez:</strong> Este presupuesto es válido hasta el {{ $budget->expires_at->format('d/m/Y') }}.
        </div>
    @endif

    <div class="footer">
        <p>Documento generado por DentalFlow — {{ $clinic->name }}</p>
        <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
