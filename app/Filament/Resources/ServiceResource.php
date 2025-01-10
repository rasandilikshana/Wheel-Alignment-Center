<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use App\Services\TwilioService;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationGroup = 'Operations';

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                Forms\Components\DateTimePicker::make('service_date')
                    ->required(),
                Forms\Components\DatePicker::make('next_service_date')
                    ->required(),
                Forms\Components\Select::make('service_type_id')
                    ->relationship('serviceType', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_service_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serviceType.name')
                    ->sortable(),
                Tables\Columns\IconColumn::make('welcome_sent')
                    ->boolean(),
                Tables\Columns\IconColumn::make('reminder_sent')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('send_reminder')
                    ->icon('heroicon-o-bell')
                    ->label('Send Reminder')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Service $record) {
                        $twilioService = app(TwilioService::class);

                        $reminderMessage = "Dear {$record->customer->name}, this is a reminder that your vehicle " .
                                         "{$record->customer->vehicle_number} is due for wheel alignment on " .
                                         $record->next_service_date->format('Y-m-d') .
                                         ". Please visit our center at your convenient time. Thank you!";

                        $success = $twilioService->sendSMS(
                            $record->customer->phone,
                            $reminderMessage
                        );

                        if ($success) {
                            $record->update(['reminder_sent' => true]);
                            Notification::make()
                                ->title('Reminder sent successfully')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Failed to send reminder')
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Service $record) => !$record->reminder_sent),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
