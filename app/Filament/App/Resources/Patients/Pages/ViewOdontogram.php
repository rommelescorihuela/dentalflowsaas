<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Models\Odontogram;
use Filament\Resources\Pages\Page;

class ViewOdontogram extends Page
{
    protected static string $resource = PatientResource::class;

    protected string $view = 'filament.app.resources.patients.pages.view-odontogram';

    public Odontogram $odontogram;

    public function mount(int $patient, Odontogram $odontogram): void
    {
        $this->odontogram = $odontogram;

        // Verify the odontogram belongs to this patient
        if ($this->odontogram->patient_id !== $patient) {
            abort(404);
        }
    }

    public function getTitle(): string
    {
        return $this->odontogram->name;
    }

    public function getSubheading(): string
    {
        return $this->odontogram->date->format('F j, Y');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('Back to Patient')
                ->icon('heroicon-o-arrow-left')
                ->url(fn() => PatientResource::getUrl('edit', ['record' => $this->odontogram->patient_id]))
                ->color('gray'),
            \Filament\Actions\EditAction::make()
                ->record($this->odontogram)
                ->form([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    \Filament\Forms\Components\DatePicker::make('date')
                        ->required(),
                    \Filament\Forms\Components\Select::make('status')
                        ->options([
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                        ])
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->rows(3),
                ])
                ->mutateFormDataUsing(function (array $data): array {
                    $data['tenant_id'] = auth()->user()->tenant_id;
                    return $data;
                }),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Livewire\Odontogram::make([
                'record' => $this->odontogram->patient,
                'odontogramId' => $this->odontogram->id,
            ]),
        ];
    }
}
