<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuSubcategoryResource\Pages;
use App\Filament\Resources\MenuSubcategoryResource\RelationManagers;
use App\Models\MenuSubcategory;
use App\Models\MenuCategory;
use App\Models\MenuAddon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\SelectFilter;

class MenuSubcategoryResource extends Resource
{
    protected static ?string $model = MenuSubcategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'Menu Subcategories';

    protected static ?string $modelLabel = 'Menu Subcategory';

    protected static ?string $pluralModelLabel = 'Menu Subcategories';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Subcategory Information')
                ->description('Configure the basic information for this subcategory')
                ->icon('heroicon-m-folder-open')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('menu_category_id')
                                ->label('Parent Category')
                                ->relationship('category', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->placeholder('Select a parent category')
                                ->helperText('Choose which main category this subcategory belongs to')
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter category name'),
                                ])
                                ->createOptionModalHeading('Create New Category')
                                ->editOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                ])
                                ->options(function () {
                                    return MenuCategory::pluck('name', 'id');
                                }),

                            TextInput::make('name')
                                ->label('Subcategory Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g., Appetizers, Main Course, Desserts...')
                                ->helperText('Enter a descriptive name for this subcategory')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $context, $state, callable $set) {
                                    if ($context === 'create') {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                }),
                        ])
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Optional Add-on & Resources')
                ->description('Link an add-on and upload menu PDF if available')
                ->icon('heroicon-m-plus')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('menu_addon_id')
                                ->label('Related Add-on')
                                ->relationship('addon', 'title')
                                ->nullable()
                                ->searchable()
                                ->preload()
                                ->placeholder('Select an add-on (optional)')
                                ->helperText('Optional: Choose an add-on that goes with this subcategory')
                                ->createOptionForm([
                                    TextInput::make('title')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter add-on title'),
                                ])
                                ->createOptionModalHeading('Create New Add-on')
                                ->options(function () {
                                    return MenuAddon::pluck('title', 'id');
                                }),

                            FileUpload::make('pdf_path')
                                ->label('Menu PDF')
                                ->directory('menu-pdfs')
                                ->acceptedFileTypes(['application/pdf'])
                                ->maxSize(10240) // 10MB
                                ->preserveFilenames()
                                ->nullable()
                                ->helperText('Optional: Upload a PDF menu for this subcategory (Max: 10MB)')
                                ->downloadable()
                                ->previewable(false)
                                ->openable()
                                ->deletable()
                                ->moveFiles()
                                ->uploadingMessage('Uploading PDF...')
                                ->uploadProgressIndicatorPosition('left'),
                        ])
                ])
                ->columns(2)
                ->collapsible()
                ->collapsed(fn($record) => $record === null ? false : (empty($record->menu_addon_id) && empty($record->pdf_path))),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Subcategory Name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->color('primary')
                    ->icon('heroicon-m-folder-open')
                    ->copyable()
                    ->copyMessage('Name copied!')
                    ->copyMessageDuration(1500),

                TextColumn::make('category.name')
                    ->label('Parent Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-tag')
                    ->placeholder('No category')
                    ->tooltip('Click to filter by this category'),

                TextColumn::make('addon.title')
                    ->label('Linked Add-on')
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-plus-circle')
                    ->placeholder('No add-on')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (!$state || strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                IconColumn::make('has_pdf')
                    ->label('PDF')
                    ->getStateUsing(fn($record) => !empty($record->pdf_path))
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(function ($record) {
                        return $record->pdf_path ? 'PDF available' : 'No PDF uploaded';
                    }),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        if ($record->deleted_at) {
                            return 'deleted';
                        }

                        $score = 0;
                        if ($record->menu_category_id) $score++;
                        if ($record->menu_addon_id) $score++;
                        if ($record->pdf_path) $score++;

                        return match ($score) {
                            3 => 'complete',
                            2 => 'good',
                            1 => 'basic',
                            0 => 'incomplete',
                        };
                    })
                    ->color(static function ($state): string {
                        return match ($state) {
                            'complete' => 'success',
                            'good' => 'info',
                            'basic' => 'warning',
                            'incomplete' => 'danger',
                            'deleted' => 'gray',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(static function ($state): string {
                        return match ($state) {
                            'complete' => 'Complete',
                            'good' => 'Good',
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
                            'basic' => 'heroicon-m-exclamation-triangle',
                            'incomplete' => 'heroicon-m-x-circle',
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

                SelectFilter::make('menu_category_id')
                    ->label('Filter by Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('All Categories'),

                SelectFilter::make('menu_addon_id')
                    ->label('Filter by Add-on')
                    ->relationship('addon', 'title')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('All Add-ons'),

                Tables\Filters\Filter::make('has_addon')
                    ->label('Has Add-on')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('menu_addon_id'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_pdf')
                    ->label('Has PDF')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('pdf_path'))
                    ->toggle(),

                Tables\Filters\Filter::make('complete')
                    ->label('Complete Setup')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereNotNull('menu_category_id')
                            ->whereNotNull('menu_addon_id')
                            ->whereNotNull('pdf_path')
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info')
                    ->modalHeading(fn($record) => "View Subcategory: {$record->name}")
                    ->modalContent(fn($record) => new HtmlString("
                        <div class='space-y-4'>
                            <div class='grid grid-cols-1 md:grid-cols-2 gap-4'>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Subcategory Name</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>{$record->name}</p>
                                </div>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Parent Category</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>" . ($record->category?->name ?? 'No category') . "</p>
                                </div>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Linked Add-on</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>" . ($record->addon?->title ?? 'No add-on') . "</p>
                                </div>
                                <div>
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>PDF Menu</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>" . ($record->pdf_path ? '✓ Available' : 'Not uploaded') . "</p>
                                </div>
                            </div>
                        </div>
                    ")),

                Tables\Actions\EditAction::make()
                    ->color('warning'),

                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn($record) => !empty($record->pdf_path))
                    ->url(fn($record) => asset('storage/' . $record->pdf_path))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Subcategory')
                    ->modalDescription(fn($record) => "Are you sure you want to delete '{$record->name}'? This action can be undone.")
                    ->modalSubmitActionLabel('Yes, delete it'),

                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Permanently Delete Subcategory')
                    ->modalDescription(fn($record) => "Are you sure you want to permanently delete '{$record->name}'? This action cannot be undone.")
                    ->modalSubmitActionLabel('Yes, delete permanently'),

                Tables\Actions\RestoreAction::make()
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected subcategories')
                        ->modalDescription('Are you sure you want to delete the selected subcategories? This action can be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),

                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Permanently delete selected subcategories')
                        ->modalDescription('Are you sure you want to permanently delete the selected subcategories? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),

                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create your first subcategory')
                    ->icon('heroicon-m-plus'),
            ])
            ->emptyStateHeading('No menu subcategories yet')
            ->emptyStateDescription('Once you create your first menu subcategory, it will appear here. Subcategories help organize your menu items.')
            ->emptyStateIcon('heroicon-o-folder-open')
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
            'index' => Pages\ListMenuSubcategories::route('/'),
            'create' => Pages\CreateMenuSubcategory::route('/create'),
            'view' => Pages\ViewMenuSubcategory::route('/{record}'),
            'edit' => Pages\EditMenuSubcategory::route('/{record}/edit'),
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

        if ($count > 15) {
            return 'success';
        }

        if ($count > 8) {
            return 'warning';
        }

        return 'primary';
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category', 'addon']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'category.name', 'addon.title'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        $details = [];

        if ($record->category) {
            $details['Category'] = $record->category->name;
        }

        if ($record->addon) {
            $details['Add-on'] = $record->addon->title;
        }

        if ($record->pdf_path) {
            $details['PDF'] = 'Available';
        }

        return $details;
    }
}
