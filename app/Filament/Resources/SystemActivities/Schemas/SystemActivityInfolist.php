<?php

namespace App\Filament\Resources\SystemActivities\Schemas;

use Filament\Schemas\Schema;

class SystemActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Activity Details')
                    ->columns(2)
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('user.name')->label('User'),
                        \Filament\Infolists\Components\TextEntry::make('clinic.name')->label('Clinic'),
                        \Filament\Infolists\Components\TextEntry::make('action')->badge(),
                        \Filament\Infolists\Components\TextEntry::make('created_at')->dateTime(),
                        \Filament\Infolists\Components\TextEntry::make('description')->columnSpanFull(),
                    ]),
                \Filament\Schemas\Components\Section::make('Request Info')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('ip_address')->label('IP Address'),
                        \Filament\Infolists\Components\TextEntry::make('method'),
                        \Filament\Infolists\Components\TextEntry::make('url')->columnSpanFull()->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('user_agent')->columnSpanFull(),
                    ]),
                \Filament\Schemas\Components\Section::make('Data Changes')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('old_values')
                            ->label('Old Values')
                            ->html()
                            ->formatStateUsing(fn($state) => '<pre style="font-size: 0.75rem; overflow-x: auto;">' . json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>'),
                        \Filament\Infolists\Components\TextEntry::make('new_values')
                            ->label('New Values')
                            ->html()
                            ->formatStateUsing(fn($state) => '<pre style="font-size: 0.75rem; overflow-x: auto;">' . json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>'),
                        \Filament\Infolists\Components\TextEntry::make('payload')
                            ->label('Request Payload')
                            ->html()
                            ->formatStateUsing(fn($state) => '<pre style="font-size: 0.75rem; overflow-x: auto;">' . json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>'),
                    ]),
            ]);
    }
}
