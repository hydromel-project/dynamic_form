<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResponseAnswerResource\Pages;
use App\Filament\Resources\ResponseAnswerResource\RelationManagers;
use App\Models\ResponseAnswer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResponseAnswerResource extends Resource
{
    protected static ?string $model = ResponseAnswer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('response_id')
                    ->relationship('response', 'session_token')
                    ->required(),
                Forms\Components\Select::make('question_id')
                    ->relationship('question', 'question_text')
                    ->required(),
                Forms\Components\Textarea::make('answer')
                    ->json()
                    ->required()
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('response.session_token')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('question.question_text')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('answer')->limit(50),
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
            'index' => Pages\ListResponseAnswers::route('/'),
            'create' => Pages\CreateResponseAnswer::route('/create'),
            'edit' => Pages\EditResponseAnswer::route('/{record}/edit'),
        ];
    }
}
