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
use Filament\Forms\Components\Textarea; // Tidak digunakan dalam kode ini, bisa dihapus jika tidak diperlukan.
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

/**
 * Kelas MenuVariantResource adalah sumber daya Filament untuk mengelola varian menu.
 * Ini menyediakan antarmuka CRUD (Create, Read, Update, Delete) di panel admin.
 */
class MenuVariantResource extends Resource
{
    /**
     * Model Eloquent yang terkait dengan sumber daya ini.
     * @var string|null
     */
    protected static ?string $model = MenuVariant::class;

    /**
     * Ikon navigasi yang akan ditampilkan di sidebar Filament.
     * Menggunakan ikon Heroicons 'squares-plus'.
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    /**
     * Label untuk item navigasi di sidebar Filament.
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Menu Variants';

    /**
     * Label singular untuk model ini, ditampilkan di UI.
     * @var string|null
     */
    protected static ?string $modelLabel = 'Menu Variant';

    /**
     * Label plural untuk model ini, ditampilkan di UI.
     * @var string|null
     */
    protected static ?string $pluralModelLabel = 'Menu Variants';

    /**
     * Grup navigasi tempat sumber daya ini akan ditempatkan di sidebar.
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Menu Management';

    /**
     * Urutan navigasi dalam grupnya.
     * @var int|null
     */
    protected static ?int $navigationSort = 5;

