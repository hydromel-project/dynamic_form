<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResponseResource\Pages;
use App\Filament\Resources\ResponseResource\RelationManagers;
use App\Models\Response;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResponseResource extends Resource
{
    protected static ?string $model = Response::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('form_id')
                    ->relationship('form', 'name')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->nullable(),
                Forms\Components\TextInput::make('session_token')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('submitted'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('form.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('session_token')->searchable(),
                Tables\Columns\IconColumn::make('submitted')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListResponses::route('/'),
            'create' => Pages\CreateResponse::route('/create'),
            'edit' => Pages\EditResponse::route('/{record}/edit'),
        ];
    }
}
