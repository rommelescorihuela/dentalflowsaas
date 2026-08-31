<?php

namespace App\Filament\App\Resources\ServiceFeedbacks\Pages;

use App\Filament\App\Resources\ServiceFeedbacks\ServiceFeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceFeedback extends EditRecord
{
    protected static string $resource = ServiceFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
