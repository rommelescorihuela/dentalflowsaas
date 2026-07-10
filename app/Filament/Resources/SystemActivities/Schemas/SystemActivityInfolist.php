<?php

namespace App\Filament\Resources\SystemActivities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SystemActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Detalles de Actividad')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')->label('Usuario'),
                        TextEntry::make('clinic.name')->label('Clínica'),
                        TextEntry::make('action')->badge(),
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('description')->columnSpanFull(),
                    ]),
                Section::make('Información de Solicitud')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('ip_address')->label('Direccion IP'),
                        TextEntry::make('method')->label('Metodo'),
                        TextEntry::make('url')->label('URL')->columnSpanFull()->copyable(),
                        TextEntry::make('user_agent')->label('Navegador')->columnSpanFull(),
                    ]),
                Section::make('Cambios de Datos')
                    ->schema([
                        TextEntry::make('old_values')
                            ->label('Valores Anteriores')
                            ->html()
                            ->formatStateUsing(fn ($state) => '<pre style="font-size: 0.75rem; overflow-x: auto;">'.json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'</pre>'),
                        TextEntry::make('new_values')
                            ->label('Valores Nuevos')
                            ->html()
                            ->formatStateUsing(fn ($state) => '<pre style="font-size: 0.75rem; overflow-x: auto;">'.json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'</pre>'),
                        TextEntry::make('payload')
                            ->label('Datos de Solicitud')
                            ->html()
                            ->formatStateUsing(fn ($state) => '<pre style="font-size: 0.75rem; overflow-x: auto;">'.json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'</pre>'),
                    ]),
            ]);
    }
}
