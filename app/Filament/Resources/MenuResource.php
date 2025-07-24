<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('menu_category_id')
                    ->label('Menu Category')
                    ->options(\App\Models\MenuCategory::all()->pluck('name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('menu_subcategory_id', null)),

                Select::make('menu_subcategory_id')
                    ->label('Menu Subcategory')
                    ->options(function (callable $get) {
                        $categoryId = $get('menu_category_id');
                        if (!$categoryId) return [];

                        return \App\Models\MenuSubcategory::where('menu_category_id', $categoryId)
                            ->pluck('name', 'id');
                    }),
                TextInput::make('name')->required(),
                Textarea::make('description')->nullable(),
                TextInput::make('price')->numeric()->nullable(),
                FileUpload::make('image')->image()->nullable(),
                Repeater::make('variants')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')->nullable(),
                    ])
                    ->columns(1)
                    ->label('Variants (optional)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')->searchable(),
                TextColumn::make('subcategory.name')->label('Subcategory'),
                TextColumn::make('price')->money('IDR')->sortable(),
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
