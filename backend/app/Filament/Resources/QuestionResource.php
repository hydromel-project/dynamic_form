<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('form_id')
                    ->relationship('form', 'name')
                    ->required(),
                Forms\Components\TextInput::make('question_text')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'text' => 'Text',
                        'number' => 'Number',
                        'boolean' => 'Boolean',
                        'select' => 'Select',
                        'file' => 'File',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('options')
                    ->json()
                    ->nullable()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('conditional_rules')
                    ->json()
                    ->nullable()
                    ->columnSpan('full'),
                Forms\Components\Toggle::make('required'),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('form.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('question_text')->searchable(),
                Tables\Columns\TextColumn::make('type')->searchable(),
                Tables\Columns\IconColumn::make('required')->boolean(),
                Tables\Columns\TextColumn::make('order')->sortable(),
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
