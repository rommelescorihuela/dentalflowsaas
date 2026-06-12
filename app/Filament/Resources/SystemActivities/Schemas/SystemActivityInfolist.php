<?php

namespace App\Filament\Resources\SystemActivities\Schemas;

use Filament\Schemas\Schema;

class SystemActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Detalles de Actividad')
                    ->columns(2)
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('user.name')->label('Usuario'),
                        \Filament\Infolists\Components\TextEntry::make('clinic.name')->label('Clínica'),
                        \Filament\Infolists\Components\TextEntry::make('action')->badge(),
                        \Filament\Infolists\Components\TextEntry::make('created_at')->dateTime(),
                        \Filament\Infolists\Components\TextEntry::make('description')->columnSpanFull(),
                    ]),
                \Filament\Schemas\Components\Section::make('Información de Solicitud')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('ip_address')->label('Direccion IP'),
                        \Filament\Infolists\Components\TextEntry::make('method')->label('Metodo'),
                        \Filament\Infolists\Components\TextEntry::make('url')->label('URL')->columnSpanFull()->copyable(),
                        \Filament\Infolists\Components\TextEntry::make('user_agent')->label('Navegador')->columnSpanFull(),
                    ]),
                \Filament\Schemas\Components\Section::make('Cambios de Datos')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('old_values')
                            ->label('Valores Anteriores')
                            ->html()
                            ->formatStateUsing(fn($state) => '<pre style="font-size: 0.75rem; overflow-x: auto;">' . json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>'),
                        \Filament\Infolists\Components\TextEntry::make('new_values')
                            ->label('Valores Nuevos')
                            ->html()
                            ->formatStateUsing(fn($state) => '<pre style="font-size: 0.75rem; overflow-x: auto;">' . json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>'),
                        \Filament\Infolists\Components\TextEntry::make('payload')
                            ->label('Datos de Solicitud')
                            ->html()
                            ->formatStateUsing(fn($state) => '<pre style="font-size: 0.75rem; overflow-x: auto;">' . json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>'),
                    ]),
            ]);
    }
}
