<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuVariantResource\Pages;
use App\Filament\Resources\MenuVariantResource\RelationManagers;
use App\Models\MenuVariant;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;

class MenuVariantResource extends Resource
{
    protected static ?string $model = MenuVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationLabel = 'Menu Variants';

    protected static ?string $modelLabel = 'Menu Variant';

    protected static ?string $pluralModelLabel = 'Menu Variants';
    protected static ?string $navigationGroup = 'Menu Management';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Variant Information')
                ->description('Configure the menu variant details and link it to a menu item')
                ->icon('heroicon-m-squares-plus')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('menu_id')
                                ->label('Menu Item')
                                ->relationship('menu', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->placeholder('Select a menu item')
                                ->helperText('Choose which menu item this variant belongs to')
                                ->getOptionLabelFromRecordUsing(fn(Menu $record) => "{$record->name}" .
                                    ($record->category ? " ({$record->category->name})" : ""))
                                ->optionsLimit(50),

                            TextInput::make('name')
                                ->label('Variant Name')
                                ->maxLength(255)
                                ->placeholder('e.g., Large, Medium, Spicy, Extra Cheese...')
                                ->helperText('Enter a descriptive name for this variant')
                                ->nullable()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $context, $state, callable $set) {
                                    if ($context === 'create' && $state) {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                }),
                        ])
                ])
                ->columns(2)
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('menu.image')
                    ->label('Menu Image')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/placeholder-food.png'))
                    ->tooltip('Menu item image'),

                TextColumn::make('menu.name')
                    ->label('Menu Item')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->color('info')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->limit(25)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 25) {
                            return null;
                        }
                        return $state;
                    })
                    ->url(function ($record) {
                        return route('filament.admin.resources.menus.edit', $record->menu);
                    }, shouldOpenInNewTab: true),

                TextColumn::make('name')
                    ->label('Variant Name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->color('primary')
                    ->icon('heroicon-m-squares-plus')
                    ->copyable()
                    ->copyMessage('Variant name copied!')
                    ->copyMessageDuration(1500)
                    ->placeholder('Unnamed variant')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (!$state || strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('menu.category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-tag')
                    ->placeholder('No category')
                    ->toggleable()
                    ->limit(20),

                TextColumn::make('menu.subcategory.name')
                    ->label('Subcategory')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-folder-open')
                    ->placeholder('No subcategory')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(20),

                TextColumn::make('menu.price')
                    ->label('Base Price')
                    ->money('IDR')
                    ->sortable()
                    ->color('gray')
                    ->icon('heroicon-m-currency-dollar')
                    ->placeholder('Not set')
                    ->toggleable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        if ($record->deleted_at) {
                            return 'deleted';
                        }

                        if (empty($record->name)) {
                            return 'unnamed';
                        }

                        return 'basic';
                    })
                    ->color(static function ($state): string {
                        return match ($state) {
                            'detailed' => 'success',
                            'basic' => 'info',
                            'unnamed' => 'warning',
                            'deleted' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(static function ($state): string {
                        return match ($state) {
                            'detailed' => 'Complete',
                            'basic' => 'Basic',
                            'unnamed' => 'Unnamed',
                            'deleted' => 'Deleted',
                            default => 'Unknown',
                        };
                    })
                    ->icon(static function ($state): string {
                        return match ($state) {
                            'detailed' => 'heroicon-m-check-circle',
                            'basic' => 'heroicon-m-check',
                            'unnamed' => 'heroicon-m-exclamation-triangle',
                            'deleted' => 'heroicon-m-x-circle',
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

                TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('danger')
                    ->placeholder('—')
                    ->icon('heroicon-m-trash'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Show Deleted Records')
                    ->placeholder('All Records')
                    ->trueLabel('Only Deleted')
                    ->falseLabel('Without Deleted'),

                SelectFilter::make('menu_id')
                    ->label('Filter by Menu Item')
                    ->relationship('menu', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('All Menu Items')
                    ->getOptionLabelFromRecordUsing(fn(Menu $record) => "{$record->name}" .
                        ($record->category ? " ({$record->category->name})" : "")),

                SelectFilter::make('menu.menu_category_id')
                    ->label('Filter by Category')
                    ->relationship('menu.category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('All Categories'),

                Tables\Filters\Filter::make('has_name')
                    ->label('Has Name')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('name')->where('name', '!=', ''))
                    ->toggle(),

                Tables\Filters\Filter::make('unnamed')
                    ->label('Unnamed Variants')
                    ->query(fn(Builder $query): Builder => $query->whereNull('name')->orWhere('name', ''))
                    ->toggle(),

                Tables\Filters\Filter::make('recent')
                    ->label('Recent (Last 7 days)')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info')
                    ->modalHeading(fn($record) => "View Variant: " . ($record->name ?: 'Unnamed Variant'))
                    ->modalContent(fn($record) => new HtmlString("
                        <div class='space-y-6'>
                            " . ($record->menu->image ? "
                            <div class='text-center'>
                                <img src='" . asset('storage/' . $record->menu->image) . "' alt='{$record->menu->name}' class='mx-auto rounded-lg max-h-48 object-cover'>
                                <p class='text-sm text-gray-500 mt-2'>Menu Item Image</p>
                            </div>
                            " : "") . "
                            <div class='grid grid-cols-1 md:grid-cols-2 gap-4'>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Menu Item</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>{$record->menu->name}</p>
                                </div>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Variant Name</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>" . ($record->name ?: 'Unnamed variant') . "</p>
                                </div>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Category</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>" . ($record->menu->category?->name ?? 'No category') . "</p>
                                </div>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Base Price</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>" . ($record->menu->price ? 'IDR ' . number_format($record->menu->price) : 'Not set') . "</p>
                                </div>
                            </div>
                        </div>
                    ")),

                Tables\Actions\EditAction::make()
                    ->color('warning'),

                Tables\Actions\Action::make('view_menu')
                    ->label('View Menu')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn($record) => route('filament.admin.resources.menus.view', $record->menu))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Variant')
                    ->modalDescription(fn($record) => "Are you sure you want to delete the variant '" . ($record->name ?: 'Unnamed') . "' from '{$record->menu->name}'? This action can be undone.")
                    ->modalSubmitActionLabel('Yes, delete it'),

                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Permanently Delete Variant')
                    ->modalDescription(fn($record) => "Are you sure you want to permanently delete the variant '" . ($record->name ?: 'Unnamed') . "' from '{$record->menu->name}'? This action cannot be undone.")
                    ->modalSubmitActionLabel('Yes, delete permanently'),

                Tables\Actions\RestoreAction::make()
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected variants')
                        ->modalDescription('Are you sure you want to delete the selected variants? This action can be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),

                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Permanently delete selected variants')
                        ->modalDescription('Are you sure you want to permanently delete the selected variants? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),

                    Tables\Actions\RestoreBulkAction::make(),

                    Tables\Actions\BulkAction::make('set_menu')
                        ->label('Change Menu Item')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Select::make('menu_id')
                                ->label('New Menu Item')
                                ->relationship('menu', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->getOptionLabelFromRecordUsing(fn(Menu $record) => "{$record->name}" .
                                    ($record->category ? " ({$record->category->name})" : "")),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['menu_id' => $data['menu_id']]);
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Change Menu Item for Selected Variants')
                        ->modalDescription('This will move all selected variants to the chosen menu item.')
                        ->modalSubmitActionLabel('Yes, move them'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create your first variant')
                    ->icon('heroicon-m-plus'),
            ])
            ->emptyStateHeading('No menu variants yet')
            ->emptyStateDescription('Once you create your first menu variant, it will appear here. Variants help provide options for your menu items.')
            ->emptyStateIcon('heroicon-o-squares-plus')
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('name')
            ->groups([
                Group::make('menu.name')
                    ->label('Menu Item')
                    ->collapsible(),
                Group::make('menu.category.name')
                    ->label('Category')
                    ->collapsible(),
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
            'view' => Pages\ViewMenuVariant::route('/{record}'),
            'edit' => Pages\EditMenuVariant::route('/{record}/edit'),
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

        if ($count > 100) {
            return 'success';
        }

        if ($count > 50) {
            return 'warning';
        }

        return 'primary';
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['menu.category', 'menu.subcategory']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'menu.name', 'menu.category.name'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        $details = [];

        $details['Menu Item'] = $record->menu->name;

        if ($record->menu->category) {
            $details['Category'] = $record->menu->category->name;
        }

        return $details;
    }

    public static function canViewAny(): bool
    {
        // Only show if there are menu items to create variants for
        return Menu::exists();
    }
}
