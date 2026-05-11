<?php

namespace App\Filament\App\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClinicSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected string $view = 'filament.app.pages.clinic-settings';
    protected static string | \UnitEnum | null $navigationGroup = 'Configuración';
    protected static ?string $navigationLabel = 'Mi Clínica';
    protected static ?string $title = 'Configuración de la Clínica';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = $this->resolveTenant();

        if ($tenant) {
            $tenantData = is_array($tenant->data)
                ? $tenant->data
                : json_decode($tenant->data ?? '{}', true);

            $this->form->fill([
                'name' => $tenant->name,
                'logo' => $tenantData['logo'] ?? null,
                'primary_color' => $tenantData['primary_color'] ?? '#2563eb',
                'currency' => $tenantData['currency'] ?? 'USD',
                'timezone' => $tenantData['timezone'] ?? 'America/New_York',
                'schedule_start' => $tenantData['schedule_start'] ?? '09:00',
                'schedule_end' => $tenantData['schedule_end'] ?? '18:00',
            ]);
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Identidad')
                    ->description('Personaliza la apariencia de tu clínica')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre de la Clínica')
                            ->required(),
                        FileUpload::make('logo')
                            ->label('Logotipo')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('logos')
                            ->visibility('public'),
                        TextInput::make('primary_color')
                            ->label('Color Primario (Hex)')
                            ->default('#2563eb'),
                    ])->columns(2),

                Section::make('Configuración Regional')
                    ->description('Moneda y zona horaria')
                    ->schema([
                        Select::make('currency')
                            ->label('Moneda Principal')
                            ->options([
                                'USD' => 'Dólar Estadounidense (USD)',
                                'EUR' => 'Euro (EUR)',
                                'MXN' => 'Peso Mexicano (MXN)',
                                'COP' => 'Peso Colombiano (COP)',
                            ])->required(),
                        Select::make('timezone')
                            ->label('Zona Horaria')
                            ->options([
                                'America/New_York' => 'New York (EST)',
                                'America/Mexico_City' => 'Mexico City (CST)',
                                'Europe/Madrid' => 'Madrid (CET)',
                                'America/Bogota' => 'Bogotá (COT)',
                            ])->required(),
                    ])->columns(2),

                Section::make('Horario de Atención')
                    ->schema([
                        TimePicker::make('schedule_start')
                            ->label('Hora de Apertura')
                            ->required(),
                        TimePicker::make('schedule_end')
                            ->label('Hora de Cierre')
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        Log::info('ClinicSettings::save() called');

        $data = $this->form->getState();

        $tenant = $this->resolveTenant();

        if (!$tenant) {
            Log::error('ClinicSettings::save() - tenant is null');
            Notification::make()
                ->title('Error: clínica no identificada')
                ->body('No se pudo determinar la clínica actual. Recarga la página e intenta de nuevo.')
                ->danger()
                ->send();
            return;
        }

        Log::info('ClinicSettings::save() - tenant found: ' . $tenant->id);

        $existingData = is_array($tenant->data)
            ? $tenant->data
            : json_decode($tenant->data ?? '{}', true);

        try {
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update([
                    'name' => $data['name'],
                    'data' => json_encode(array_merge($existingData, [
                        'logo' => $data['logo'],
                        'currency' => $data['currency'],
                        'timezone' => $data['timezone'],
                        'schedule_start' => $data['schedule_start'],
                        'schedule_end' => $data['schedule_end'],
                        'primary_color' => $data['primary_color'],
                        'onboarding_step' => 4,
                    ]))
                ]);

            Notification::make()
                ->title('Configuración guardada exitosamente')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Log::error('ClinicSettings::save() DB error: ' . $e->getMessage());
            Notification::make()
                ->title('Error al guardar')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Resolve the current tenant reliably.
     * Uses tenant() helper first, falls back to subdomain lookup.
     */
    private function resolveTenant(): ?object
    {
        // Try the standard helper first
        $tenant = tenant();
        if ($tenant) {
            return $tenant;
        }

        // Fallback: resolve from the current request's subdomain
        $host = request()->getHost();
        $subdomain = explode('.', $host)[0];

        if ($subdomain && $subdomain !== 'localhost' && $subdomain !== '127') {
            $tenantModel = config('tenancy.tenant_model');
            $found = $tenantModel::find($subdomain);

            if ($found) {
                // Re-initialize tenancy so tenant() works for the rest of the request
                tenancy()->initialize($found);
                return $found;
            }
        }

        return null;
    }
}
