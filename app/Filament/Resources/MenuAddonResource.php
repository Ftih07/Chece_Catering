<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuAddonResource\Pages;
use App\Filament\Resources\MenuAddonResource\RelationManagers;
use App\Models\MenuAddon;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;

class MenuAddonResource extends Resource
{
    protected static ?string $model = MenuAddon::class;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    protected static ?string $navigationLabel = 'Menu Add-ons';

    protected static ?string $modelLabel = 'Menu Add-on';

    protected static ?string $pluralModelLabel = 'Menu Add-ons';
    protected static ?string $navigationGroup = 'Menu Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Add-on Information')
                ->description('Configure the menu add-on details and description')
                ->icon('heroicon-m-plus-circle')
                ->schema([
                    Grid::make(1)
                        ->schema([
                            TextInput::make('title')
                                ->label('Add-on Title')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g., Extra Cheese, Bacon, Large Size...')
                                ->helperText('Enter a clear and descriptive title for this add-on')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $context, $state, callable $set) {
                                    if ($context === 'create') {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                }),
                        ])
                ])
                ->columns(1)
                ->collapsible(),

            Section::make('Description & Details')
                ->description('Provide detailed information about this add-on')
                ->icon('heroicon-m-document-text')
                ->schema([
                    RichEditor::make('description')
                        ->label('Description')
                        ->placeholder('Describe this add-on, its ingredients, or any special notes...')
                        ->helperText('Optional: Add detailed description, ingredients, or special instructions')
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                            'bulletList',
                            'orderedList',
                            'h2',
                            'h3',
                            'link',
                            'undo',
                            'redo',
                        ])
                        ->columnSpanFull()
                        ->nullable(),
                ])
                ->collapsible()
                ->collapsed(fn($record) => $record === null ? false : empty($record->description)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Add-on Title')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->color('primary')
                    ->icon('heroicon-m-plus-circle')
                    ->copyable()
                    ->copyMessage('Title copied!')
                    ->copyMessageDuration(1500),

                TextColumn::make('description')
                    ->label('Description')
                    ->html()
                    ->limit(60)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen(strip_tags($state)) <= 60) {
                            return null;
                        }
                        return strip_tags($state);
                    })
                    ->placeholder('No description')
                    ->color('gray')
                    ->wrap(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        if ($record->deleted_at) {
                            return 'deleted';
                        }
                        return empty($record->description) ? 'basic' : 'detailed';
                    })
                    ->color(static function ($state): string {
                        return match ($state) {
                            'deleted' => 'danger',
                            'detailed' => 'success',
                            'basic' => 'warning',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(static function ($state): string {
                        return match ($state) {
                            'deleted' => 'Deleted',
                            'detailed' => 'Complete',
                            'basic' => 'Basic',
                            default => 'Unknown',
                        };
                    })
                    ->icon(static function ($state): string {
                        return match ($state) {
                            'deleted' => 'heroicon-m-x-circle',
                            'detailed' => 'heroicon-m-check-circle',
                            'basic' => 'heroicon-m-exclamation-triangle',
                            default => 'heroicon-m-question-mark-circle',
                        };
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable()
                    ->color('gray')
                    ->icon('heroicon-m-calendar'),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray')
                    ->since()
                    ->icon('heroicon-m-clock'),

                TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->dateTime('M j, Y g:i A')
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

                Tables\Filters\Filter::make('has_description')
                    ->label('Has Description')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('description')->where('description', '!=', ''))
                    ->toggle(),

                Tables\Filters\Filter::make('no_description')
                    ->label('No Description')
                    ->query(fn(Builder $query): Builder => $query->whereNull('description')->orWhere('description', ''))
                    ->toggle(),

                Tables\Filters\Filter::make('recent')
                    ->label('Recent (Last 7 days)')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info')
                    ->modalHeading(fn($record) => "View Add-on: {$record->title}")
                    ->modalContent(fn($record) => new HtmlString("
                        <div class='space-y-4'>
                            <div>
                                <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Title</h3>
                                <p class='text-gray-600 dark:text-gray-300'>{$record->title}</p>
                            </div>
                            " . ($record->description ? "
                            <div>
                                <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Description</h3>
                                <div class='prose prose-sm max-w-none text-gray-600 dark:text-gray-300'>
                                    {$record->description}
                                </div>
                            </div>
                            " : "<p class='text-gray-500 italic'>No description provided</p>") . "
                        </div>
                    ")),

                Tables\Actions\EditAction::make()
                    ->color('warning'),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Add-on')
                    ->modalDescription(fn($record) => "Are you sure you want to delete '{$record->title}'? This action can be undone.")
                    ->modalSubmitActionLabel('Yes, delete it'),

                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Permanently Delete Add-on')
                    ->modalDescription(fn($record) => "Are you sure you want to permanently delete '{$record->title}'? This action cannot be undone.")
                    ->modalSubmitActionLabel('Yes, delete permanently'),

                Tables\Actions\RestoreAction::make()
                    ->color('success'),

                Tables\Actions\ReplicateAction::make()
                    ->color('gray')
                    ->beforeReplicaSaved(function ($replica) {
                        $replica->title = $replica->title . ' (Copy)';
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected add-ons')
                        ->modalDescription('Are you sure you want to delete the selected add-ons? This action can be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),

                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Permanently delete selected add-ons')
                        ->modalDescription('Are you sure you want to permanently delete the selected add-ons? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),

                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create your first add-on')
                    ->icon('heroicon-m-plus'),
            ])
            ->emptyStateHeading('No menu add-ons yet')
            ->emptyStateDescription('Once you create your first menu add-on, it will appear here. Add-ons help customers customize their orders.')
            ->emptyStateIcon('heroicon-o-plus-circle')
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('title')
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
            'index' => Pages\ListMenuAddons::route('/'),
            'create' => Pages\CreateMenuAddon::route('/create'),
            'view' => Pages\ViewMenuAddon::route('/{record}'),
            'edit' => Pages\EditMenuAddon::route('/{record}/edit'),
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

        if ($count > 20) {
            return 'success';
        }

        if ($count > 10) {
            return 'warning';
        }

        return 'primary';
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Description' => $record->description ? strip_tags($record->description) : 'No description',
        ];
    }
}
