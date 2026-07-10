<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientMedicalHistory extends Model
{
    use BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'antecedentes_personales',
        'antecedentes_familiares',
        'alergias',
        'medicamentos_actuales',
        'enfermedades_cronicas',
        'habitos',
        'cirugias_previas',
        'historia_dental',
        'presion_arterial',
        'frecuencia_cardiaca',
        'peso',
        'altura',
        'grupo_sanguineo',
        'firma_paciente',
        'fecha_firma',
        'nombre_testigo',
        'rut_testigo',
        'observaciones',
    ];

    protected $casts = [
        'fecha_firma' => 'datetime',
        'peso' => 'decimal:2',
        'altura' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
