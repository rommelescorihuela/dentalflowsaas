<?php

namespace App\Filament\App\Resources\ServiceFeedbacks\Pages;

use App\Filament\App\Resources\ServiceFeedbacks\ServiceFeedbackResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceFeedback extends CreateRecord
{
    protected static string $resource = ServiceFeedbackResource::class;
}
