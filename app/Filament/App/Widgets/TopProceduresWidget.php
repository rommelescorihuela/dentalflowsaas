<?php

namespace App\Filament\App\Widgets;

use App\Models\Clinic;
use App\Models\ClinicalRecord;
use App\Services\PlanLimits;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopProceduresWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = '1/2';

    protected static ?string $heading = 'Procedimientos Más Frecuentes';

    public static function canView(): bool
    {
        $tenant = tenant() ?? (Clinic::find(auth()->user()?->clinic_id));

        if (! $tenant) {
            return false;
        }

        return app(PlanLimits::class)->hasFeature($tenant, 'bi_reports');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ClinicalRecord::query()
                    ->selectRaw('procedure_prices.procedure_name, count(*) as total')
                    ->join('procedure_prices', 'clinical_records.procedure_price_id', '=', 'procedure_prices.id')
                    ->whereNotNull('clinical_records.procedure_price_id')
                    ->groupBy('procedure_prices.procedure_name')
                    ->orderByDesc('total')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('procedure_name')
                    ->label('Procedimiento')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('total')
                    ->label('Veces Realizado')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->paginated(false)
            ->emptyStateHeading('Sin procedimientos registrados')
            ->emptyStateDescription('Los procedimientos realizados aparecerán aquí.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    protected function getHeader(): ?string
    {
        return 'Procedimientos Más Frecuentes';
    }
}
