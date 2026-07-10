<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Clinic;
use App\Models\Inventory;
use App\Models\ProcedurePrice;
use App\Models\User;
use App\Scopes\ClinicScope;
use Database\Seeders\InventorySeeder;
use Database\Seeders\ProcedureInventorySeeder;
use Database\Seeders\ProcedurePriceSeeder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class OnboardingWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rocket-launch';

    public function getHeadingIcon(): \BackedEnum|string|null
    {
        return null;
    }

    protected string $view = 'filament.app.pages.onboarding-wizard';

    protected static ?string $navigationLabel = 'Configuración inicial';

    protected static ?string $title = 'Bienvenido a DentalFlow — Configuremos tu clínica';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = -100;

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = $this->resolveTenant();

        if ($tenant) {
            $this->form->fill([
                'clinic_name' => $tenant->name,
                'logo' => $tenant->logo,
                'currency' => $tenant->currency ?: 'USD',
                'timezone' => $tenant->timezone ?: 'America/Caracas',
                'schedule_start' => $tenant->schedule_start ?: '09:00',
                'schedule_end' => $tenant->schedule_end ?: '18:00',
                'import_procedures' => true,
                'import_inventory' => true,
                'doctor_name' => '',
                'doctor_email' => '',
                'assistant_name' => '',
                'assistant_email' => '',
            ]);
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Datos de tu clínica')
                        ->icon('heroicon-o-building-office-2')
                        ->description('Identidad y configuración básica')
                        ->schema([
                            TextInput::make('clinic_name')
                                ->label('Nombre de la clínica')
                                ->required(),
                            FileUpload::make('logo')
                                ->label('Logotipo (opcional)')
                                ->image()
                                ->disk('public')
                                ->directory('logos')
                                ->visibility('public'),
                            Select::make('currency')
                                ->label('Moneda')
                                ->options([
                                    'USD' => 'Dólar (USD)',
                                    'Bs' => 'Bolívares (Bs)',
                                    'EUR' => 'Euro (EUR)',
                                ])
                                ->required(),
                            Select::make('timezone')
                                ->label('Zona horaria')
                                ->options([
                                    'America/Caracas' => 'Caracas (VET)',
                                    'America/Bogota' => 'Bogotá (COT)',
                                    'America/Santiago' => 'Santiago (CLT)',
                                    'America/Mexico_City' => 'Ciudad de México (CST)',
                                ])
                                ->required(),
                            TimePicker::make('schedule_start')
                                ->label('Hora de apertura')
                                ->required(),
                            TimePicker::make('schedule_end')
                                ->label('Hora de cierre')
                                ->required(),
                        ])->columns(2),

                    Step::make('Precios de procedimientos')
                        ->icon('heroicon-o-currency-dollar')
                        ->description('Configura tu catálogo de tratamientos')
                        ->schema([
                            Select::make('import_procedures')
                                ->label('¿Cómo quieres configurar tus procedimientos?')
                                ->options([
                                    '1' => 'Importar plantilla (47 procedimientos predefinidos) — Recomendado',
                                    '0' => 'Los configuraré manualmente después',
                                ])
                                ->default('1')
                                ->required(),
                        ]),
                    Step::make('Inventario inicial')
                        ->icon('heroicon-o-archive-box')
                        ->description('Carga tu stock de materiales')
                        ->schema([
                            Select::make('import_inventory')
                                ->label('¿Cómo quieres configurar tu inventario?')
                                ->options([
                                    '1' => 'Importar plantilla (95 items predefinidos) — Recomendado',
                                    '0' => 'Lo configuraré manualmente después',
                                ])
                                ->default('1')
                                ->required(),
                        ]),
                    Step::make('Tu equipo')
                        ->icon('heroicon-o-users')
                        ->description('Invita a tu doctor y asistente')
                        ->schema([
                            TextInput::make('doctor_name')
                                ->label('Nombre del doctor/a')
                                ->placeholder('Dr. Juan Pérez'),
                            TextInput::make('doctor_email')
                                ->label('Email del doctor/a')
                                ->email()
                                ->placeholder('doctor@miclinica.com'),
                            TextInput::make('assistant_name')
                                ->label('Nombre del asistente')
                                ->placeholder('María González'),
                            TextInput::make('assistant_email')
                                ->label('Email del asistente')
                                ->email()
                                ->placeholder('asistente@miclinica.com'),
                        ])->columns(2),
                ])
                    ->submitAction(new HtmlString('<button type="submit" class="fi-btn fi-btn-size-md fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm rounded-lg bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2">Finalizar configuración</button>'))
                    ->persistStepInQueryString(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $tenant = $this->resolveTenant();

        if (! $tenant) {
            Notification::make()->title('Error: clínica no identificada')->danger()->send();

            return;
        }

        $clinic = Clinic::withoutGlobalScope(ClinicScope::class)->find($tenant->id);

        if (! $clinic) {
            Notification::make()->title('Error: clínica no encontrada')->danger()->send();

            return;
        }

        $clinic->name = $data['clinic_name'];

        // Setear atributos individuales — VirtualColumn los codifica en 'data' al hacer save().
        // NO usar $clinic->data = [...] porque encodeAttributes() sobreescribe data con solo
        // los atributos fuera de getCustomColumns(), perdiendo lo puesto directamente en data.
        $clinic->logo = $data['logo'] ?? null;
        $clinic->currency = $data['currency'];
        $clinic->timezone = $data['timezone'];
        $clinic->schedule_start = $data['schedule_start'];
        $clinic->schedule_end = $data['schedule_end'];
        $clinic->onboarding_step = 4;
        $clinic->onboarding_completed_at = now()->toIso8601String();
        $clinic->save();

        if (in_array($data['import_procedures'] ?? '1', ['1', true, 'true'], true)) {
            $this->importProcedures($clinic->id);
        }

        if (in_array($data['import_inventory'] ?? '1', ['1', true, 'true'], true)) {
            $this->importInventory($clinic->id);
        }

        if (! empty($data['doctor_email'])) {
            $this->createTeamMember($clinic->id, $data['doctor_name'], $data['doctor_email'], 'doctor');
        }

        if (! empty($data['assistant_email'])) {
            $this->createTeamMember($clinic->id, $data['assistant_name'], $data['assistant_email'], 'assistant');
        }

        Notification::make()
            ->title('¡Configuración completada!')
            ->body('Tu clínica está lista para usar. Ya puedes empezar a registrar pacientes.')
            ->success()
            ->send();

        $this->redirect('/app', navigate: false);
    }

    protected function importProcedures(string $clinicId): void
    {
        $existing = ProcedurePrice::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->count();

        if ($existing > 0) {
            return;
        }

        $seeder = new ProcedurePriceSeeder;
        $seeder->run();
    }

    protected function importInventory(string $clinicId): void
    {
        $existing = Inventory::withoutGlobalScope(ClinicScope::class)
            ->where('clinic_id', $clinicId)
            ->count();

        if ($existing > 0) {
            return;
        }

        $seeder = new InventorySeeder;
        $seeder->run();

        $procInvSeeder = new ProcedureInventorySeeder;
        $procInvSeeder->run();
    }

    protected function createTeamMember(string $clinicId, ?string $name, string $email, string $role): void
    {
        if (User::where('email', $email)->exists()) {
            return;
        }

        $user = User::create([
            'name' => $name ?: $email,
            'email' => $email,
            'password' => Hash::make(Str::random(16)),
            'clinic_id' => $clinicId,
        ]);

        $user->assignRole($role);
    }

    protected function resolveTenant(): ?object
    {
        $tenant = tenant();

        if ($tenant) {
            return $tenant;
        }

        $user = auth()->user();

        if ($user && $user->clinic_id) {
            $tenantModel = config('tenancy.tenant_model');

            $found = $tenantModel::find($user->clinic_id);

            if ($found) {
                tenancy()->initialize($found);

                return $found;
            }
        }

        return null;
    }
}
