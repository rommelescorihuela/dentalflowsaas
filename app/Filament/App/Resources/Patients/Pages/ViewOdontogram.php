<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Filament\App\Resources\Budgets\BudgetResource;
use App\Filament\App\Resources\Patients\PatientResource;
use App\Helpers\ClinicHelper;
use App\Models\Budget;
use App\Models\Odontogram;
use App\Services\BudgetGenerator;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class ViewOdontogram extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PatientResource::class;

    protected string $view = 'filament.app.resources.patients.pages.view-odontogram';

    public Odontogram $odontogram;

    public ?Budget $budget = null;

    public ?array $data = [];

    public function mount(int $patient, Odontogram $odontogram): void
    {
        $this->odontogram = $odontogram;

        if ($this->odontogram->patient_id !== $patient) {
            abort(404);
        }

        $this->loadBudget();
        $this->form->fill($this->odontogram->attributesToArray());
    }

    protected function loadBudget(): void
    {
        $this->budget = Budget::where('odontogram_id', $this->odontogram->id)->first();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Detalles del Odontograma')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('date')
                            ->required(),
                        Select::make('status')
                            ->options([
                                'in_progress' => 'En Progreso',
                                'completed' => 'Completado',
                            ])
                            ->required()
                            ->hint('Cambia a "Completado" para generar un presupuesto automaticamente'),
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Odontograma')
                    ->schema([
                        View::make('filament.app.resources.patients.pages.components.odontogram-embed')
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

        $this->budget = Budget::where('odontogram_id', $this->odontogram->id)->first();

        Notification::make()
            ->success()
            ->title('Odontograma guardado')
            ->send();

        $this->js('window.location.reload()');
    }

    public function getTitle(): string
    {
        return $this->odontogram->name;
    }

    public function getSubheading(): string
    {
        return Carbon::parse($this->odontogram->date)->locale('es')->isoFormat('D [de] MMMM, Y');
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            Action::make('back')
                ->label('Volver al Paciente')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => PatientResource::getUrl('edit', ['record' => $this->odontogram->patient_id]))
                ->color('gray'),
            Action::make('save')
                ->label('Guardar Cambios')
                ->action('save')
                ->color('primary'),
        ];

        if ($this->odontogram->status === 'completed') {
            if ($this->budget) {
                $actions[] = Action::make('view_budget')
                    ->label('Ver Presupuesto #'.$this->budget->id)
                    ->icon('heroicon-o-document-currency-dollar')
                    ->color('success')
                    ->url(fn () => BudgetResource::getUrl('edit', ['record' => $this->budget->id]));
            } else {
                $actions[] = Action::make('generate_budget')
                    ->label('Generar Presupuesto')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (BudgetGenerator $generator) {
                        $budget = $generator->generate($this->odontogram);
                        if ($budget) {
                            $this->budget = $budget;
                            Notification::make()
                                ->success()
                                ->title('Presupuesto generado')
                                ->body('Presupuesto #'.$budget->id.' creado por '.ClinicHelper::formatMoneyShort($budget->total))
                                ->send();
                        }
                    });
            }
        }

        return $actions;
    }
}
