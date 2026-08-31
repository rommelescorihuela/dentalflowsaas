<?php

namespace App\Filament\App\Resources\ServiceFeedbacks\Pages;

use App\Filament\App\Resources\ServiceFeedbacks\ServiceFeedbackResource;
use Filament\Resources\Pages\ListRecords;

class ListServiceFeedbacks extends ListRecords
{
    protected static string $resource = ServiceFeedbackResource::class;
}
