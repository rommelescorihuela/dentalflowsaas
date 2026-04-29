<?php

namespace App\Livewire;

use App\Models\ClinicalRecord;
use App\Models\Patient;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class Odontogram extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'livewire.odontogram-v2';

    protected int|string|array $columnSpan = 'full';

    public ?Model $record = null;
    public ?int $odontogramId = null; // NEW: specific odontogram session
    public ?int $selectedTooth = null;
    public array $selectedSurfaces = [];

    // Tooth state: [tooth_number => [surface => status]]
    public array $toothMap = [];

    // Colors mapping
    public array $statusColors = [
        // Core odontogram colors
        'caries' => '#ef4444', // Red
        'filled' => '#3b82f6', // Blue
        'endodontic' => '#eab308', // Yellow
        'endodontic_multi' => '#eab308',
        'endo_retreatment' => '#eab308',
        'missing' => '#1f2937', // Black
        'healthy' => '#ffffff', // White
        'crown' => '#a855f7', // Purple
        // Additional procedure colors
        'prophylaxis' => '#22c55e', // Green
        'sealant' => '#14b8a6', // Teal
        'fluoride' => '#06b6d4', // Cyan
        'inlay' => '#6366f1', // Indigo
        'scaling' => '#84cc16', // Lime
        'gingivectomy' => '#f97316', // Orange
        'gingival_contouring' => '#f97316',
        'flap_surgery' => '#dc2626', // Dark red
        'surgical_extraction' => '#1f2937',
        'wisdom_tooth' => '#1f2937',
        'apicoectomy' => '#991b1b',
        'frenectomy' => '#f59e0b',
        'implant' => '#6b7280', // Gray
        'implant_crown' => '#9ca3af',
        'sinus_lift' => '#4b5563',
        'braces_metal' => '#a78bfa', // Light purple
        'braces_aesthetic' => '#c4b5fd',
        'ortho_adjustment' => '#d8b4fe',
        'retainer_fixed' => '#8b5cf6',
        'retainer_removable' => '#7c3aed',
        'crown_pfm' => '#a855f7',
        'crown_zirconia' => '#9333ea',
        'bridge' => '#7e22ce',
        'partial_denture' => '#6b21a8',
        'full_denture' => '#581c87',
        'denture_rebase' => '#4c1d95',
        'whitening' => '#f0f9ff', // Light blue/white
        'veneer_composite' => '#bae6fd',
        'veneer_ceramic' => '#7dd3fc',
        'ss_crown' => '#94a3b8',
        'pulpotomy' => '#fb923c',
        'space_maintainer' => '#fbbf24',
        'consultation' => '#e2e8f0',
        'xray_periapical' => '#cbd5e1',
        'xray_panoramic' => '#94a3b8',
        'cbct' => '#64748b',
    ];

    public array $upperTeethRight = [18, 17, 16, 15, 14, 13, 12, 11];
    public array $upperTeethLeft = [21, 22, 23, 24, 25, 26, 27, 28];
    public array $lowerTeethRight = [48, 47, 46, 45, 44, 43, 42, 41];
    public array $lowerTeethLeft = [31, 32, 33, 34, 35, 36, 37, 38];

    public function mount(?Model $record = null, ?int $odontogramId = null)
    {
        $this->record = $record;
        $this->odontogramId = $odontogramId;
        $this->loadTreatments();
        $this->form->fill();
    }

    public function loadTreatments()
    {
        $query = ClinicalRecord::where('patient_id', $this->record->id);

        // Filter by odontogram if specified
        if ($this->odontogramId) {
            $query->where('odontogram_id', $this->odontogramId);
        }

        $records = $query->get();

        foreach ($records as $rec) {
            $this->toothMap[$rec->tooth_number][$rec->surface] = $rec->diagnosis_code;
        }
    }

    public function getSurfaces(int $tooth)
    {
        $defaults = [
            'top' => null,
            'bottom' => null,
            'left' => null,
            'right' => null,
            'center' => null,
            'root' => null,
        ];

        return array_merge($defaults, $this->toothMap[$tooth] ?? []);
    }

    public function selectSurface(int $tooth, string $surface)
    {
        // If selecting a different tooth, reset everything
        if ($this->selectedTooth !== $tooth) {
            $this->selectedTooth = $tooth;
            $this->selectedSurfaces = [];
        }

        // Toggle surface selection
        if (in_array($surface, $this->selectedSurfaces)) {
            $this->selectedSurfaces = array_values(array_diff($this->selectedSurfaces, [$surface]));
            // If no surfaces left, clear tooth selection? 
            if (empty($this->selectedSurfaces)) {
                $this->selectedTooth = null;
            }
        } else {
            $this->selectedSurfaces[] = $surface;
        }

        // Form Logic
        // If single selection, try to load existing data
        if (count($this->selectedSurfaces) === 1) {
            $existing = ClinicalRecord::where('clinic_id', tenant('id'))
                ->where('patient_id', $this->record->id)
                ->where('tooth_number', $this->selectedTooth)
                ->where('surface', $this->selectedSurfaces[0])
                ->first();

            if ($existing) {
                $this->form->fill([
                    'surfaces' => $this->selectedSurfaces,
                    'procedure_price_id' => $existing->procedure_price_id,
                    'diagnosis_code' => $existing->diagnosis_code,
                    'treatment_status' => $existing->treatment_status,
                    'notes' => $existing->notes,
                ]);
            } else {
                $this->form->fill([
                    'surfaces' => $this->selectedSurfaces,
                    'procedure_price_id' => null,
                    'diagnosis_code' => null,
                    'treatment_status' => 'planned',
                    'notes' => null,
                ]);
            }
        } else {
            // Multiple selection or empty: fill with current selection
            $this->form->fill([
                'surfaces' => $this->selectedSurfaces,
                'procedure_price_id' => null,
                'diagnosis_code' => null,
                'treatment_status' => 'planned',
                'notes' => null,
            ]);
        }
    }

    public ?array $data = []; // Keep using array but initializing properly

    public function form(Schema $schema): Schema
    {
        // Fetch procedures from CRUD to populate options dynamically
        $clinicId = tenant('id');
        $procedures = $clinicId ? \App\Models\ProcedurePrice::where('clinic_id', $clinicId)->get() : collect();

        $options = [];

        foreach ($procedures as $proc) {
            // Use ID as key to show ALL procedures
            $label = $proc->procedure_name;
            if ($proc->price) {
                $label .= ' ($' . number_format($proc->price, 0) . ')';
            }
            $options[$proc->id] = $label;
        }

        // Fallback if no procedures are defined in CRUD
        if (empty($options)) {
            $options = [
                'caries' => 'Caries (Rojo)',
                'filled' => 'Restauración (Azul)',
                'endodontic' => 'Endodoncia (Amarillo)',
                'missing' => 'Ausente (Negro)',
                'crown' => 'Corona (Morado)',
                'healthy' => 'Sano (Blanco)',
            ];
        }

        return $schema
            ->components([
                Forms\Components\CheckboxList::make('surfaces')
                    ->label('Selected Surfaces')
                    ->options([
                        'top' => 'Top (Vestibular)',
                        'bottom' => 'Bottom (Lingual)',
                        'left' => 'Left (Mesial)',
                        'right' => 'Right (Distal)',
                        'center' => 'Center (Occlusal)',
                        'root' => 'Root (Apical)',
                    ])
                    ->columns(3)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->selectedSurfaces = $state;
                    }),
                Forms\Components\Select::make('procedure_price_id')
                    ->label('Procedure / Diagnosis')
                    ->options($options)
                    ->required()
                    ->live(),
                Forms\Components\Select::make('treatment_status')
                    ->options([
                        'planned' => 'Planned',
                        'completed' => 'Completed',
                        'existing' => 'Existing',
                    ])
                    ->required()
                    ->default('planned'),
                Forms\Components\Textarea::make('notes')
                    ->rows(2),
            ])
            ->statePath('data');
    }

    public function saveRecord()
    {
        $data = $this->form->getState();
        $surfaces = $data['surfaces'] ?? [];

        if (!$this->selectedTooth || empty($surfaces)) {
            return;
        }

        // Resolve diagnosis_code from procedure_price_id if using CRUD procedures
        $diagnosisCode = $data['diagnosis_code'] ?? null;
        $procedurePriceId = $data['procedure_price_id'] ?? null;

        if (is_numeric($procedurePriceId)) {
            $proc = \App\Models\ProcedurePrice::find($procedurePriceId);
            if ($proc) {
                $diagnosisCode = $proc->diagnosis_code;
            }
        }

        // Fallback for legacy/hardcoded options
        if (!$diagnosisCode && isset($data['diagnosis_code'])) {
            $diagnosisCode = $data['diagnosis_code'];
        }

        foreach ($surfaces as $surface) {
            ClinicalRecord::updateOrCreate(
                [
                    'clinic_id' => Auth::user()->clinic_id,
                    'patient_id' => $this->record->id,
                    'tooth_number' => $this->selectedTooth,
                    'surface' => $surface,
                    'odontogram_id' => $this->odontogramId,
                ],
                [
                    'procedure_price_id' => is_numeric($procedurePriceId) ? $procedurePriceId : null,
                    'diagnosis_code' => $diagnosisCode,
                    'treatment_status' => $data['treatment_status'],
                    'notes' => $data['notes'],
                ]
            );

            // Update local map for reactivity
            $this->toothMap[$this->selectedTooth][$surface] = $diagnosisCode;
        }

        $this->dispatch('record-saved');

        \Filament\Notifications\Notification::make()
            ->title('Record Saved')
            ->success()
            ->send();

        $this->selectedTooth = null;
        $this->selectedSurfaces = [];
    }

    public function deleteRecord($tooth, $surface)
    {
        ClinicalRecord::where('clinic_id', tenant('id'))
            ->where('patient_id', $this->record->id)
            ->where('tooth_number', $tooth)
            ->where('surface', $surface)
            ->delete();

        unset($this->toothMap[$tooth][$surface]);
    }
}
