<?php

namespace App\Filament\App\Pages;

use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step as WizardStep;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Blade;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;

class OnboardingWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rocket-launch';

    protected string $view = 'filament.app.pages.onboarding-wizard';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = tenant();
        if ($tenant && $tenant->onboarding_step >= 4) {
            // Already completed, redirect to dashboard
            $this->redirect(route('filament.app.pages.dashboard', ['tenant' => $tenant->getRouteKey()]));
            return;
        }

        if ($tenant) {
            $this->form->fill([
                'name' => $tenant->name,
                'primary_color' => $tenant->primary_color ?? '#2563eb',
            ]);
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Wizard::make([
                    WizardStep::make('Identidad')
                        ->description('Personaliza la apariencia de tu clínica')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombre de la Clínica')
                                ->required(),
                            FileUpload::make('logo')
                                ->label('Logotipo')
                                ->image()
                                ->avatar()
                                ->directory('logos'),
                            TextInput::make('primary_color')
                                ->label('Color Primario (Hex)')
                                ->default('#2563eb'),
                        ])
                        ->afterValidation(function () {
                            $this->saveProgress(1);
                        }),
                    WizardStep::make('Configuración')
                        ->description('Moneda y zona horaria')
                        ->schema([
                            Select::make('currency')
                                ->label('Moneda Principal')
                                ->options([
                                    'USD' => 'Dólar Estadounidense (USD)',
                                    'EUR' => 'Euro (EUR)',
                                    'MXN' => 'Peso Mexicano (MXN)',
                                    'COP' => 'Peso Colombiano (COP)',
                                ])
                                ->default('USD')
                                ->required(),
                            Select::make('timezone')
                                ->label('Zona Horaria')
                                ->options([
                                    'America/New_York' => 'New York (EST)',
                                    'America/Mexico_City' => 'Mexico City (CST)',
                                    'Europe/Madrid' => 'Madrid (CET)',
                                    'America/Bogota' => 'Bogotá (COT)',
                                ])
                                ->default('America/New_York')
                                ->required(),
                            \Filament\Forms\Components\TimePicker::make('schedule_start')
                                ->label('Hora de Apertura')
                                ->default('09:00')
                                ->required(),
                            \Filament\Forms\Components\TimePicker::make('schedule_end')
                                ->label('Hora de Cierre')
                                ->default('18:00')
                                ->required(),
                        ])
                        ->afterValidation(function () {
                            $this->saveProgress(2);
                        }),
                    WizardStep::make('Listo!')
                        ->description('Todo listo para empezar')
                        ->schema([
                            Section::make()
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('success')
                                        ->label('')
                                        ->content(new \Illuminate\Support\HtmlString('<div class="text-center"><h2 class="text-xl font-bold text-success-500">¡Configuración Completada!</h2><p class="mt-2">Tu clínica está lista. Ahora puedes empezar a gestionar tus pacientes.</p></div>'))
                                ])
                        ]),
                ])
                    ->submitAction(new \Illuminate\Support\HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        type="submit"
                        size="sm"
                    >
                        Ir al Dashboard
                    </x-filament::button>
                BLADE)))
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $tenant = Filament::getTenant();

        if ($tenant) {
            // Update Tenant
            $tenant->update([
                'name' => $data['name'],
                'logo' => $data['logo'] ?? null,
                // 'primary_color' => $data['primary_color'] ?? null, // Assuming column exists or is handled
                'data' => array_merge($tenant->data ?? [], [
                    'currency' => $data['currency'] ?? 'USD',
                    'timezone' => $data['timezone'] ?? 'UTC',
                    'schedule_start' => $data['schedule_start'] ?? '09:00',
                    'schedule_end' => $data['schedule_end'] ?? '18:00',
                    'primary_color' => $data['primary_color'] ?? '#2563eb',
                ]),
                'onboarding_step' => 4 // Completed
            ]);

            Notification::make()
                ->title('¡Bienvenido a DentalFlow!')
                ->success()
                ->send();

            $this->redirect(route('filament.app.pages.dashboard', ['tenant' => $tenant]));
        } else {
             Notification::make()
                ->title('Error: No se pudo identificar la clínica.')
                ->danger()
                ->send();
            $this->redirect('/app/login'); // Redirect to login if tenant is not found
        }
    }

    protected function saveProgress(int $step): void
    {
        $tenant = Filament::getTenant();
        if ($tenant && $tenant->onboarding_step < $step) {
            $tenant->update(['onboarding_step' => $step]);
        }
    }
}