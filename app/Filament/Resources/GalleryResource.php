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

/**
 * Kelas GalleryResource
 *
 * Mengatur tampilan dan fungsionalitas CRUD (Create, Read, Update, Delete)
 * untuk model Gallery (item galeri individual) di dalam Filament Admin Panel.
 */
class GalleryResource extends Resource
{
    /**
     * Menentukan model Eloquent yang terkait dengan resource ini.
     * Dalam kasus ini, modelnya adalah App\Models\Gallery.
     *
     * @var string|null
     */
    protected static ?string $model = Gallery::class;

    // Improved navigation
    /**
     * Icon navigasi yang akan ditampilkan di sidebar Filament.
     * Menggunakan icon 'heroicon-o-camera'.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-camera';

    /**
     * Label navigasi yang akan ditampilkan di sidebar Filament.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Gallery Items';

    /**
     * Label untuk satu instance model (singular).
     *
     * @var string|null
     */
    protected static ?string $modelLabel = 'Gallery Item';

    /**
     * Label untuk beberapa instance model (plural).
     *
     * @var string|null
     */
    protected static ?string $pluralModelLabel = 'Gallery Items';

    /**
     * Grup navigasi tempat resource ini akan ditempatkan di sidebar.
     * Membantu dalam mengorganisir navigasi admin.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Gallery Management';

    /**
     * Urutan navigasi di dalam grupnya. Angka yang lebih kecil akan tampil lebih dulu.
     * Ini diatur setelah GalleryCategoryResource.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 2;

    // Record title attribute
    /**
     * Atribut model yang akan digunakan sebagai judul untuk identifikasi record.
     * Digunakan misalnya di breadcrumbs atau di tampilan record individu.
     *
     * @var string|null
     */
    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Mendefinisikan skema formulir untuk membuat atau mengedit item galeri.
     *
     * @param Form $form Objek Form yang akan dikonfigurasi.
     * @return Form Skema formulir yang sudah dikonfigurasi.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Bagian utama untuk informasi dasar item galeri.
                Forms\Components\Section::make('Basic Information')
                    ->description('Essential details for your gallery item') // Deskripsi singkat.
                    ->icon('heroicon-o-information-circle') // Icon untuk bagian ini.
                    ->schema([
                        // Tata letak grid dengan 2 kolom.
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // Bidang Select untuk memilih kategori galeri.
                                Forms\Components\Select::make('gallery_category_id')
                                    ->label('Category') // Label yang ditampilkan.
                                    ->relationship('category', 'name') // Mengambil data dari relasi 'category', menggunakan kolom 'name'.
                                    ->required() // Wajib diisi.
                                    ->searchable() // Memungkinkan pencarian dalam dropdown.
                                    ->preload() // Memuat semua opsi saat form dibuka.
                                    // Memungkinkan pembuatan kategori baru langsung dari dropdown ini.
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(callable $set, $state) => $set('slug', Str::slug($state))), // Otomatis mengisi slug.
                                        Forms\Components\Textarea::make('description')
                                            ->required()
                                            ->rows(3),
                                    ])
                                    ->createOptionModalHeading('Create New Category') // Judul modal untuk membuat kategori baru.
                                    ->columnSpan(1), // Mengambil 1 kolom dalam grid.

                                // Bidang input teks untuk judul galeri.
                                Forms\Components\TextInput::make('name')
                                    ->label('Gallery Title') // Label yang ditampilkan.
                                    ->required() // Wajib diisi.
                                    ->maxLength(255) // Batas karakter maksimum.
                                    ->live(onBlur: true) // Memperbarui state secara langsung saat bidang kehilangan fokus.
                                    // Callback setelah state input 'name' diperbarui.
                                    // Digunakan untuk secara otomatis mengisi 'slug' saat membuat item galeri baru.
                                    ->afterStateUpdated(function (string $context, $state, callable $set) {
                                        if ($context === 'create') { // Hanya saat membuat record baru.
                                            $set('slug', Str::slug($state)); // Mengisi bidang 'slug' dengan versi slug dari 'name'.
                                        }
                                    })
                                    ->columnSpan(1), // Mengambil 1 kolom dalam grid.
                            ]),

                        // Bidang textarea untuk deskripsi galeri.
                        Forms\Components\Textarea::make('description')
                            ->label('Description') // Label yang ditampilkan.
                            ->columnSpanFull(), // Mengambil seluruh lebar kolom.
                    ]),

                // Bagian untuk mengunggah dan mengelola file media.
                Forms\Components\Section::make('Media')
                    ->description('Upload and manage gallery media files') // Deskripsi singkat.
                    ->icon('heroicon-o-photo') // Icon untuk bagian ini.
                    ->schema([
                        // Bidang FileUpload untuk gambar utama.
                        Forms\Components\FileUpload::make('image')
                            ->label('Primary Image') // Label yang ditampilkan.
                            ->image() // Hanya menerima file gambar.
                            ->imageEditor() // Memungkinkan pengeditan gambar (crop, rotate).
                            // Aspek rasio yang tersedia untuk editor gambar.
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                                '3:4',
                                '9:16',
                            ])
                            ->directory('gallery/images') // Direktori penyimpanan file di storage.
                            ->visibility('public') // File akan dapat diakses secara publik.
                            ->maxSize(10240) // Ukuran file maksimum dalam KB (10MB).
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']) // Tipe file yang diterima.
                            ->helperText('Maximum size: 10MB. Supported formats: JPEG, PNG, WebP') // Teks bantuan.
                            ->columnSpanFull(), // Mengambil seluruh lebar kolom.
                    ]),
            ])
            ->columns(1); // Menetapkan form menggunakan 1 kolom utama.
    }

    /**
     * Mendefinisikan skema tabel untuk menampilkan daftar item galeri.
     *
     * @param Table $table Objek Table yang akan dikonfigurasi.
     * @return Table Skema tabel yang sudah dikonfigurasi.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom untuk menampilkan gambar.
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image') // Label kolom.
                    ->circular() // Menampilkan gambar dalam bentuk lingkaran.
                    ->size(60) // Ukuran gambar.
                    ->defaultImageUrl(url('/images/placeholder.jpg')), // Gambar placeholder jika tidak ada gambar.

                // Kolom teks untuk judul galeri.
                Tables\Columns\TextColumn::make('name')
                    ->label('Title') // Label kolom.
                    ->searchable() // Memungkinkan pencarian.
                    ->sortable() // Memungkinkan pengurutan.
                    ->weight(FontWeight::SemiBold) // Ketebalan font.
                    // Menampilkan potongan deskripsi sebagai deskripsi di bawah judul.
                    ->description(fn(Gallery $record): string => Str::limit($record->description, 50)),

                // Kolom untuk menampilkan nama kategori terkait.
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category') // Label kolom.
                    ->sortable() // Memungkinkan pengurutan.
                    ->searchable() // Memungkinkan pencarian.
                    ->badge() // Menampilkan nama kategori sebagai badge.
                    // Menentukan warna badge berdasarkan atribut 'color' dari kategori (jika ada).
                    ->color(fn(Gallery $record): string => match ($record->category?->color) {
                        null => 'gray', // Default jika tidak ada warna kategori.
                        default => 'primary', // Menggunakan warna 'primary' dari kategori.
                    }),

                // Kolom untuk tanggal pembuatan.
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created') // Label kolom.
                    ->dateTime('M j, Y H:i') // Format tampilan tanggal dan waktu.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable(isToggledHiddenByDefault: true), // Tersembunyi secara default.

                // Kolom untuk tanggal terakhir diperbarui.
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated') // Label kolom.
                    ->dateTime('M j, Y H:i') // Format tampilan tanggal dan waktu.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable(isToggledHiddenByDefault: true), // Tersembunyi secara default.
            ])
            ->filters([
                // Filter untuk melihat record yang sudah dihapus (soft delete).
                Tables\Filters\TrashedFilter::make()
                    ->label('Deleted Records'),

                // Filter Select untuk memilih kategori.
                Tables\Filters\SelectFilter::make('gallery_category_id')
                    ->label('Category') // Label filter.
                    ->relationship('category', 'name') // Mengambil opsi dari relasi 'category'.
                    ->searchable() // Memungkinkan pencarian di dalam dropdown filter.
                    ->preload(), // Memuat semua opsi saat filter dibuka.

                // Filter untuk item galeri yang memiliki gambar.
                Tables\Filters\Filter::make('has_image')
                    ->label('Has Image') // Label filter.
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('image')), // Query: hanya ambil yang kolom 'image' tidak null.

                // Filter berdasarkan rentang tanggal pembuatan.
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
                // Aksi untuk melihat detail record.
                Tables\Actions\ViewAction::make()
                    ->iconButton() // Tampilkan sebagai tombol icon.
                    ->modalWidth(MaxWidth::FiveExtraLarge), // Mengatur lebar modal tampilan.

                // Aksi untuk mengedit record.
                Tables\Actions\EditAction::make()
                    ->iconButton(), // Tampilkan sebagai tombol icon.

                // Aksi kustom untuk menduplikasi item galeri.
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplicate') // Label tombol.
                    ->icon('heroicon-o-document-duplicate') // Icon tombol.
                    ->color('gray') // Warna tombol.
                    ->action(function (Gallery $record) {
                        $newRecord = $record->replicate(); // Menduplikasi record.
                        $newRecord->name = $record->name . ' (Copy)'; // Menambahkan '(Copy)' pada nama duplikat.
                        $newRecord->save(); // Menyimpan record baru.
                    })
                    ->successNotificationTitle('Gallery item duplicated successfully'), // Notifikasi sukses setelah duplikasi.

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
                    // Aksi massal kustom untuk memperbarui kategori.
                    Tables\Actions\BulkAction::make('updateCategory')
                        ->label('Update Category') // Label aksi.
                        ->icon('heroicon-o-tag') // Icon aksi.
                        ->form([
                            Forms\Components\Select::make('gallery_category_id')
                                ->label('New Category')
                                ->relationship('category', 'name')
                                ->required(),
                        ])
                        ->action(function (array $data, \Illuminate\Database\Eloquent\Collection $records) {
                            // Memperbarui kategori untuk semua record yang dipilih.
                            $records->each->update(['gallery_category_id' => $data['gallery_category_id']]);
                        })
                        ->deselectRecordsAfterCompletion(), // Batalkan pilihan record setelah aksi selesai.
                ]),
            ])
            ->defaultSort('created_at', 'desc') // Mengatur pengurutan default tabel berdasarkan tanggal pembuatan (terbaru duluan).
            // Modifikasi query utama tabel untuk menyertakan record yang dihapus (soft delete).
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->withoutGlobalScopes([SoftDeletingScope::class])
            )
            ->emptyStateActions([
                // Aksi yang ditampilkan saat tabel kosong.
                Tables\Actions\CreateAction::make()
                    ->label('Upload First Gallery Item') // Label tombol.
                    ->icon('heroicon-o-plus'), // Icon tombol.
            ])
            ->emptyStateHeading('No gallery items found') // Judul saat tabel kosong.
            ->emptyStateDescription('Start building your gallery by uploading your first image or creating a new gallery item.') // Deskripsi saat tabel kosong.
            ->emptyStateIcon('heroicon-o-camera'); // Icon saat tabel kosong.
    }

    /**
     * Mendefinisikan skema Infolist untuk menampilkan detail satu item galeri.
     * Infolist digunakan untuk tampilan "View" record.
     *
     * @param Infolist $infolist Objek Infolist yang akan dikonfigurasi.
     * @return Infolist Skema infolist yang sudah dikonfigurasi.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Bagian untuk menampilkan pratinjau gambar galeri.
                Infolists\Components\Section::make('Gallery Preview')
                    ->schema([
                        Infolists\Components\ImageEntry::make('image')
                            ->label('') // Tanpa label karena gambar sudah cukup jelas.
                            ->size(400) // Ukuran gambar.
                            ->columnSpanFull(), // Mengambil seluruh lebar kolom.
                    ]),

                // Bagian untuk informasi dasar item galeri.
                Infolists\Components\Section::make('Basic Information')
                    ->schema([
                        // Tata letak grid dengan 2 kolom.
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                // Tampilan teks untuk judul galeri.
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Title') // Label yang ditampilkan.
                                    ->weight(FontWeight::Bold) // Ketebalan font.
                                    ->size('lg'), // Ukuran font besar.

                                // Tampilan teks untuk nama kategori terkait.
                                Infolists\Components\TextEntry::make('category.name')
                                    ->label('Category') // Label yang ditampilkan.
                                    ->badge(), // Menampilkan sebagai badge.
                            ]),

                        // Tampilan teks untuk deskripsi galeri.
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description') // Label yang ditampilkan.
                            ->html() // Memungkinkan rendering HTML (jika deskripsi mengandung HTML).
                            ->columnSpanFull(), // Mengambil seluruh lebar kolom.
                    ]),

                // Bagian untuk informasi sistem (timestamps).
                Infolists\Components\Section::make('System Information')
                    ->schema([
                        // Tata letak grid dengan 3 kolom.
                        Infolists\Components\Grid::make(3)
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
            // Contoh: RelationManagers\CommentsRelationManager::class,
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
            'index' => Pages\ListGalleries::route('/'), // Halaman daftar item galeri.
            'create' => Pages\CreateGallery::route('/create'), // Halaman pembuatan item galeri baru.
            'view' => Pages\ViewGallery::route('/{record}'), // Halaman melihat detail item galeri.
            'edit' => Pages\EditGallery::route('/{record}/edit'), // Halaman mengedit item galeri.
        ];
    }

    // Global search configuration
    /**
     * Mengatur query Eloquent yang digunakan untuk pencarian global.
     * Menambahkan eager loading relasi 'category' untuk efisiensi.
     *
     * @return Builder
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category']);
    }

    /**
     * Mendapatkan atribut model yang akan dicari dalam pencarian global.
     *
     * @return array Atribut yang dapat dicari.
     */
    public static function getGlobalSearchAttributes(): array
    {
        return ['name', 'description'];
    }

    /**
     * Mendapatkan detail tambahan yang akan ditampilkan di hasil pencarian global.
     * Menambahkan nama kategori sebagai detail tambahan.
     *
     * @param Model $record Record yang ditemukan.
     * @return array Detail tambahan.
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Category' => $record->category?->name, // Menampilkan nama kategori.
        ];
    }

    /**
     * Mendapatkan badge yang akan ditampilkan di samping item navigasi.
     * Menampilkan jumlah total item galeri.
     *
     * @return string|null Jumlah item galeri.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * Mendapatkan warna badge navigasi berdasarkan jumlah item galeri.
     * Warna akan berubah tergantung pada kuantitas record.
     *
     * @return string|null Warna badge (e.g., 'primary', 'success', 'warning').
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();

        if ($count > 10) {
            return 'success'; // Lebih dari 10 item: hijau.
        }

        if ($count > 5) {
            return 'warning'; // Lebih dari 5 item: kuning.
        }

        return 'primary'; // 5 item atau kurang: biru.
    }
}
