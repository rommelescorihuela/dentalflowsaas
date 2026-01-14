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

    protected string $view = 'livewire.odontogram';

    public ?Model $record = null;
    public ?int $odontogramId = null; // NEW: specific odontogram session
    public ?int $selectedTooth = null;
    public array $selectedSurfaces = [];

    // Tooth state: [tooth_number => [surface => status]]
    public array $toothMap = [];

    // Colors mapping
    public array $statusColors = [
        'caries' => '#ef4444', // Red
        'filled' => '#3b82f6', // Blue
        'endodontic' => '#eab308', // Yellow
        'missing' => '#1f2937', // Black
        'healthy' => '#ffffff', // White
        'crown' => '#a855f7', // Purple
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
            $existing = ClinicalRecord::where('patient_id', $this->record->id)
                ->where('tooth_number', $this->selectedTooth)
                ->where('surface', $this->selectedSurfaces[0])
                ->first();

            if ($existing) {
                $this->form->fill([
                    'diagnosis_code' => $existing->diagnosis_code,
                    'treatment_status' => $existing->treatment_status,
                    'notes' => $existing->notes,
                ]);
            } else {
                $this->form->fill([
                    'diagnosis_code' => null,
                    'treatment_status' => 'planned',
                    'notes' => null,
                ]);
            }
        } elseif (count($this->selectedSurfaces) > 1) {
            // Multiple selection: clear form for batch entry
            $this->form->fill([
                'diagnosis_code' => null,
                'treatment_status' => 'planned',
                'notes' => null,
            ]);
        }
    }

    public ?array $data = []; // Keep using array but initializing properly

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('diagnosis_code')
                    ->label('Diagnosis / Status')
                    ->options([
                        'caries' => 'Caries (Red)',
                        'filled' => 'Restoration (Blue)',
                        'endodontic' => 'Endodontic (Yellow)',
                        'missing' => 'Missing (Black)',
                        'crown' => 'Crown (Purple)',
                        'healthy' => 'Healthy (Clear)',
                    ])
                    ->required()
                    ->live(), // Ensure updates are sent immediately
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

        if (!$this->selectedTooth || empty($this->selectedSurfaces)) {
            return;
        }

        foreach ($this->selectedSurfaces as $surface) {
            ClinicalRecord::updateOrCreate(
                [
                    'tenant_id' => Auth::user()->tenant_id,
                    'patient_id' => $this->record->id,
                    'tooth_number' => $this->selectedTooth,
                    'surface' => $surface,
                    'odontogram_id' => $this->odontogramId,
                ],
                [
                    'diagnosis_code' => $data['diagnosis_code'],
                    'treatment_status' => $data['treatment_status'],
                    'notes' => $data['notes'],
                ]
            );

            // Update local map for reactivity
            $this->toothMap[$this->selectedTooth][$surface] = $data['diagnosis_code'];
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
        ClinicalRecord::where('patient_id', $this->record->id)
            ->where('tooth_number', $tooth)
            ->where('surface', $surface)
            ->delete();

        unset($this->toothMap[$tooth][$surface]);
    }
}
