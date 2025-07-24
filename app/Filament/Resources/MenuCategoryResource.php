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

/**
 * Kelas MenuCategoryResource
 *
 * Mengatur tampilan dan fungsionalitas CRUD (Create, Read, Update, Delete)
 * untuk model MenuCategory (kategori menu) di dalam Filament Admin Panel.
 */
class MenuCategoryResource extends Resource
{
    /**
     * Menentukan model Eloquent yang terkait dengan resource ini.
     * Dalam kasus ini, modelnya adalah App\Models\MenuCategory.
     *
     * @var string|null
     */
    protected static ?string $model = MenuCategory::class;

    /**
     * Icon navigasi yang akan ditampilkan di sidebar Filament.
     * Menggunakan icon 'heroicon-o-squares-2x2'.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    /**
     * Label navigasi yang akan ditampilkan di sidebar Filament.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Menu Categories';

    /**
     * Label untuk satu instance model (singular).
     *
     * @var string|null
     */
    protected static ?string $modelLabel = 'Menu Category';

    /**
     * Label untuk beberapa instance model (plural).
     *
     * @var string|null
     */
    protected static ?string $pluralModelLabel = 'Menu Categories';

    /**
     * Grup navigasi tempat resource ini akan ditempatkan di sidebar.
     * Membantu dalam mengorganisir navigasi admin.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Menu Management';

    /**
     * Urutan navigasi di dalam grupnya. Angka yang lebih kecil akan tampil lebih dulu.
     * Ini diatur sebagai yang pertama di grup 'Menu Management'.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 1;

    /**
     * Mendefinisikan skema formulir untuk membuat atau mengedit kategori menu.
     *
     * @param Form $form Objek Form yang akan dikonfigurasi.
     * @return Form Skema formulir yang sudah dikonfigurasi.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            // Bagian untuk informasi dasar kategori.
            Section::make('Category Information')
                ->description('Enter the basic information for the menu category') // Deskripsi singkat untuk bagian ini.
                ->icon('heroicon-m-information-circle') // Icon untuk bagian ini.
                ->schema([
                    // Bidang input teks untuk nama kategori.
                    TextInput::make('name')
                        ->label('Category Name') // Label yang ditampilkan.
                        ->required() // Wajib diisi.
                        ->maxLength(255) // Batas karakter maksimum.
                        ->placeholder('Enter category name...') // Placeholder teks.
                        ->helperText('This will be displayed as the category title') // Teks bantuan.
                        ->columnSpanFull(), // Mengambil seluruh lebar kolom yang tersedia.
                ])
                ->columns(1) // Bagian ini menggunakan 1 kolom.
                ->collapsible(), // Memungkinkan bagian untuk diciutkan/dibentangkan.

            // Bagian untuk mengelola gambar galeri terkait dengan kategori.
            Section::make('Gallery Images')
                ->description('Add images to showcase this menu category') // Deskripsi singkat.
                ->icon('heroicon-m-photo') // Icon untuk bagian ini.
                ->schema([
                    // Repeater memungkinkan penambahan beberapa set input untuk galeri.
                    Repeater::make('galleries')
                        ->relationship() // Menunjukkan bahwa repeater ini mengelola relasi (misalnya, `hasMany` ke model Gallery).
                        ->label('') // Tanpa label utama untuk repeater ini.
                        ->schema([
                            // Grid internal dalam repeater untuk setiap item galeri.
                            Grid::make(2)
                                ->schema([
                                    // Input teks untuk judul gambar galeri.
                                    TextInput::make('name')
                                        ->label('Image Title') // Label yang ditampilkan.
                                        ->placeholder('Enter image title...')
                                        ->helperText('Optional: Add a descriptive title for this image')
                                        ->maxLength(255),

                                    // Upload file untuk gambar galeri.
                                    FileUpload::make('image')
                                        ->label('Upload Image') // Label yang ditampilkan.
                                        ->image() // Hanya menerima file gambar.
                                        ->directory('menu_categories/gallery') // Direktori penyimpanan file di storage.
                                        ->imageEditor() // Memungkinkan pengeditan gambar (crop, rotate).
                                        // Aspek rasio yang tersedia untuk editor gambar.
                                        ->imageEditorAspectRatios([
                                            '16:9',
                                            '4:3',
                                            '1:1',
                                            '3:4',
                                        ])
                                        ->maxSize(5120) // Ukuran file maksimum dalam KB (5MB).
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']) // Tipe file yang diterima.
                                        ->helperText('Max size: 5MB. Formats: JPEG, PNG, WebP')
                                        ->imagePreviewHeight('200') // Tinggi pratinjau gambar.
                                        ->loadingIndicatorPosition('left') // Posisi indikator loading.
                                        ->panelAspectRatio('2:1') // Rasio aspek panel upload.
                                        ->panelLayout('integrated') // Tata letak panel upload.
                                        ->removeUploadedFileButtonPosition('right') // Posisi tombol hapus file.
                                        ->uploadButtonPosition('left') // Posisi tombol upload.
                                        ->uploadProgressIndicatorPosition('left'), // Posisi indikator progres upload.
                                ])
                        ])
                        // Menentukan label untuk setiap item di repeater berdasarkan nilai 'name' di dalamnya.
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Gallery Image')
                        ->addActionLabel('Add New Image') // Label tombol untuk menambah item baru.
                        ->reorderableWithButtons() // Memungkinkan pengurutan item dengan tombol.
                        ->collapsible() // Setiap item di repeater dapat diciutkan.
                        ->cloneable() // Memungkinkan duplikasi item.
                        // Mengonfirmasi penghapusan item di repeater.
                        ->deleteAction(
                            fn($action) => $action->requiresConfirmation()
                        )
                        ->defaultItems(0) // Jumlah item default saat form baru dibuka.
                        ->minItems(0) // Jumlah item minimum.
                        ->maxItems(10) // Jumlah item maksimum.
                        ->grid(1) // Tata letak grid untuk repeater itu sendiri.
                        ->columnSpanFull(), // Mengambil seluruh lebar kolom yang tersedia.
                ])
                ->collapsible() // Bagian Gallery Images dapat diciutkan/dibentangkan.
                ->collapsed(false), // Awalnya dibentangkan (tidak diciutkan).
        ]);
    }

    /**
     * Mendefinisikan skema tabel untuk menampilkan daftar kategori menu.
     *
     * @param Table $table Objek Table yang akan dikonfigurasi.
     * @return Table Skema tabel yang sudah dikonfigurasi.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom teks untuk nama kategori.
                TextColumn::make('name')
                    ->label('Category Name') // Label kolom.
                    ->searchable() // Memungkinkan pencarian.
                    ->sortable() // Memungkinkan pengurutan.
                    ->weight(FontWeight::SemiBold) // Ketebalan font.
                    ->color('primary') // Warna teks.
                    ->icon('heroicon-m-tag'), // Icon di samping teks.

                // Kolom gambar untuk pratinjau galeri terkait.
                ImageColumn::make('galleries.image')
                    ->label('Gallery Preview') // Label kolom.
                    ->circular() // Menampilkan gambar dalam bentuk lingkaran.
                    ->stacked() // Gambar ditampilkan bertumpuk (untuk banyak gambar).
                    ->limit(3) // Batasi jumlah gambar yang ditampilkan.
                    ->limitedRemainingText() // Menampilkan teks seperti "+2 lainnya".
                    ->size(40) // Ukuran gambar.
                    ->defaultImageUrl(url('/images/placeholder.png')), // Gambar placeholder jika tidak ada gambar.

                // Kolom badge untuk menampilkan jumlah gambar di galeri kategori.
                BadgeColumn::make('galleries_count')
                    ->label('Images') // Label kolom.
                    ->counts('galleries') // Menghitung jumlah relasi 'galleries'.
                    // Mengatur warna badge berdasarkan jumlah gambar.
                    ->color(static function ($state): string {
                        if ($state === 0) {
                            return 'gray'; // Jika tidak ada gambar.
                        }
                        if ($state <= 3) {
                            return 'warning'; // Jika 1-3 gambar.
                        }
                        return 'success'; // Jika lebih dari 3 gambar.
                    })
                    // Mengatur icon badge berdasarkan jumlah gambar.
                    ->icon(static function ($state): string {
                        if ($state === 0) {
                            return 'heroicon-m-photo'; // Icon jika tidak ada gambar.
                        }
                        return 'heroicon-m-camera'; // Icon jika ada gambar.
                    }),

                // Kolom untuk tanggal pembuatan.
                TextColumn::make('created_at')
                    ->label('Created') // Label kolom.
                    ->dateTime('M j, Y') // Format tampilan tanggal.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable() // Dapat disembunyikan/ditampilkan.
                    ->color('gray'), // Warna teks.

                // Kolom untuk tanggal terakhir diperbarui.
                TextColumn::make('updated_at')
                    ->label('Updated') // Label kolom.
                    ->dateTime('M j, Y') // Format tampilan tanggal.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable(isToggledHiddenByDefault: true) // Tersembunyi secara default.
                    ->color('gray'), // Warna teks.

                // Kolom untuk tanggal penghapusan (jika soft delete diaktifkan).
                TextColumn::make('deleted_at')
                    ->label('Deleted') // Label kolom.
                    ->dateTime('M j, Y') // Format tampilan tanggal.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable(isToggledHiddenByDefault: true) // Tersembunyi secara default.
                    ->color('danger') // Warna teks.
                    ->placeholder('—'), // Teks placeholder jika null.
            ])
            ->filters([
                // Filter untuk melihat record yang sudah dihapus (soft delete).
                Tables\Filters\TrashedFilter::make()
                    ->label('Show Deleted Records')
                    ->placeholder('All Records')
                    ->trueLabel('Only Deleted')
                    ->falseLabel('Without Deleted'),

                // Filter untuk kategori yang memiliki gambar galeri.
                Tables\Filters\Filter::make('has_images')
                    ->label('Has Images')
                    ->query(fn(Builder $query): Builder => $query->has('galleries')) // Query: hanya ambil yang memiliki relasi 'galleries'.
                    ->toggle(), // Tampilkan sebagai toggle.

                // Filter untuk kategori yang tidak memiliki gambar galeri.
                Tables\Filters\Filter::make('no_images')
                    ->label('No Images')
                    ->query(fn(Builder $query): Builder => $query->doesntHave('galleries')) // Query: hanya ambil yang tidak memiliki relasi 'galleries'.
                    ->toggle(), // Tampilkan sebagai toggle.
            ])
            ->actions([
                // Aksi untuk melihat detail record.
                Tables\Actions\ViewAction::make()
                    ->color('info'), // Warna tombol.
                // Aksi untuk mengedit record.
                Tables\Actions\EditAction::make()
                    ->color('warning'), // Warna tombol.
                // Aksi untuk menghapus record (soft delete).
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation() // Meminta konfirmasi sebelum menghapus.
                    ->modalHeading('Delete Category')
                    ->modalDescription('Are you sure you want to delete this category? This action can be undone.')
                    ->modalSubmitActionLabel('Yes, delete it'),
                // Aksi untuk menghapus record secara permanen (force delete).
                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation() // Meminta konfirmasi sebelum menghapus permanen.
                    ->modalHeading('Permanently Delete Category')
                    ->modalDescription('Are you sure you want to permanently delete this category? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete permanently'),
                // Aksi untuk mengembalikan record yang sudah dihapus.
                Tables\Actions\RestoreAction::make()
                    ->color('success'), // Warna tombol.
            ])
            ->bulkActions([
                // Grup aksi massal (bulk actions) untuk record yang dipilih.
                Tables\Actions\BulkActionGroup::make([
                    // Aksi hapus massal (soft delete).
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation() // Meminta konfirmasi sebelum hapus massal.
                        ->modalHeading('Delete selected categories')
                        ->modalDescription('Are you sure you want to delete the selected categories? This action can be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),
                    // Aksi hapus permanen massal.
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation() // Meminta konfirmasi sebelum hapus permanen massal.
                        ->modalHeading('Permanently delete selected categories')
                        ->modalDescription('Are you sure you want to permanently delete the selected categories? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),
                    // Aksi kembalikan massal.
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Aksi yang ditampilkan saat tabel kosong.
                Tables\Actions\CreateAction::make()
                    ->label('Create your first category')
                    ->icon('heroicon-m-plus'),
            ])
            ->emptyStateHeading('No menu categories yet') // Judul saat tabel kosong.
            ->emptyStateDescription('Once you create your first menu category, it will appear here.') // Deskripsi saat tabel kosong.
            ->emptyStateIcon('heroicon-o-squares-2x2') // Icon saat tabel kosong.
            ->striped() // Menambahkan striping pada baris tabel.
            ->defaultSort('created_at', 'desc') // Mengatur pengurutan default tabel berdasarkan tanggal pembuatan (terbaru duluan).
            // Modifikasi query utama tabel untuk menyertakan record yang dihapus (soft delete).
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->withoutGlobalScopes([SoftDeletingScope::class])
            );
    }

    /**
     * Mendapatkan daftar Relation Managers yang terkait dengan resource ini.
     * Relation Managers memungkinkan pengelolaan relasi model langsung dari halaman resource.
     * Saat ini tidak ada Relation Manager yang didefinisikan secara eksplisit di sini,
     * namun relasi `galleries` dikelola melalui Repeater di form.
     *
     * @return array Daftar kelas Relation Manager.
     */
    public static function getRelations(): array
    {
        return [
            //
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
            'index' => Pages\ListMenuCategories::route('/'), // Halaman daftar kategori menu.
            'create' => Pages\CreateMenuCategory::route('/create'), // Halaman pembuatan kategori menu baru.
            'view' => Pages\ViewMenuCategory::route('/{record}'), // Halaman melihat detail kategori menu.
            'edit' => Pages\EditMenuCategory::route('/{record}/edit'), // Halaman mengedit kategori menu.
        ];
    }

    /**
     * Mengatur query Eloquent global untuk resource ini.
     * Memastikan bahwa record yang di-soft-delete juga dapat diakses secara default
     * untuk resource ini, yang kemudian disesuaikan oleh filter di tabel.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Mendapatkan badge yang akan ditampilkan di samping item navigasi.
     * Menampilkan jumlah total kategori menu.
     *
     * @return string|null Jumlah kategori menu.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * Mendapatkan warna badge navigasi berdasarkan jumlah kategori menu.
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
