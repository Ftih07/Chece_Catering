<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\MenuSubcategory;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Get;
use Filament\Forms\Set;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Menus';

    protected static ?string $modelLabel = 'Menu Item';

    protected static ?string $pluralModelLabel = 'Menu Items';

    protected static ?string $navigationGroup = 'Menu Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Menu Classification')
                ->description('Organize your menu item by category and subcategory')
                ->icon('heroicon-m-folder')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('menu_category_id')
                                ->label('Menu Category')
                                ->relationship('category', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->placeholder('Select a category')
                                ->helperText('Choose the main category for this menu item')
                                ->afterStateUpdated(function (Set $set) {
                                    $set('menu_subcategory_id', null);
                                })
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter category name'),
                                ])
                                ->createOptionModalHeading('Create New Category'),

                            Select::make('menu_subcategory_id')
                                ->label('Menu Subcategory')
                                ->relationship('subcategory', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Select a subcategory (optional)')
                                ->helperText('Optional: Choose a more specific subcategory')
                                ->options(function (Get $get) {
                                    $categoryId = $get('menu_category_id');
                                    if (!$categoryId) {
                                        return [];
                                    }
                                    return MenuSubcategory::where('menu_category_id', $categoryId)
                                        ->pluck('name', 'id');
                                })
                                ->createOptionForm([
                                    Select::make('menu_category_id')
                                        ->label('Parent Category')
                                        ->relationship('category', 'name')
                                        ->required(),
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter subcategory name'),
                                ])
                                ->createOptionModalHeading('Create New Subcategory'),
                        ])
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Menu Information')
                ->description('Enter the basic details about this menu item')
                ->icon('heroicon-m-information-circle')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Menu Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g., Grilled Chicken Sandwich')
                                ->helperText('Enter the name of the menu item')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $context, $state, callable $set) {
                                    if ($context === 'create') {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                })
                                ->columnSpan(1),

                            TextInput::make('price')
                                ->label('Price')
                                ->numeric()
                                ->prefix('IDR')
                                ->placeholder('0')
                                ->helperText('Enter the price in Indonesian Rupiah')
                                ->nullable()
                                ->minValue(0)
                                ->maxValue(999999999)
                                ->step(100)
                                ->columnSpan(1),
                        ]),

                    MarkdownEditor::make('description')
                        ->label('Description')
                        ->placeholder('Describe this menu item, its ingredients, preparation method, or any special notes...')
                        ->helperText('Optional: Provide a detailed description of the menu item')
                        ->columnSpanFull()
                        ->nullable()
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'strike',
                            'bulletList',
                            'orderedList',
                            'link',
                        ]),
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Visual & Media')
                ->description('Upload an image to showcase this menu item')
                ->icon('heroicon-m-camera')
                ->schema([
                    FileUpload::make('image')
                        ->label('Menu Image')
                        ->image()
                        ->directory('menu-items')
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->maxSize(5120) // 5MB
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Optional: Upload an appetizing image of this menu item (Max: 5MB)')
                        ->imagePreviewHeight('250')
                        ->loadingIndicatorPosition('left')
                        ->panelAspectRatio('16:9')
                        ->panelLayout('integrated')
                        ->removeUploadedFileButtonPosition('right')
                        ->uploadButtonPosition('left')
                        ->uploadProgressIndicatorPosition('left')
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(fn($record) => $record === null ? false : empty($record->image)),

            Section::make('Menu Variants')
                ->description('Add different variants or options for this menu item')
                ->icon('heroicon-m-squares-plus')
                ->schema([
                    Repeater::make('variants')
                        ->relationship()
                        ->label('')
                        ->schema([
                            TextInput::make('name')
                                ->label('Variant Name')
                                ->placeholder('e.g., Large, Medium, Spicy, Extra Sauce...')
                                ->helperText('Enter the name of this variant')
                                ->nullable()
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ])
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Menu Variant')
                        ->addActionLabel('Add New Variant')
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->cloneable()
                        ->deleteAction(
                            fn($action) => $action->requiresConfirmation()
                        )
                        ->defaultItems(0)
                        ->minItems(0)
                        ->maxItems(20)
                        ->grid(1)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(fn($record) => $record === null ? true : $record->variants->isEmpty()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('/images/placeholder-food.png'))
                    ->tooltip('Menu image'),

                TextColumn::make('name')
                    ->label('Menu Name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->color('primary')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->copyable()
                    ->copyMessage('Menu name copied!')
                    ->copyMessageDuration(1500)
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-tag')
                    ->placeholder('No category')
                    ->limit(20),

                TextColumn::make('subcategory.name')
                    ->label('Subcategory')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-folder-open')
                    ->placeholder('No subcategory')
                    ->toggleable()
                    ->limit(20),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable()
                    ->color('warning')
                    ->icon('heroicon-m-currency-dollar')
                    ->placeholder('Not set')
                    ->tooltip('Click to sort by price'),

                BadgeColumn::make('variants_count')
                    ->label('Variants')
                    ->counts('variants')
                    ->color(static function ($state): string {
                        if ($state === 0) {
                            return 'gray';
                        }
                        if ($state <= 3) {
                            return 'info';
                        }
                        return 'success';
                    })
                    ->icon(static function ($state): string {
                        if ($state === 0) {
                            return 'heroicon-m-squares-2x2';
                        }
                        return 'heroicon-m-squares-plus';
                    }),

                IconColumn::make('has_description')
                    ->label('Description')
                    ->getStateUsing(fn($record) => !empty($record->description))
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(function ($record) {
                        return $record->description ? 'Has description' : 'No description';
                    }),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        if ($record->deleted_at) {
                            return 'deleted';
                        }

                        $score = 0;
                        if ($record->name) $score++;
                        if ($record->price) $score++;
                        if ($record->description) $score++;
                        if ($record->image) $score++;
                        if ($record->menu_category_id) $score++;

                        return match ($score) {
                            5 => 'complete',
                            4 => 'good',
                            3 => 'fair',
                            2 => 'basic',
                            default => 'incomplete',
                        };
                    })
                    ->color(static function ($state): string {
                        return match ($state) {
                            'complete' => 'success',
                            'good' => 'info',
                            'fair' => 'warning',
                            'basic' => 'danger',
                            'incomplete' => 'gray',
                            'deleted' => 'gray',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(static function ($state): string {
                        return match ($state) {
                            'complete' => 'Complete',
                            'good' => 'Good',
                            'fair' => 'Fair',
                            'basic' => 'Basic',
                            'incomplete' => 'Incomplete',
                            'deleted' => 'Deleted',
                            default => 'Unknown',
                        };
                    })
                    ->icon(static function ($state): string {
                        return match ($state) {
                            'complete' => 'heroicon-m-check-circle',
                            'good' => 'heroicon-m-check',
                            'fair' => 'heroicon-m-exclamation-triangle',
                            'basic' => 'heroicon-m-x-circle',
                            'incomplete' => 'heroicon-m-minus-circle',
                            'deleted' => 'heroicon-m-trash',
                            default => 'heroicon-m-question-mark-circle',
                        };
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable()
                    ->color('gray')
                    ->icon('heroicon-m-calendar'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray')
                    ->since()
                    ->icon('heroicon-m-clock'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Show Deleted Records')
                    ->placeholder('All Records')
                    ->trueLabel('Only Deleted')
                    ->falseLabel('Without Deleted'),

                SelectFilter::make('menu_category_id')
                    ->label('Filter by Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('All Categories'),

                SelectFilter::make('menu_subcategory_id')
                    ->label('Filter by Subcategory')
                    ->relationship('subcategory', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('All Subcategories'),

                Tables\Filters\Filter::make('has_price')
                    ->label('Has Price')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('price'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_image')
                    ->label('Has Image')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('image'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_variants')
                    ->label('Has Variants')
                    ->query(fn(Builder $query): Builder => $query->has('variants'))
                    ->toggle(),

                Tables\Filters\Filter::make('complete_items')
                    ->label('Complete Items')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereNotNull('name')
                            ->whereNotNull('price')
                            ->whereNotNull('description')
                            ->whereNotNull('image')
                            ->whereNotNull('menu_category_id')
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price_from')
                                    ->label('Min Price')
                                    ->numeric()
                                    ->prefix('IDR'),
                                Forms\Components\TextInput::make('price_to')
                                    ->label('Max Price')
                                    ->numeric()
                                    ->prefix('IDR'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn(Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn(Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['price_from'] ?? null) {
                            $indicators['price_from'] = 'Min price: IDR ' . number_format($data['price_from']);
                        }
                        if ($data['price_to'] ?? null) {
                            $indicators['price_to'] = 'Max price: IDR ' . number_format($data['price_to']);
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info')
                    ->modalHeading(fn($record) => "View Menu: {$record->name}")
                    ->modalContent(fn($record) => new HtmlString("
                        <div class='space-y-6'>
                            " . ($record->image ? "
                            <div class='text-center'>
                                <img src='" . asset('storage/' . $record->image) . "' alt='{$record->name}' class='mx-auto rounded-lg max-h-64 object-cover'>
                            </div>
                            " : "") . "
                            <div class='grid grid-cols-1 md:grid-cols-2 gap-4'>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Menu Name</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>{$record->name}</p>
                                </div>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Price</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>" . ($record->price ? 'IDR ' . number_format($record->price) : 'Not set') . "</p>
                                </div>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Subcategory</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>" . ($record->subcategory?->name ?? 'No subcategory') . "</p>
                                </div>
                            </div>
                            " . ($record->description ? "
                            <div>
                                <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Description</h3>
                                <div class='prose prose-sm max-w-none text-gray-600 dark:text-gray-300'>
                                    {$record->description}
                                </div>
                            </div>
                            " : "") . "
                            " . ($record->variants->count() > 0 ? "
                            <div>
                                <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Variants (" . $record->variants->count() . ")</h3>
                                <div class='space-y-1'>
                                    " . $record->variants->map(fn($variant) => "<span class='inline-block bg-gray-100 dark:bg-gray-700 rounded-full px-3 py-1 text-sm text-gray-700 dark:text-gray-300 mr-2 mb-2'>{$variant->name}</span>")->join('') . "
                                </div>
                            </div>
                            " : "") . "
                        </div>
                    ")),

                Tables\Actions\EditAction::make()
                    ->color('warning'),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Menu Item')
                    ->modalDescription(fn($record) => "Are you sure you want to delete '{$record->name}'? This action can be undone.")
                    ->modalSubmitActionLabel('Yes, delete it'),

                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Permanently Delete Menu Item')
                    ->modalDescription(fn($record) => "Are you sure you want to permanently delete '{$record->name}'? This action cannot be undone.")
                    ->modalSubmitActionLabel('Yes, delete permanently'),

                Tables\Actions\RestoreAction::make()
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected menu items')
                        ->modalDescription('Are you sure you want to delete the selected menu items? This action can be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),

                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Permanently delete selected menu items')
                        ->modalDescription('Are you sure you want to permanently delete the selected menu items? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),

                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create your first menu item')
                    ->icon('heroicon-m-plus'),
            ])
            ->emptyStateHeading('No menu items yet')
            ->emptyStateDescription('Once you create your first menu item, it will appear here. Start building your delicious menu!')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('name')
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
            'view' => Pages\ViewMenu::route('/{record}'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
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

        if ($count > 50) {
            return 'success';
        }

        if ($count > 20) {
            return 'warning';
        }

        return 'primary';
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category', 'subcategory', 'variants']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description', 'category.name', 'subcategory.name'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        $details = [];

        if ($record->category) {
            $details['Category'] = $record->category->name;
        }

        if ($record->subcategory) {
            $details['Subcategory'] = $record->subcategory->name;
        }

        if ($record->price) {
            $details['Price'] = 'IDR ' . number_format($record->price);
        }

        if ($record->variants->count() > 0) {
            $details['Variants'] = $record->variants->count() . ' variant(s)';
        }

        return $details;
    }
}
