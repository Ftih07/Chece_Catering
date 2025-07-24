<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryCategoryResource\Pages;
use App\Filament\Resources\GalleryCategoryResource\RelationManagers;
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
use Illuminate\Support\Str;

/**
 * Kelas GalleryCategoryResource
 *
 * Mengatur tampilan dan fungsionalitas CRUD (Create, Read, Update, Delete)
 * untuk model GalleryCategory di dalam Filament Admin Panel.
 */
class GalleryCategoryResource extends Resource
{
    /**
     * Menentukan model Eloquent yang terkait dengan resource ini.
     * Dalam kasus ini, modelnya adalah App\Models\GalleryCategory.
     *
     * @var string|null
     */
    protected static ?string $model = GalleryCategory::class;

    // Improved navigation
    /**
     * Icon navigasi yang akan ditampilkan di sidebar Filament.
     * Menggunakan icon 'heroicon-o-photo'.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    /**
     * Label navigasi yang akan ditampilkan di sidebar Filament.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Gallery Categories';

    /**
     * Label untuk satu instance model (singular).
     *
     * @var string|null
     */
    protected static ?string $modelLabel = 'Gallery Category';

    /**
     * Label untuk beberapa instance model (plural).
     *
     * @var string|null
     */
    protected static ?string $pluralModelLabel = 'Gallery Categories';

    /**
     * Grup navigasi tempat resource ini akan ditempatkan di sidebar.
     * Membantu dalam mengorganisir navigasi admin.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Gallery Management';

    /**
     * Urutan navigasi di dalam grupnya. Angka yang lebih kecil akan tampil lebih dulu.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 1;

    // Record title attribute for better identification
    /**
     * Atribut model yang akan digunakan sebagai judul untuk identifikasi record.
     * Digunakan misalnya di breadcrumbs atau di tampilan record individu.
     *
     * @var string|null
     */
    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Mendefinisikan skema formulir untuk membuat atau mengedit kategori galeri.
     *
     * @param Form $form Objek Form yang akan dikonfigurasi.
     * @return Form Skema formulir yang sudah dikonfigurasi.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Bagian utama untuk informasi kategori.
                Forms\Components\Section::make('Category Information')
                    ->description('Manage gallery category details') // Deskripsi singkat untuk bagian ini.
                    ->icon('heroicon-o-information-circle') // Icon untuk bagian ini.
                    ->schema([
                        // Tata letak grid dengan 2 kolom.
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // Bidang input teks untuk 'name' kategori.
                                Forms\Components\TextInput::make('name')
                                    ->label('Category Name') // Label yang ditampilkan di form.
                                    ->required() // Menandakan bahwa bidang ini wajib diisi.
                                    ->maxLength(255) // Batas karakter maksimum.
                                    ->live(onBlur: true) // Memperbarui state secara langsung saat bidang kehilangan fokus.
                                    // Callback setelah state input 'name' diperbarui.
                                    // Digunakan untuk secara otomatis mengisi 'slug' saat membuat kategori baru.
                                    ->afterStateUpdated(function (string $context, $state, callable $set) {
                                        if ($context === 'create') { // Hanya saat membuat record baru.
                                            $set('slug', Str::slug($state)); // Mengisi bidang 'slug' dengan versi slug dari 'name'.
                                        }
                                    })
                                    ->columnSpan(1), // Mengambil 1 kolom dalam grid.
                            ]),

