<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Filament\Resources\GalleryResource\RelationManagers;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Filament\Support\Enums\MaxWidth;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    // Improved navigation
    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static ?string $navigationLabel = 'Gallery Items';
    protected static ?string $modelLabel = 'Gallery Item';
    protected static ?string $pluralModelLabel = 'Gallery Items';
    protected static ?string $navigationGroup = 'Gallery Management';
    protected static ?int $navigationSort = 2;

    // Record title attribute
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Essential details for your gallery item')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('gallery_category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(callable $set, $state) => $set('slug', Str::slug($state))),
                                        Forms\Components\Textarea::make('description')
                                            ->required()
                                            ->rows(3),
                                    ])
                                    ->createOptionModalHeading('Create New Category')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('name')
                                    ->label('Gallery Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $context, $state, callable $set) {
                                        if ($context === 'create') {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                    ]),

                Forms\Components\Section::make('Media')
                    ->description('Upload and manage gallery media files')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Primary Image')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                                '3:4',
                                '9:16',
                            ])
                            ->directory('gallery/images')
                            ->visibility('public')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Maximum size: 10MB. Supported formats: JPEG, PNG, WebP')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl(url('/images/placeholder.jpg')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn(Gallery $record): string => Str::limit($record->description, 50)),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(Gallery $record): string => match ($record->category?->color) {
                        null => 'gray',
                        default => 'primary',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Deleted Records'),

                Tables\Filters\SelectFilter::make('gallery_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('has_image')
                    ->label('Has Image')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('image')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created from'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Created from ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Created until ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->modalWidth(MaxWidth::FiveExtraLarge),

                Tables\Actions\EditAction::make()
                    ->iconButton(),

                Tables\Actions\Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Gallery $record) {
                        $newRecord = $record->replicate();
                        $newRecord->name = $record->name . ' (Copy)';
                        $newRecord->save();
                    })
                    ->successNotificationTitle('Gallery item duplicated successfully'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton(),

                Tables\Actions\RestoreAction::make()
                    ->iconButton(),

                Tables\Actions\ForceDeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('updateCategory')
                        ->label('Update Category')
                        ->icon('heroicon-o-tag')
                        ->form([
                            Forms\Components\Select::make('gallery_category_id')
                                ->label('New Category')
                                ->relationship('category', 'name')
                                ->required(),
                        ])
                        ->action(function (array $data, \Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['gallery_category_id' => $data['gallery_category_id']]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->withoutGlobalScopes([SoftDeletingScope::class])
            )
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Upload First Gallery Item')
                    ->icon('heroicon-o-plus'),
            ])
            ->emptyStateHeading('No gallery items found')
            ->emptyStateDescription('Start building your gallery by uploading your first image or creating a new gallery item.')
            ->emptyStateIcon('heroicon-o-camera');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Gallery Preview')
                    ->schema([
                        Infolists\Components\ImageEntry::make('image')
                            ->label('')
                            ->size(400)
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Basic Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Title')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('category.name')
                                    ->label('Category')
                                    ->badge(),
                            ]),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->html()
                            ->columnSpanFull(),
                    ]),


                Infolists\Components\Section::make('System Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime(),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers here if needed
            // RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'view' => Pages\ViewGallery::route('/{record}'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }

    // Global search configuration
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category']);
    }

    public static function getGlobalSearchAttributes(): array
    {
        return ['name', 'description',];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Category' => $record->category?->name,
        ];
    }
}
