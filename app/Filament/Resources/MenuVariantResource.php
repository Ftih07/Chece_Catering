<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuVariantResource\Pages;
use App\Filament\Resources\MenuVariantResource\RelationManagers;
use App\Models\MenuVariant;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuVariantResource extends Resource
{
    protected static ?string $model = MenuVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('menu_id')
                    ->relationship('menu', 'name')
                    ->required(),
                TextInput::make('name')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('menu.name')->label('Menu'),
                TextColumn::make('name')->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->withoutGlobalScopes([SoftDeletingScope::class])
            );
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
            'index' => Pages\ListMenuVariants::route('/'),
            'create' => Pages\CreateMenuVariant::route('/create'),
            'edit' => Pages\EditMenuVariant::route('/{record}/edit'),
        ];
    }
}