    /**
     * Mendefinisikan skema formulir untuk membuat dan mengedit varian menu.
     *
     * @param Form $form Objek formulir Filament.
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            // Bagian utama untuk informasi varian
            Section::make('Variant Information')
                ->description('Configure the menu variant details and link it to a menu item') // Deskripsi bagian
                ->icon('heroicon-m-squares-plus') // Ikon untuk bagian
                ->schema([
                    // Mengatur layout ke grid 2 kolom
                    Grid::make(2)
                        ->schema([
                            // Field Select untuk memilih item Menu yang terkait
                            Select::make('menu_id')
                                ->label('Menu Item') // Label field
                                ->relationship('menu', 'name') // Menghubungkan ke model 'Menu' dan menampilkan kolom 'name'
                                ->required() // Field wajib diisi
                                ->searchable() // Memungkinkan pencarian dalam dropdown
                                ->preload() // Memuat semua opsi di awal
                                ->placeholder('Select a menu item') // Placeholder untuk field
                                ->helperText('Choose which menu item this variant belongs to') // Teks bantuan
                                // Mengatur format label opsi dropdown, menambahkan kategori jika ada
                                ->getOptionLabelFromRecordUsing(fn(Menu $record) => "{$record->name}" .
                                    ($record->category ? " ({$record->category->name})" : ""))
                                ->optionsLimit(50), // Batas jumlah opsi yang dimuat

                            // Field TextInput untuk nama varian
                            TextInput::make('name')
                                ->label('Variant Name') // Label field
                                ->maxLength(255) // Batas panjang karakter
                                ->placeholder('e.g., Large, Medium, Spicy, Extra Cheese...') // Contoh placeholder
                                ->helperText('Enter a descriptive name for this variant') // Teks bantuan
                                ->nullable() // Field bisa kosong
                                ->live(onBlur: true) // Memperbarui secara real-time saat blur
                                // Hook setelah state diperbarui untuk menghasilkan slug otomatis saat membuat
                                ->afterStateUpdated(function (string $context, $state, callable $set) {
                                    if ($context === 'create' && $state) {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                }),
                        ])
                ])
                ->columns(2) // Mengatur kolom untuk bagian (ini akan diterapkan ke konten di dalam schema)
                ->collapsible(), // Membuat bagian bisa dibuka/ditutup
        ]);
    }

    /**
     * Mendefinisikan skema tabel untuk menampilkan daftar varian menu.
     *
     * @param Table $table Objek tabel Filament.
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Gambar untuk gambar menu terkait
                ImageColumn::make('menu.image')
                    ->label('Menu Image') // Label kolom
                    ->circular() // Membuat gambar melingkar
                    ->size(40) // Ukuran gambar
                    ->defaultImageUrl(url('/images/placeholder-food.png')) // Gambar default jika tidak ada gambar
                    ->tooltip('Menu item image'), // Tooltip saat hover

                // Kolom Teks untuk nama item menu
                TextColumn::make('menu.name')
                    ->label('Menu Item') // Label kolom
                    ->searchable() // Memungkinkan pencarian pada kolom ini
                    ->sortable() // Memungkinkan pengurutan pada kolom ini
                    ->weight(FontWeight::SemiBold) // Berat font
                    ->color('info') // Warna teks
                    ->icon('heroicon-m-clipboard-document-list') // Ikon di samping teks
                    ->limit(25) // Batas panjang teks yang ditampilkan
                    // Tooltip yang menampilkan nama lengkap jika terpotong
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 25) {
                            return null;
                        }
                        return $state;
                    })
                    // Menjadikan teks sebagai link ke halaman edit menu item
                    ->url(function ($record) {
                        return route('filament.admin.resources.menus.edit', $record->menu);
                    }, shouldOpenInNewTab: true), // Membuka link di tab baru

                // Kolom Teks untuk nama varian
                TextColumn::make('name')
                    ->label('Variant Name') // Label kolom
                    ->searchable() // Memungkinkan pencarian
                    ->sortable() // Memungkinkan pengurutan
                    ->weight(FontWeight::Medium) // Berat font
                    ->color('primary') // Warna teks
                    ->icon('heroicon-m-squares-plus') // Ikon
                    ->copyable() // Memungkinkan penyalinan teks
                    ->copyMessage('Variant name copied!') // Pesan saat disalin
                    ->copyMessageDuration(1500) // Durasi pesan
                    ->placeholder('Unnamed variant') // Placeholder jika nama kosong
                    ->limit(30) // Batas panjang teks
                    // Tooltip yang menampilkan nama lengkap jika terpotong
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (!$state || strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                // Kolom Teks untuk nama kategori menu
                TextColumn::make('menu.category.name')
                    ->label('Category') // Label kolom
                    ->searchable() // Memungkinkan pencarian
                    ->sortable() // Memungkinkan pengurutan
                    ->badge() // Menampilkan sebagai badge
                    ->color('success') // Warna badge
                    ->icon('heroicon-m-tag') // Ikon badge
                    ->placeholder('No category') // Placeholder jika tidak ada kategori
                    ->toggleable() // Memungkinkan kolom di-toggle (disembunyikan/ditampilkan)
                    ->limit(20), // Batas panjang teks

                // Kolom Teks untuk nama subkategori menu
                TextColumn::make('menu.subcategory.name')
                    ->label('Subcategory') // Label kolom
                    ->searchable() // Memungkinkan pencarian
                    ->sortable() // Memungkinkan pengurutan
                    ->badge() // Menampilkan sebagai badge
                    ->color('warning') // Warna badge
                    ->icon('heroicon-m-folder-open') // Ikon badge
                    ->placeholder('No subcategory') // Placeholder jika tidak ada subkategori
                    ->toggleable(isToggledHiddenByDefault: true) // Tersembunyi secara default
                    ->limit(20), // Batas panjang teks

                // Kolom Teks untuk harga dasar menu
                TextColumn::make('menu.price')
                    ->label('Base Price') // Label kolom
                    ->money('IDR') // Memformat sebagai mata uang IDR
                    ->sortable() // Memungkinkan pengurutan
                    ->color('gray') // Warna teks
                    ->icon('heroicon-m-currency-dollar') // Ikon
                    ->placeholder('Not set') // Placeholder jika harga belum diatur
                    ->toggleable(), // Memungkinkan kolom di-toggle

                // Kolom Badge untuk status varian (dihapus, belum bernama, atau dasar)
                BadgeColumn::make('status')
                    ->label('Status') // Label kolom
                    // Menentukan state (nilai) badge berdasarkan kondisi record
                    ->getStateUsing(function ($record) {
                        if ($record->deleted_at) {
                            return 'deleted'; // Varian dihapus
                        }
                        if (empty($record->name)) {
                            return 'unnamed'; // Varian tidak memiliki nama
                        }
                        return 'basic'; // Varian dasar (memiliki nama dan tidak dihapus)
                    })
                    // Menentukan warna badge berdasarkan state
                    ->color(static function ($state): string {
                        return match ($state) {
                            'detailed' => 'success', // Contoh: jika ada status 'detailed'
                            'basic' => 'info',
                            'unnamed' => 'warning',
                            'deleted' => 'danger',
                            default => 'gray',
                        };
                    })
                    // Memformat tampilan teks badge berdasarkan state
                    ->formatStateUsing(static function ($state): string {
                        return match ($state) {
                            'detailed' => 'Complete',
                            'basic' => 'Basic',
                            'unnamed' => 'Unnamed',
                            'deleted' => 'Deleted',
                            default => 'Unknown',
                        };
                    })
                    // Menentukan ikon badge berdasarkan state
                    ->icon(static function ($state): string {
                        return match ($state) {
                            'detailed' => 'heroicon-m-check-circle',
                            'basic' => 'heroicon-m-check',
                            'unnamed' => 'heroicon-m-exclamation-triangle',
                            'deleted' => 'heroicon-m-x-circle',
                            default => 'heroicon-m-question-mark-circle',
                        };
                    }),

                // Kolom Teks untuk tanggal pembuatan
                TextColumn::make('created_at')
                    ->label('Created') // Label kolom
                    ->dateTime('M j, Y') // Format tanggal dan waktu
                    ->sortable() // Memungkinkan pengurutan
                    ->toggleable() // Memungkinkan kolom di-toggle
                    ->color('gray') // Warna teks
                    ->icon('heroicon-m-calendar'), // Ikon

                // Kolom Teks untuk tanggal pembaruan terakhir
                TextColumn::make('updated_at')
                    ->label('Updated') // Label kolom
                    ->dateTime('M j, Y') // Format tanggal dan waktu
                    ->sortable() // Memungkinkan pengurutan
                    ->toggleable(isToggledHiddenByDefault: true) // Tersembunyi secara default
                    ->color('gray') // Warna teks
                    ->since() // Menampilkan "X days ago"
                    ->icon('heroicon-m-clock'), // Ikon

                // Kolom Teks untuk tanggal penghapusan (untuk soft deletes)
                TextColumn::make('deleted_at')
                    ->label('Deleted') // Label kolom
                    ->dateTime('M j, Y') // Format tanggal dan waktu
                    ->sortable() // Memungkinkan pengurutan
                    ->toggleable(isToggledHiddenByDefault: true) // Tersembunyi secara default
                    ->color('danger') // Warna teks merah
                    ->placeholder('—') // Placeholder jika tidak dihapus
                    ->icon('heroicon-m-trash'), // Ikon
            ])
            // Filter untuk tabel
            ->filters([
                // Filter untuk menampilkan atau menyembunyikan record yang dihapus (soft deleted)
                Tables\Filters\TrashedFilter::make()
                    ->label('Show Deleted Records') // Label filter
                    ->placeholder('All Records') // Placeholder
                    ->trueLabel('Only Deleted') // Label jika filter aktif (hanya yang dihapus)
                    ->falseLabel('Without Deleted'), // Label jika filter tidak aktif (tanpa yang dihapus)

                // Filter Select untuk memfilter berdasarkan item Menu
                SelectFilter::make('menu_id')
                    ->label('Filter by Menu Item') // Label filter
                    ->relationship('menu', 'name') // Menghubungkan ke model 'Menu' dan menampilkan kolom 'name'
                    ->searchable() // Memungkinkan pencarian dalam dropdown filter
                    ->preload() // Memuat semua opsi di awal
                    ->multiple() // Memungkinkan pemilihan banyak item menu
                    ->placeholder('All Menu Items') // Placeholder
                    // Mengatur format label opsi dropdown, menambahkan kategori jika ada
                    ->getOptionLabelFromRecordUsing(fn(Menu $record) => "{$record->name}" .
                        ($record->category ? " ({$record->category->name})" : "")),

                // Filter Select untuk memfilter berdasarkan kategori menu (melalui relasi menu)
                SelectFilter::make('menu.menu_category_id')
                    ->label('Filter by Category') // Label filter
                    ->relationship('menu.category', 'name') // Menghubungkan ke model 'Category' melalui 'menu'
                    ->searchable() // Memungkinkan pencarian
                    ->preload() // Memuat semua opsi di awal
                    ->multiple() // Memungkinkan pemilihan banyak kategori
                    ->placeholder('All Categories'), // Placeholder

                // Filter kustom untuk varian yang memiliki nama
                Tables\Filters\Filter::make('has_name')
                    ->label('Has Name') // Label filter
                    // Query untuk filter: mencari record dengan nama tidak null dan tidak kosong
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('name')->where('name', '!=', ''))
                    ->toggle(), // Menampilkan sebagai toggle switch

                // Filter kustom untuk varian yang belum memiliki nama
                Tables\Filters\Filter::make('unnamed')
                    ->label('Unnamed Variants') // Label filter
                    // Query untuk filter: mencari record dengan nama null atau kosong
                    ->query(fn(Builder $query): Builder => $query->whereNull('name')->orWhere('name', ''))
                    ->toggle(), // Menampilkan sebagai toggle switch

                // Filter kustom untuk varian yang dibuat dalam 7 hari terakhir
                Tables\Filters\Filter::make('recent')
                    ->label('Recent (Last 7 days)') // Label filter
                    // Query untuk filter: mencari record yang dibuat dalam 7 hari terakhir
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(), // Menampilkan sebagai toggle switch
            ])
            // Aksi-aksi yang tersedia untuk setiap baris tabel
            ->actions([
                // Aksi 'View' untuk melihat detail varian
                Tables\Actions\ViewAction::make()
                    ->color('info') // Warna tombol
                    // Judul modal tampilan, menampilkan nama varian atau 'Unnamed Variant'
                    ->modalHeading(fn($record) => "View Variant: " . ($record->name ?: 'Unnamed Variant'))
                    // Konten modal tampilan, menampilkan detail varian dengan HTML kustom
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

                // Aksi 'Edit' untuk mengedit varian
                Tables\Actions\EditAction::make()
                    ->color('warning'), // Warna tombol

                // Aksi kustom 'View Menu' untuk langsung melihat halaman menu item terkait
                Tables\Actions\Action::make('view_menu')
                    ->label('View Menu') // Label tombol
                    ->icon('heroicon-o-eye') // Ikon
                    ->color('info') // Warna tombol
                    ->url(fn($record) => route('filament.admin.resources.menus.view', $record->menu)) // URL ke halaman menu item
                    ->openUrlInNewTab(), // Membuka di tab baru

                // Aksi 'Delete' untuk soft delete varian
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation() // Membutuhkan konfirmasi
                    ->modalHeading('Delete Variant') // Judul modal konfirmasi
                    // Deskripsi modal konfirmasi
                    ->modalDescription(fn($record) => "Are you sure you want to delete the variant '" . ($record->name ?: 'Unnamed') . "' from '{$record->menu->name}'? This action can be undone.")
                    ->modalSubmitActionLabel('Yes, delete it'), // Label tombol submit modal

                // Aksi 'ForceDelete' untuk menghapus varian secara permanen
                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation() // Membutuhkan konfirmasi
                    ->modalHeading('Permanently Delete Variant') // Judul modal konfirmasi
                    // Deskripsi modal konfirmasi
                    ->modalDescription(fn($record) => "Are you sure you want to permanently delete the variant '" . ($record->name ?: 'Unnamed') . "' from '{$record->menu->name}'? This action cannot be undone.")
                    ->modalSubmitActionLabel('Yes, delete permanently'), // Label tombol submit modal

                // Aksi 'Restore' untuk mengembalikan varian yang dihapus (soft deleted)
                Tables\Actions\RestoreAction::make()
                    ->color('success'), // Warna tombol
            ])
            // Aksi massal (bulk actions) yang tersedia untuk beberapa baris tabel
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Aksi massal 'Delete'
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected variants')
                        ->modalDescription('Are you sure you want to delete the selected variants? This action can be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),

                    // Aksi massal 'ForceDelete' (hapus permanen)
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Permanently delete selected variants')
                        ->modalDescription('Are you sure you want to permanently delete the selected variants? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),

                    // Aksi massal 'Restore'
                    Tables\Actions\RestoreBulkAction::make(),

                    // Aksi massal kustom 'Set Menu' untuk mengubah item menu dari beberapa varian sekaligus
                    Tables\Actions\BulkAction::make('set_menu')
                        ->label('Change Menu Item') // Label aksi
                        ->icon('heroicon-o-arrow-path') // Ikon
                        ->color('warning') // Warna tombol
                        ->form([ // Formulir untuk aksi massal ini
                            Select::make('menu_id')
                                ->label('New Menu Item')
                                ->relationship('menu', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                // Mengatur format label opsi dropdown
                                ->getOptionLabelFromRecordUsing(fn(Menu $record) => "{$record->name}" .
                                    ($record->category ? " ({$record->category->name})" : "")),
                        ])
                        // Logika yang dijalankan saat aksi dikonfirmasi
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['menu_id' => $data['menu_id']]);
                            }
                        })
                        ->requiresConfirmation() // Membutuhkan konfirmasi
                        ->modalHeading('Change Menu Item for Selected Variants') // Judul modal
                        ->modalDescription('This will move all selected variants to the chosen menu item.') // Deskripsi modal
                        ->modalSubmitActionLabel('Yes, move them'), // Label tombol submit
                ]),
            ])
            // Aksi-aksi yang ditampilkan saat tabel kosong
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create your first variant') // Label tombol
                    ->icon('heroicon-m-plus'), // Ikon
            ])
            // Teks dan ikon saat tabel kosong
            ->emptyStateHeading('No menu variants yet')
            ->emptyStateDescription('Once you create your first menu variant, it will appear here. Variants help provide options for your menu items.')
            ->emptyStateIcon('heroicon-o-squares-plus')
            ->striped() // Membuat baris tabel bergantian warna
            ->defaultSort('created_at', 'desc') // Pengurutan default berdasarkan created_at secara descending
            ->recordTitleAttribute('name') // Atribut yang digunakan sebagai judul record
            // Pengelompokan baris tabel
            ->groups([
                // Mengelompokkan berdasarkan nama item menu
                Group::make('menu.name')
                    ->label('Menu Item')
                    ->collapsible(), // Membuat grup bisa dibuka/ditutup
                // Mengelompokkan berdasarkan nama kategori menu
                Group::make('menu.category.name')
                    ->label('Category')
                    ->collapsible(), // Membuat grup bisa dibuka/ditutup
            ])
            // Memodifikasi query dasar tabel, menghilangkan global scope SoftDeletingScope
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->withoutGlobalScopes([SoftDeletingScope::class])
            );
    }

    /**
     * Mendefinisikan relasi yang akan ditampilkan di halaman detail sumber daya.
     * Saat ini tidak ada relasi yang didefinisikan secara eksplisit di sini.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Mendefinisikan rute untuk halaman-halaman sumber daya (daftar, buat, lihat, edit).
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuVariants::route('/'), // Halaman daftar
            'create' => Pages\CreateMenuVariant::route('/create'), // Halaman buat baru
            'view' => Pages\ViewMenuVariant::route('/{record}'), // Halaman lihat detail
            'edit' => Pages\EditMenuVariant::route('/{record}/edit'), // Halaman edit
        ];
    }

    /**
     * Mengambil query Eloquent dasar untuk sumber daya ini.
     * Secara default, ini akan menghilangkan SoftDeletingScope sehingga record yang dihapus juga bisa dilihat (jika filter aktif).
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class, // Memastikan record yang dihapus juga disertakan dalam query utama
            ]);
    }

    /**
     * Mengambil jumlah total varian menu untuk ditampilkan sebagai badge di navigasi.
     *
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * Menentukan warna badge navigasi berdasarkan jumlah varian menu.
     * Hijau jika > 100, kuning jika > 50, dan biru standar lainnya.
     *
     * @return string|null
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();

        if ($count > 100) {
            return 'success'; // Hijau
        }

        if ($count > 50) {
            return 'warning'; // Kuning
        }

        return 'primary'; // Biru (default)
    }

    /**
     * Mengambil query Eloquent untuk pencarian global.
     * Ini memuat relasi 'menu', 'menu.category', dan 'menu.subcategory' untuk pencarian yang lebih efisien.
     *
     * @return Builder
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['menu.category', 'menu.subcategory']);
    }

    /**
     * Mendefinisikan atribut-atribut yang dapat dicari secara global.
     * Memungkinkan pencarian berdasarkan nama varian, nama menu, dan nama kategori menu.
     *
     * @return array
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'menu.name', 'menu.category.name'];
    }

    /**
     * Mengambil detail tambahan untuk hasil pencarian global.
     * Menampilkan "Menu Item" dan "Category" dari varian yang ditemukan.
     *
     * @param mixed $record Record varian menu yang ditemukan.
     * @return array
     */
    public static function getGlobalSearchResultDetails($record): array
    {
        $details = [];

        $details['Menu Item'] = $record->menu->name;

        if ($record->menu->category) {
            $details['Category'] = $record->menu->category->name;
        }

        return $details;
    }

    /**
     * Menentukan apakah sumber daya ini (daftar varian) dapat dilihat.
     * Hanya akan ditampilkan jika ada setidaknya satu item menu.
     *
     * @return bool
     */
    public static function canViewAny(): bool
    {
        // Hanya tampilkan jika ada item menu untuk dibuat varian
        return Menu::exists();
    }
}
