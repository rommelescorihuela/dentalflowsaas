<?php

namespace App\Filament\App\Pages;

use App\Models\ClinicSetting;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClinicSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Configuración de Clínica';

    protected static ?string $title = 'Configuración de Clínica';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected string $view = 'filament.app.pages.clinic-settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = ClinicSetting::first();
        $this->form->fill($setting?->toArray() ?? []);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Tema')
                    ->schema([
                        Forms\Components\ColorPicker::make('primary_color')
                            ->label('Color Primario')
                            ->default('#06b6d4'),
                        Forms\Components\ColorPicker::make('secondary_color')
                            ->label('Color Secundario')
                            ->default('#0891b2'),
                        Forms\Components\ColorPicker::make('accent_color')
                            ->label('Color de Acento')
                            ->default('#0e7490'),
                        Forms\Components\Toggle::make('dark_mode')
                            ->label('Modo Oscuro')
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make('Landing Page')
                    ->schema([
                        Forms\Components\Toggle::make('landing_enabled')
                            ->label('Habilitar Landing Page')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('landing_title')
                            ->label('Título')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('landing_description')
                            ->label('Descripción')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('landing_logo')
                            ->label('Logo')
                            ->image()
                            ->directory('clinic/logos'),
                        Forms\Components\FileUpload::make('landing_hero_image')
                            ->label('Imagen Hero')
                            ->image()
                            ->directory('clinic/hero'),
                        Forms\Components\TextInput::make('landing_phone')
                            ->label('Teléfono')
                            ->tel(),
                        Forms\Components\TextInput::make('landing_email')
                            ->label('Email')
                            ->email(),
                        Forms\Components\TextInput::make('landing_address')
                            ->label('Dirección')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('landing_facebook')
                            ->label('Facebook URL')
                            ->url(),
                        Forms\Components\TextInput::make('landing_instagram')
                            ->label('Instagram URL')
                            ->url(),
                        Forms\Components\TextInput::make('landing_whatsapp')
                            ->label('WhatsApp')
                            ->tel(),
                    ])
                    ->columns(2),

                Section::make('Notificaciones')
                    ->schema([
                        Forms\Components\Toggle::make('email_notifications')
                            ->label('Notificaciones por Email')
                            ->default(true),
                        Forms\Components\Toggle::make('appointment_reminders')
                            ->label('Recordatorios de Citas')
                            ->default(true),
                        Forms\Components\TextInput::make('reminder_hours_before')
                            ->label('Horas antes de la cita')
                            ->numeric()
                            ->default(24)
                            ->minValue(1)
                            ->maxValue(72),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        ClinicSetting::updateOrCreate(
            ['clinic_id' => tenant()->getTenantKey()],
            $data
        );

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }

    public function getFormActions(): array
    {
        return [
            Forms\Actions\Action::make('save')
                ->label('Guardar')
                ->submit('save'),
        ];
    }
}
