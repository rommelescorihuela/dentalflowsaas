<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

class SystemTools extends Page
{
    protected string $view = 'filament.pages.system-tools';

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-wrench-screwdriver';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public function getTitle(): string
    {
        return 'System Tools';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runSeeders')
                ->label('Run Database Seeders (Soft Reset)')
                ->icon('heroicon-m-play')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Run Seeders?')
                ->modalDescription('This will execute the database seeders and insert base data into your SaaS. Since it uses firstOrCreate, it should gracefully restore roles and seed missing components without deleting production data.')
                ->modalSubmitActionLabel('Yes, run them')
                ->action(function () {
                    try {
                        Artisan::call('db:seed', ['--force' => true]);
                        Notification::make()
                            ->title('Seeders executed successfully!')
                            ->body('The output was recorded in the system logs.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error executing seeders')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
