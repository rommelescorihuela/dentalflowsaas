<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Enums\Plan;
use App\Enums\SubscriptionStatus;
use App\Models\SubscriptionPayment;
use App\Services\PlanLimits;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class Billing extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected string $view = 'filament.app.pages.billing';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Facturación';

    protected static ?string $title = 'Suscripción y Facturación';

    public ?array $paymentData = [];

    public function mount(): void
    {
        $this->paymentForm->fill();
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

    public function paymentForm(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Subir comprobante de pago')
                    ->description('Registra tu pago mensual para mantener tu suscripción activa')
                    ->schema([
                        Select::make('plan')
                            ->label('Plan a contratar')
                            ->options(collect([Plan::Basic, Plan::Pro])->mapWithKeys(fn (Plan $p) => [$p->value => $p->label().' — $'.number_format($p->priceUsd(), 0).'/mes'])->toArray())
                            ->required()
                            ->live(),
                        Select::make('method')
                            ->label('Método de pago')
                            ->options([
                                'bank_transfer_bs' => 'Transferencia bancaria (Bs)',
                                'pago_movil_bs' => 'Pago móvil (Bs)',
                                'zelle_usd' => 'Zelle (USD)',
                                'binance_usdt' => 'Binance / USDT',
                            ])
                            ->required()
                            ->live(),
                        TextInput::make('reference')
                            ->label(fn ($get) => match ($get('method')) {
                                'bank_transfer_bs' => 'Número de operación',
                                'pago_movil_bs' => 'Número de referencia',
                                'zelle_usd' => 'Email/Teléfono Zelle + # confirmación',
                                'binance_usdt' => 'TX Hash',
                                default => 'Referencia',
                            })
                            ->required(),
                        TextInput::make('amount')
                            ->label('Monto pagado')
                            ->numeric()
                            ->required(),
                        Select::make('currency')
                            ->label('Moneda')
                            ->options([
                                'USD' => 'USD',
                                'Bs' => 'Bolívares (Bs)',
                                'USDT' => 'USDT',
                            ])
                            ->default('USD')
                            ->required(),
                        DatePicker::make('paid_at')
                            ->label('Fecha del pago')
                            ->default(now())
                            ->required(),
                        FileUpload::make('proof_path')
                            ->label('Comprobante (imagen/PDF)')
                            ->disk('local')
                            ->directory('payments')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->maxSize(5120)
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('paymentData');
    }

    public function submitPayment(): void
    {
        $data = $this->paymentForm->getState();

        $clinic = $this->resolveTenant();

        if (! $clinic) {
            Notification::make()->title('Error: clínica no identificada')->danger()->send();

            return;
        }

        $subscription = $clinic->subscription;

        SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'subscription_id' => $subscription?->id,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'method' => $data['method'],
            'status' => 'pending',
            'reference' => $data['reference'],
            'proof_path' => $data['proof_path'],
            'paid_at' => $data['paid_at'],
        ]);

        Notification::make()
            ->title('Comprobante enviado')
            ->body('Tu pago está en revisión. Te notificaremos al ser aprobado.')
            ->success()
            ->send();
    }

    public function getSubscriptionStatusProperty(): ?SubscriptionStatus
    {
        $clinic = $this->resolveTenant();

        if (! $clinic || ! $clinic->subscription) {
            return null;
        }

        return $clinic->subscription->status;
    }

    public function getPlanLabelProperty(): string
    {
        $clinic = $this->resolveTenant();

        if (! $clinic) {
            return '—';
        }

        $planLimits = app(PlanLimits::class);

        return $planLimits->effectivePlan($clinic)->label();
    }
}
