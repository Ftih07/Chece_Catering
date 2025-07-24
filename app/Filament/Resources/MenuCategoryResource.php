<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuCategoryResource\Pages;
use App\Filament\Resources\MenuCategoryResource\RelationManagers;
use App\Models\MenuCategory;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class MenuCategoryResource extends Resource
{
    protected static ?string $model = MenuCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Menu Categories';

    protected static ?string $modelLabel = 'Menu Category';

    protected static ?string $pluralModelLabel = 'Menu Categories';
    protected static ?string $navigationGroup = 'Menu Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Category Information')
                ->description('Enter the basic information for the menu category')
                ->icon('heroicon-m-information-circle')
                ->schema([
                    TextInput::make('name')
                        ->label('Category Name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Enter category name...')
                        ->helperText('This will be displayed as the category title')
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->collapsible(),

            Section::make('Gallery Images')
                ->description('Add images to showcase this menu category')
                ->icon('heroicon-m-photo')
                ->schema([
                    Repeater::make('galleries')
                        ->relationship()
                        ->label('')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Image Title')
                                        ->placeholder('Enter image title...')
                                        ->helperText('Optional: Add a descriptive title for this image')
                                        ->maxLength(255),

                                    FileUpload::make('image')
                                        ->label('Upload Image')
                                        ->image()
                                        ->directory('menu_categories/gallery')
                                        ->imageEditor()
                                        ->imageEditorAspectRatios([
                                            '16:9',
                                            '4:3',
                                            '1:1',
                                        ])
                                        ->maxSize(5120) // 5MB
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->helperText('Max size: 5MB. Formats: JPEG, PNG, WebP')
                                        ->imagePreviewHeight('200')
                                        ->loadingIndicatorPosition('left')
                                        ->panelAspectRatio('2:1')
                                        ->panelLayout('integrated')
                                        ->removeUploadedFileButtonPosition('right')
                                        ->uploadButtonPosition('left')
                                        ->uploadProgressIndicatorPosition('left'),
                                ])
                        ])
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Gallery Image')
                        ->addActionLabel('Add New Image')
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->cloneable()
                        ->deleteAction(
                            fn($action) => $action->requiresConfirmation()
                        )
                        ->defaultItems(0)
                        ->minItems(0)
                        ->maxItems(10)
                        ->grid(1)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->color('primary')
                    ->icon('heroicon-m-tag'),

                ImageColumn::make('galleries.image')
                    ->label('Gallery Preview')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->size(40)
                    ->defaultImageUrl(url('/images/placeholder.png')),

                BadgeColumn::make('galleries_count')
                    ->label('Images')
                    ->counts('galleries')
                    ->color(static function ($state): string {
                        if ($state === 0) {
                            return 'gray';
                        }
                        if ($state <= 3) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->icon(static function ($state): string {
                        if ($state === 0) {
                            return 'heroicon-m-photo';
                        }
                        return 'heroicon-m-camera';
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable()
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),

                TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('danger')
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Show Deleted Records')
                    ->placeholder('All Records')
                    ->trueLabel('Only Deleted')
                    ->falseLabel('Without Deleted'),

                Tables\Filters\Filter::make('has_images')
                    ->label('Has Images')
                    ->query(fn(Builder $query): Builder => $query->has('galleries'))
                    ->toggle(),

                Tables\Filters\Filter::make('no_images')
                    ->label('No Images')
                    ->query(fn(Builder $query): Builder => $query->doesntHave('galleries'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Category')
                    ->modalDescription('Are you sure you want to delete this category? This action can be undone.')
                    ->modalSubmitActionLabel('Yes, delete it'),
                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Permanently Delete Category')
                    ->modalDescription('Are you sure you want to permanently delete this category? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete permanently'),
                Tables\Actions\RestoreAction::make()
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected categories')
                        ->modalDescription('Are you sure you want to delete the selected categories? This action can be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Permanently delete selected categories')
                        ->modalDescription('Are you sure you want to permanently delete the selected categories? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create your first category')
                    ->icon('heroicon-m-plus'),
            ])
            ->emptyStateHeading('No menu categories yet')
            ->emptyStateDescription('Once you create your first menu category, it will appear here.')
            ->emptyStateIcon('heroicon-o-squares-2x2')
            ->striped()
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListMenuCategories::route('/'),
            'create' => Pages\CreateMenuCategory::route('/create'),
            'view' => Pages\ViewMenuCategory::route('/{record}'),
            'edit' => Pages\EditMenuCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();

        if ($count > 10) {
            return 'success';
        }

        if ($count > 5) {
            return 'warning';
        }

        return 'primary';
    }
}
