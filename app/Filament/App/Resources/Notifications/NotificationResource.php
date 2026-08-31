<?php

namespace App\Filament\App\Resources\Notifications;

use App\Models\Notification;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notificaciones';

    protected static ?string $modelLabel = 'Notificación';

    protected static ?string $pluralModelLabel = 'Notificaciones';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'info' => 'Información',
                        'success' => 'Éxito',
                        'warning' => 'Advertencia',
                        'error' => 'Error',
                        'appointment' => 'Cita',
                        'payment' => 'Pago',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->label('Mensaje')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('link')
                    ->label('Enlace')
                    ->url(),
                Forms\Components\TextInput::make('icon')
                    ->label('Ícono')
                    ->placeholder('heroicon-o-bell'),
                Forms\Components\Select::make('color')
                    ->label('Color')
                    ->options([
                        'blue' => 'Azul',
                        'green' => 'Verde',
                        'yellow' => 'Amarillo',
                        'red' => 'Rojo',
                        'purple' => 'Púrpura',
                    ]),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'unread' => 'No leído',
                        'read' => 'Leído',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'blue',
                        'success' => 'green',
                        'warning' => 'yellow',
                        'error' => 'red',
                        'appointment' => 'purple',
                        'payment' => 'emerald',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Mensaje')
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'warning',
                        'read' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('unread')
                    ->label('No leídas')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'unread')),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'info' => 'Información',
                        'success' => 'Éxito',
                        'warning' => 'Advertencia',
                        'error' => 'Error',
                        'appointment' => 'Cita',
                        'payment' => 'Pago',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                Action::make('markRead')
                    ->label('Marcar leído')
                    ->icon('heroicon-o-check')
                    ->action(fn (Notification $record) => $record->markAsRead())
                    ->visible(fn (Notification $record) => $record->isUnread()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
