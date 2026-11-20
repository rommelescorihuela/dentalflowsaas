<?php

namespace App\Filament\Resources\Clinics\Tables;

use App\Enums\Plan;
use App\Enums\SubscriptionStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClinicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Clínica')
                    ->weight('semibold')
                    ->description(fn ($record): string => (string) $record->id)
                    ->searchable(['name', 'id']),
                TextColumn::make('domains.domain')
                    ->label('Dominio')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Sin dominio')
                    ->limitList(1),
                TextColumn::make('plan')
                    ->label('Plan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Plan::tryFrom($state)?->label() ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Plan::Pro->value => 'success',
                        Plan::Enterprise->value => 'info',
                        Plan::Basic->value => 'primary',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('subscription_status')
                    ->label('Suscripción')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? (SubscriptionStatus::tryFrom($state)?->label() ?? $state)
                        : 'Sin suscripción')
                    ->color(fn (?string $state): string => match ($state) {
                        SubscriptionStatus::Active->value => 'success',
                        SubscriptionStatus::Trialing->value => 'info',
                        SubscriptionStatus::PastDue->value => 'warning',
                        SubscriptionStatus::Suspended->value,
                        SubscriptionStatus::Cancelled->value => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('trial_ends_at')
                    ->label('Prueba hasta')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('patients_count')
                    ->label('Pacientes')
                    ->counts('patients')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('plan')
                    ->label('Plan')
                    ->options(collect(Plan::cases())
                        ->mapWithKeys(fn ($plan) => [$plan->value => $plan->label()])
                        ->all()),
                SelectFilter::make('subscription_status')
                    ->label('Suscripción')
                    ->options([
                        SubscriptionStatus::Active->value => 'Activa',
                        SubscriptionStatus::Trialing->value => 'En prueba',
                        SubscriptionStatus::PastDue->value => 'Vencida',
                        SubscriptionStatus::Suspended->value => 'Suspendida',
                        SubscriptionStatus::Cancelled->value => 'Cancelada',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