                        // Bidang textarea untuk 'description' kategori.
                        Forms\Components\Textarea::make('description')
                            ->label('Description') // Label yang ditampilkan di form.
                            ->rows(4) // Tinggi textarea dalam baris.
                            ->maxLength(1000) // Batas karakter maksimum.
                            ->columnSpanFull() // Mengambil seluruh lebar kolom.
                            ->helperText('Optional description for this category'), // Teks bantuan di bawah input.
                    ]),
            ]);
    }

    /**
     * Mendefinisikan skema tabel untuk menampilkan daftar kategori galeri.
     *
     * @param Table $table Objek Table yang akan dikonfigurasi.
     * @return Table Skema tabel yang sudah dikonfigurasi.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom teks untuk nama kategori.
                Tables\Columns\TextColumn::make('name')
                    ->label('Category Name') // Label kolom.
                    ->searchable() // Memungkinkan pencarian berdasarkan nama.
                    ->sortable() // Memungkinkan pengurutan berdasarkan nama.
                    ->weight(FontWeight::SemiBold) // Mengatur ketebalan font.
                    // Menampilkan slug sebagai deskripsi di bawah nama kategori.
                    ->description(fn(GalleryCategory $record): string => $record->slug ?? ''),

                // Kolom teks untuk deskripsi.
                Tables\Columns\TextColumn::make('description')
                    ->label('Description') // Label kolom.
                    ->limit(80) // Batasi teks hingga 80 karakter.
                    // Menampilkan tooltip dengan deskripsi lengkap jika lebih dari 80 karakter.
                    ->tooltip(function (GalleryCategory $record): ?string {
                        return strlen($record->description) > 80 ? $record->description : null;
                    })
                    ->wrap(), // Memungkinkan teks membungkus ke baris berikutnya.

                // Kolom untuk menghitung jumlah item galeri yang terkait.
                Tables\Columns\TextColumn::make('galleries_count')
                    ->label('Items') // Label kolom.
                    ->counts('galleries') // Menghitung relasi 'galleries'.
                    ->sortable() // Memungkinkan pengurutan berdasarkan jumlah item.
                    ->alignCenter() // Menyelaraskan teks ke tengah.
                    ->badge() // Menampilkan jumlah sebagai badge.
                    ->color('primary'), // Warna badge.

                // Kolom untuk tanggal pembuatan.
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created') // Label kolom.
                    ->dateTime('M j, Y') // Format tampilan tanggal dan waktu.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable(isToggledHiddenByDefault: true), // Tersembunyi secara default, bisa ditampilkan.

                // Kolom untuk tanggal terakhir diperbarui.
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated') // Label kolom.
                    ->dateTime('M j, Y') // Format tampilan tanggal dan waktu.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable(isToggledHiddenByDefault: true), // Tersembunyi secara default, bisa ditampilkan.
            ])
            ->filters([
                // Filter untuk melihat record yang sudah dihapus (soft delete).
                Tables\Filters\TrashedFilter::make()
                    ->label('Deleted Records'),

                // Filter untuk kategori yang memiliki item galeri.
                Tables\Filters\Filter::make('has_galleries')
                    ->label('Has Gallery Items') // Label filter.
                    // Query untuk filter: hanya ambil kategori yang memiliki relasi 'galleries'.
                    ->query(fn(Builder $query): Builder => $query->has('galleries')),

                // Filter berdasarkan rentang tanggal pembuatan.
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        // Input date picker untuk tanggal mulai.
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created from'),
                        // Input date picker untuk tanggal selesai.
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created until'),
                    ])
                    // Logika query untuk filter tanggal.
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            // Jika 'created_from' ada, tambahkan kondisi whereDate >=.
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            // Jika 'created_until' ada, tambahkan kondisi whereDate <=.
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    // Menentukan bagaimana indikator filter ditampilkan di UI.
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
                // Aksi untuk melihat detail record.
                Tables\Actions\ViewAction::make()
                    ->iconButton(), // Tampilkan sebagai tombol icon.

                // Aksi untuk mengedit record.
                Tables\Actions\EditAction::make()
                    ->iconButton(), // Tampilkan sebagai tombol icon.

                // Aksi untuk menghapus record (soft delete).
                Tables\Actions\DeleteAction::make()
                    ->iconButton(), // Tampilkan sebagai tombol icon.

                // Aksi untuk mengembalikan record yang sudah dihapus.
                Tables\Actions\RestoreAction::make()
                    ->iconButton(), // Tampilkan sebagai tombol icon.

                // Aksi untuk menghapus record secara permanen.
                Tables\Actions\ForceDeleteAction::make()
                    ->iconButton(), // Tampilkan sebagai tombol icon.
            ])
            ->bulkActions([
                // Grup aksi massal (bulk actions) untuk record yang dipilih.
                Tables\Actions\BulkActionGroup::make([
                    // Aksi hapus massal (soft delete).
                    Tables\Actions\DeleteBulkAction::make(),
                    // Aksi kembalikan massal.
                    Tables\Actions\RestoreBulkAction::make(),
                    // Aksi hapus permanen massal.
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            // Modifikasi query utama tabel untuk menyertakan record yang dihapus (soft delete).
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->withoutGlobalScopes([SoftDeletingScope::class])
            )
            ->emptyStateActions([
                // Aksi yang ditampilkan saat tabel kosong.
                Tables\Actions\CreateAction::make()
                    ->label('Create First Category') // Label tombol.
                    ->icon('heroicon-o-plus'), // Icon tombol.
            ])
            ->emptyStateHeading('No gallery categories found') // Judul saat tabel kosong.
            ->emptyStateDescription('Create your first gallery category to get started with organizing your gallery items.') // Deskripsi saat tabel kosong.
            ->emptyStateIcon('heroicon-o-photo'); // Icon saat tabel kosong.
    }

    /**
     * Mendefinisikan skema Infolist untuk menampilkan detail satu kategori galeri.
     * Infolist digunakan untuk tampilan "View" record.
     *
     * @param Infolist $infolist Objek Infolist yang akan dikonfigurasi.
     * @return Infolist Skema infolist yang sudah dikonfigurasi.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Bagian untuk detail kategori.
                Infolists\Components\Section::make('Category Details')
                    ->schema([
                        // Tata letak grid dengan 2 kolom.
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                // Tampilan teks untuk nama kategori.
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Name') // Label yang ditampilkan.
                                    ->weight(FontWeight::SemiBold), // Ketebalan font.
                            ]),

                        // Tampilan teks untuk deskripsi kategori.
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description') // Label yang ditampilkan.
                            ->columnSpanFull(), // Mengambil seluruh lebar kolom.
                    ]),

                // Bagian untuk informasi sistem (timestamps).
                Infolists\Components\Section::make('System Information')
                    ->schema([
                        // Tata letak grid dengan 2 kolom.
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                // Tampilan teks untuk tanggal pembuatan.
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created') // Label yang ditampilkan.
                                    ->dateTime(), // Format tampilan tanggal dan waktu.

                                // Tampilan teks untuk tanggal terakhir diperbarui.
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Last Updated') // Label yang ditampilkan.
                                    ->dateTime(), // Format tampilan tanggal dan waktu.
                            ]),
                    ])
                    ->collapsed(), // Bagian ini akan diciutkan (collapsed) secara default.
            ]);
    }

    /**
     * Mendapatkan daftar Relation Managers yang terkait dengan resource ini.
     * Relation Managers memungkinkan pengelolaan relasi model langsung dari halaman resource.
     *
     * @return array Daftar kelas Relation Manager.
     */
    public static function getRelations(): array
    {
        return [
            // Tambahkan Relation Managers di sini jika diperlukan.
            // Contoh: RelationManagers\GalleriesRelationManager::class,
        ];
    }

    /**
     * Mendapatkan daftar halaman (pages) yang terkait dengan resource ini.
     * Ini mendefinisikan URL untuk setiap tindakan (list, create, view, edit).
     *
     * @return array Daftar halaman.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryCategories::route('/'), // Halaman daftar kategori.
            'create' => Pages\CreateGalleryCategory::route('/create'), // Halaman pembuatan kategori baru.
            'view' => Pages\ViewGalleryCategory::route('/{record}'), // Halaman melihat detail kategori.
            'edit' => Pages\EditGalleryCategory::route('/{record}/edit'), // Halaman mengedit kategori.
        ];
    }

    // Global search configuration
    /**
     * Mengatur query Eloquent yang digunakan untuk pencarian global.
     * Menambahkan eager loading relasi 'galleries' untuk efisiensi.
     *
     * @return Builder
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['galleries']);
    }

    /**
     * Mendapatkan atribut model yang akan dicari dalam pencarian global.
     *
     * @return array Atribut yang dapat dicari.
     */
    public static function getGlobalSearchAttributes(): array
    {
        return ['name', 'description', 'slug'];
    }

    /**
     * Mendapatkan badge yang akan ditampilkan di samping item navigasi.
     * Menampilkan jumlah total kategori galeri.
     *
     * @return string|null Jumlah kategori.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * Mendapatkan warna badge navigasi berdasarkan jumlah kategori.
     * Warna akan berubah tergantung pada kuantitas record.
     *
     * @return string|null Warna badge (e.g., 'primary', 'success', 'warning').
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();

        if ($count > 10) {
            return 'success'; // Lebih dari 10 kategori: hijau.
        }

        if ($count > 5) {
            return 'warning'; // Lebih dari 5 kategori: kuning.
        }

        return 'primary'; // 5 kategori atau kurang: biru.
    }
}
