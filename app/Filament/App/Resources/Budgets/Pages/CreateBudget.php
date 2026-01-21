<?php

namespace App\Filament\App\Resources\Budgets\Pages;

use App\Filament\App\Resources\Budgets\BudgetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;
}
