<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Models\Odontogram;
use Filament\Resources\Pages\Page;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ViewOdontogram extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PatientResource::class;

    protected string $view = 'filament.app.resources.patients.pages.view-odontogram';

    public Odontogram $odontogram;

    public ?array $data = [];

    public function mount(int $patient, Odontogram $odontogram): void
    {
        $this->odontogram = $odontogram;

        // Verify the odontogram belongs to this patient
        if ($this->odontogram->patient_id !== $patient) {
            abort(404);
        }

        $this->form->fill($this->odontogram->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                \Filament\Schemas\Components\Section::make('Odontogram Details')
                    ->columns(2)
                    ->schema([
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
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                \Filament\Schemas\Components\Section::make('Odontogram')
                    ->schema([
                        \Filament\Schemas\Components\View::make('filament.app.resources.patients.pages.components.odontogram-embed')
                            ->viewData([
                                'patient' => $this->odontogram->patient,
                                'odontogramId' => $this->odontogram->id,
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data')
            ->model($this->odontogram);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->odontogram->update($data);

        Notification::make()
            ->success()
            ->title('Odontogram saved')
            ->send();
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
            Action::make('save')
                ->label('Save Changes')
                ->action('save')
                ->color('primary'),
        ];
    }
}
