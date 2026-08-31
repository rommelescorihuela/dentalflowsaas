<?php

namespace App\Filament\App\Resources\Messages\Pages;

use App\Filament\App\Resources\Messages\MessageResource;
use Filament\Resources\Pages\ListRecords;

class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;
}
