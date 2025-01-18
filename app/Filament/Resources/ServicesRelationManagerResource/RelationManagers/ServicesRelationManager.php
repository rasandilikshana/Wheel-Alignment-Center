<?php

namespace App\Filament\Resources\ServicesRelationManagerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('service_type_id')
                ->relationship('serviceType', 'name')
                ->required(),
            Forms\Components\DateTimePicker::make('service_date')
                ->required()
,
            Forms\Components\DatePicker::make('next_service_date')
                ->required(),
            Forms\Components\Textarea::make('notes')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('service_type')
            ->columns([
                Stack::make([
                    Tables\Columns\TextColumn::make('serviceType.name')
                        ->label('Service Type')
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('service_date')
                        ->label('Service Date')
                        ->weight(FontWeight::Bold)
                        ->date()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('next_service_date')
                        ->label('Next Service Date')
                        ->date()
                        ->sortable(),
                    Tables\Columns\IconColumn::make('welcome_sent')
                        ->label('Welcome SMS')
                        ->boolean(),
                    Tables\Columns\IconColumn::make('reminder_sent')
                        ->label('Reminder SMS')
                        ->boolean(),
                    ])
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
