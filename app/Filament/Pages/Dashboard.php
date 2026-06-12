<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Escritorio';

    protected static ?string $title = 'Escritorio';

    public function getTitle(): string
    {
        return 'Escritorio';
    }

    public function getHeading(): string
    {
        return 'Escritorio';
    }
}
