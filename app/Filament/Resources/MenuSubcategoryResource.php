<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuSubcategoryResource\Pages;
use App\Filament\Resources\MenuSubcategoryResource\RelationManagers;
use App\Models\MenuSubcategory;
use App\Models\MenuCategory; // Diperlukan untuk field Select relationship
use App\Models\MenuAddon; // Diperlukan untuk field Select relationship
use Filament\Forms;
use Filament\Forms\Components\FileUpload; // Untuk mengunggah file PDF
use Filament\Forms\Components\Select; // Untuk dropdown pemilihan kategori dan add-on
use Filament\Forms\Components\TextInput; // Untuk input teks nama subkategori
use Filament\Forms\Components\Section; // Untuk mengelompokkan field dalam formulir
use Filament\Forms\Components\Grid; // Untuk tata letak kolom dalam section
use Filament\Forms\Form; // Kelas dasar untuk mendefinisikan formulir
use Filament\Resources\Resource; // Kelas dasar untuk resource Filament
use Filament\Tables;
use Filament\Tables\Columns\TextColumn; // Kolom teks di tabel
use Filament\Tables\Columns\BadgeColumn; // Kolom badge untuk status
use Filament\Tables\Columns\IconColumn; // Kolom ikon untuk indikator PDF
use Filament\Tables\Table; // Kelas dasar untuk mendefinisikan tabel
use Illuminate\Database\Eloquent\Builder; // Untuk query builder Eloquent
use Illuminate\Database\Eloquent\SoftDeletingScope; // Untuk menangani soft deletes
use Filament\Support\Enums\FontWeight; // Untuk mengatur ketebalan font pada TextColumn
use Illuminate\Support\HtmlString; // Digunakan untuk menampilkan HTML di modal view
use Filament\Tables\Filters\SelectFilter; // Filter dropdown di tabel

/**
 * Kelas MenuSubcategoryResource
 *
 * Mengatur tampilan dan fungsionalitas CRUD (Create, Read, Update, Delete)
 * untuk model MenuSubcategory (subkategori menu) di dalam Filament Admin Panel.
 */
class MenuSubcategoryResource extends Resource
{
    /**
     * Menentukan model Eloquent yang terkait dengan resource ini.
     * Dalam kasus ini, modelnya adalah App\Models\MenuSubcategory.
     *
     * @var string|null
     */
    protected static ?string $model = MenuSubcategory::class;

    /**
     * Icon navigasi yang akan ditampilkan di sidebar Filament.
     * Menggunakan icon 'heroicon-o-folder-open'.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    /**
     * Label navigasi yang akan ditampilkan di sidebar Filament.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Menu Subcategories';

    /**
     * Label untuk satu instance model (singular).
     *
     * @var string|null
     */
    protected static ?string $modelLabel = 'Menu Subcategory';

    /**
     * Label untuk beberapa instance model (plural).
     *
     * @var string|null
     */
    protected static ?string $pluralModelLabel = 'Menu Subcategories';

    /**
     * Grup navigasi tempat resource ini akan ditempatkan di sidebar.
     * Membantu dalam mengorganisir navigasi admin.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Menu Management';

    /**
     * Urutan navigasi di dalam grupnya. Angka yang lebih kecil akan tampil lebih dulu.
     * Ini diatur sebagai yang ketiga di grup 'Menu Management'.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 3;

    /**
     * Mendefinisikan skema formulir untuk membuat atau mengedit subkategori menu.
     *
     * @param Form $form Objek Form yang akan dikonfigurasi.
     * @return Form Skema formulir yang sudah dikonfigurasi.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            // Bagian untuk informasi dasar subkategori.
            Section::make('Subcategory Information')
                ->description('Configure the basic information for this subcategory') // Deskripsi singkat untuk bagian ini.
                ->icon('heroicon-m-folder-open') // Icon untuk bagian ini.
                ->schema([
                    Grid::make(2) // Grid dengan 2 kolom.
                        ->schema([
                            // Bidang Select untuk memilih kategori induk.
                            Select::make('menu_category_id')
                                ->label('Parent Category') // Label yang ditampilkan.
                                ->relationship('category', 'name') // Mengambil data dari relasi 'category' dengan kolom 'name'.
                                ->required() // Wajib diisi.
                                ->searchable() // Memungkinkan pencarian dalam dropdown.
                                ->preload() // Memuat semua opsi di awal.
                                ->live() // Memperbarui bidang lain secara live.
                                ->placeholder('Select a parent category') // Placeholder teks.
                                ->helperText('Choose which main category this subcategory belongs to') // Teks bantuan.
                                ->createOptionForm([ // Formulir untuk membuat kategori baru langsung dari dropdown.
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter category name'),
                                ])
                                ->createOptionModalHeading('Create New Category') // Judul modal untuk membuat kategori baru.
                                ->editOptionForm([ // Formulir untuk mengedit kategori langsung dari dropdown.
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                ])
                                ->options(function () { // Secara eksplisit mengambil opsi dari model MenuCategory.
                                    return MenuCategory::pluck('name', 'id');
                                }),

                            // Bidang input teks untuk nama subkategori.
                            TextInput::make('name')
                                ->label('Subcategory Name') // Label yang ditampilkan.
                                ->required() // Wajib diisi.
                                ->maxLength(255) // Batas karakter maksimum.
                                ->placeholder('e.g., Appetizers, Main Course, Desserts...') // Placeholder teks.
                                ->helperText('Enter a descriptive name for this subcategory') // Teks bantuan.
                                ->live(onBlur: true) // Memperbarui bidang lain secara live saat input ini kehilangan fokus.
                                // Callback setelah state diperbarui untuk secara otomatis membuat slug dari nama.
                                ->afterStateUpdated(function (string $context, $state, callable $set) {
                                    if ($context === 'create') {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                }),
                        ]),
                ])
                ->columns(2) // Bagian ini menggunakan 2 kolom.
                ->collapsible(), // Memungkinkan bagian untuk diciutkan/dibentangkan.

            // Bagian untuk add-on opsional dan sumber daya (PDF).
            Section::make('Optional Add-on & Resources')
                ->description('Link an add-on and upload menu PDF if available') // Deskripsi singkat.
                ->icon('heroicon-m-plus') // Icon untuk bagian ini.
                ->schema([
                    Grid::make(2) // Grid dengan 2 kolom.
                        ->schema([
                            // Bidang Select untuk memilih add-on terkait.
                            Select::make('menu_addon_id')
                                ->label('Related Add-on') // Label yang ditampilkan.
                                ->relationship('addon', 'title') // Mengambil data dari relasi 'addon' dengan kolom 'title'.
                                ->nullable() // Bidang ini boleh kosong.
                                ->searchable() // Memungkinkan pencarian dalam dropdown.
                                ->preload() // Memuat semua opsi di awal.
                                ->placeholder('Select an add-on (optional)') // Placeholder teks.
                                ->helperText('Optional: Choose an add-on that goes with this subcategory') // Teks bantuan.
                                ->createOptionForm([ // Formulir untuk membuat add-on baru langsung dari dropdown.
                                    TextInput::make('title')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter add-on title'),
                                ])
                                ->createOptionModalHeading('Create New Add-on') // Judul modal untuk membuat add-on baru.
                                ->options(function () { // Secara eksplisit mengambil opsi dari model MenuAddon.
                                    return MenuAddon::pluck('title', 'id');
                                }),

                            // Bidang FileUpload untuk mengunggah PDF menu.
                            FileUpload::make('pdf_path')
                                ->label('Menu PDF') // Label yang ditampilkan.
                                ->directory('menu-pdfs') // Direktori penyimpanan file.
                                ->acceptedFileTypes(['application/pdf']) // Hanya menerima file PDF.
                                ->maxSize(10240) // Ukuran maksimum file (10MB).
                                ->preserveFilenames() // Mempertahankan nama file asli.
                                ->nullable() // Bidang ini boleh kosong.
                                ->helperText('Optional: Upload a PDF menu for this subcategory (Max: 10MB)') // Teks bantuan.
                                ->downloadable() // Memungkinkan file untuk diunduh.
                                ->previewable(false) // Tidak menampilkan pratinjau (karena PDF).
                                ->openable() // Memungkinkan file untuk dibuka.
                                ->deletable() // Memungkinkan file untuk dihapus.
                                ->moveFiles() // Memindahkan file setelah upload selesai.
                                ->uploadingMessage('Uploading PDF...') // Pesan saat mengunggah.
                                ->uploadProgressIndicatorPosition('left'), // Posisi indikator progres upload.
                        ]),
                ])
                ->columns(2) // Bagian ini menggunakan 2 kolom.
                ->collapsible() // Memungkinkan bagian untuk diciutkan/dibentangkan.
                // Kondisi untuk awalannya ciut (collapsed) atau bentang (expanded).
                // Jika record baru (null) atau tidak ada add-on/PDF, maka dibentangkan.
                ->collapsed(fn($record) => $record === null ? false : (empty($record->menu_addon_id) && empty($record->pdf_path))),
        ]);
    }

    /**
     * Mendefinisikan skema tabel untuk menampilkan daftar subkategori menu.
     *
     * @param Table $table Objek Table yang akan dikonfigurasi.
     * @return Table Skema tabel yang sudah dikonfigurasi.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom teks untuk nama subkategori.
                TextColumn::make('name')
                    ->label('Subcategory Name') // Label kolom.
                    ->searchable() // Memungkinkan pencarian.
                    ->sortable() // Memungkinkan pengurutan.
                    ->weight(FontWeight::SemiBold) // Ketebalan font.
                    ->color('primary') // Warna teks.
                    ->icon('heroicon-m-folder-open') // Icon di samping teks.
                    ->copyable() // Memungkinkan menyalin teks kolom.
                    ->copyMessage('Name copied!') // Pesan saat disalin.
                    ->copyMessageDuration(1500), // Durasi pesan dalam ms.

                // Kolom teks untuk nama kategori induk (dari relasi).
                TextColumn::make('category.name')
                    ->label('Parent Category') // Label kolom.
                    ->searchable() // Memungkinkan pencarian.
                    ->sortable() // Memungkinkan pengurutan.
                    ->badge() // Menampilkan sebagai badge.
                    ->color('info') // Warna badge.
                    ->icon('heroicon-m-tag') // Icon di samping teks.
                    ->placeholder('No category') // Placeholder jika tidak ada kategori.
                    ->tooltip('Click to filter by this category'), // Tooltip saat hover.

                // Kolom teks untuk judul add-on terkait (dari relasi).
                TextColumn::make('addon.title')
                    ->label('Linked Add-on') // Label kolom.
                    ->searchable() // Memungkinkan pencarian.
                    ->toggleable() // Dapat disembunyikan/ditampilkan.
                    ->badge() // Menampilkan sebagai badge.
                    ->color('success') // Warna badge.
                    ->icon('heroicon-m-plus-circle') // Icon di samping teks.
                    ->placeholder('No add-on') // Placeholder jika tidak ada add-on.
                    ->limit(30) // Batasi teks yang ditampilkan.
                    // Menambahkan tooltip yang menampilkan judul add-on lengkap jika terpotong.
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (!$state || strlen($state) <= 30) {
                            return null; // Tidak perlu tooltip jika teks tidak terpotong atau kosong.
                        }
                        return $state; // Tampilkan teks lengkap di tooltip.
                    }),

                // Kolom ikon untuk menunjukkan keberadaan PDF.
                IconColumn::make('has_pdf')
                    ->label('PDF') // Label kolom.
                    ->getStateUsing(fn($record) => !empty($record->pdf_path)) // Mengambil boolean berdasarkan keberadaan pdf_path.
                    ->boolean() // Menampilkan sebagai ikon boolean (centang/silang).
                    ->trueIcon('heroicon-o-document-text') // Ikon jika true (ada PDF).
                    ->falseIcon('heroicon-o-x-mark') // Ikon jika false (tidak ada PDF).
                    ->trueColor('success') // Warna ikon jika true.
                    ->falseColor('gray') // Warna ikon jika false.
                    ->tooltip(function ($record) { // Tooltip dinamis.
                        return $record->pdf_path ? 'PDF available' : 'No PDF uploaded';
                    }),

                // Kolom badge untuk menampilkan status kelengkapan data subkategori.
                BadgeColumn::make('status')
                    ->label('Status') // Label kolom.
                    // Mengambil state status berdasarkan kondisi record.
                    ->getStateUsing(function ($record) {
                        if ($record->deleted_at) {
                            return 'deleted'; // Jika record di-soft-delete.
                        }

                        $score = 0; // Menghitung kelengkapan data.
                        if ($record->menu_category_id) $score++;
                        if ($record->menu_addon_id) $score++;
                        if ($record->pdf_path) $score++;

                        // Menentukan status berdasarkan skor kelengkapan.
                        return match ($score) {
                            3 => 'complete', // Kategori, Add-on, PDF semua ada.
                            2 => 'good', // Dua dari tiga ada.
                            1 => 'basic', // Satu dari tiga ada.
                            0 => 'incomplete', // Tidak ada dari tiga.
                            default => 'unknown', // Status default.
                        };
                    })
                    // Mengatur warna badge berdasarkan state status.
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
                    // Mengatur format tampilan teks badge berdasarkan state status.
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
                    // Mengatur icon badge berdasarkan state status.
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

                // Kolom untuk tanggal pembuatan.
                TextColumn::make('created_at')
                    ->label('Created') // Label kolom.
                    ->dateTime('M j, Y') // Format tampilan tanggal.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable() // Dapat disembunyikan/ditampilkan.
                    ->color('gray') // Warna teks.
                    ->icon('heroicon-m-calendar'), // Icon.

                // Kolom untuk tanggal terakhir diperbarui.
                TextColumn::make('updated_at')
                    ->label('Updated') // Label kolom.
                    ->dateTime('M j, Y') // Format tampilan tanggal.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable(isToggledHiddenByDefault: true) // Tersembunyi secara default.
                    ->color('gray') // Warna teks.
                    ->since() // Menampilkan "sejak kapan" (e.g., "3 hours ago").
                    ->icon('heroicon-m-clock'), // Icon.

                // Kolom untuk tanggal penghapusan (jika soft delete diaktifkan).
                TextColumn::make('deleted_at')
                    ->label('Deleted') // Label kolom.
                    ->dateTime('M j, Y') // Format tampilan tanggal.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable(isToggledHiddenByDefault: true) // Tersembunyi secara default.
                    ->color('danger') // Warna teks.
                    ->placeholder('—') // Teks placeholder jika null.
                    ->icon('heroicon-m-trash'), // Icon.
            ])
            ->filters([
                // Filter untuk melihat record yang sudah dihapus (soft delete).
                Tables\Filters\TrashedFilter::make()
                    ->label('Show Deleted Records')
                    ->placeholder('All Records')
                    ->trueLabel('Only Deleted')
                    ->falseLabel('Without Deleted'),

                // Filter dropdown untuk kategori induk.
                SelectFilter::make('menu_category_id')
                    ->label('Filter by Category')
                    ->relationship('category', 'name') // Mengambil opsi dari relasi 'category'.
                    ->searchable()
                    ->preload()
                    ->multiple() // Memungkinkan pemilihan banyak kategori.
                    ->placeholder('All Categories'),

                // Filter dropdown untuk add-on terkait.
                SelectFilter::make('menu_addon_id')
                    ->label('Filter by Add-on')
                    ->relationship('addon', 'title') // Mengambil opsi dari relasi 'addon'.
                    ->searchable()
                    ->preload()
                    ->multiple() // Memungkinkan pemilihan banyak add-on.
                    ->placeholder('All Add-ons'),

                // Filter toggle untuk subkategori yang memiliki add-on.
                Tables\Filters\Filter::make('has_addon')
                    ->label('Has Add-on')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('menu_addon_id'))
                    ->toggle(),

                // Filter toggle untuk subkategori yang memiliki PDF.
                Tables\Filters\Filter::make('has_pdf')
                    ->label('Has PDF')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('pdf_path'))
                    ->toggle(),

                // Filter toggle untuk subkategori yang memiliki setup lengkap (kategori, add-on, PDF).
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
                // Aksi untuk melihat detail record.
                Tables\Actions\ViewAction::make()
                    ->color('info') // Warna tombol.
                    // Menyesuaikan judul modal view.
                    ->modalHeading(fn($record) => "View Subcategory: {$record->name}")
                    // Menyesuaikan konten modal view dengan HTML kustom.
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

                // Aksi untuk mengedit record.
                Tables\Actions\EditAction::make()
                    ->color('warning'), // Warna tombol.

                // Aksi kustom untuk mengunduh PDF.
                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF') // Label tombol.
                    ->icon('heroicon-o-arrow-down-tray') // Ikon.
                    ->color('success') // Warna tombol.
                    ->visible(fn($record) => !empty($record->pdf_path)) // Hanya terlihat jika ada PDF.
                    ->url(fn($record) => asset('storage/' . $record->pdf_path)) // URL untuk mengunduh PDF.
                    ->openUrlInNewTab(), // Membuka URL di tab baru.

                // Aksi untuk menghapus record (soft delete).
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation() // Meminta konfirmasi sebelum menghapus.
                    ->modalHeading('Delete Subcategory')
                    ->modalDescription(fn($record) => "Are you sure you want to delete '{$record->name}'? This action can be undone.") // Pesan konfirmasi dinamis.
                    ->modalSubmitActionLabel('Yes, delete it'),

                // Aksi untuk menghapus record secara permanen (force delete).
                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation() // Meminta konfirmasi sebelum menghapus permanen.
                    ->modalHeading('Permanently Delete Subcategory')
                    ->modalDescription(fn($record) => "Are you sure you want to permanently delete '{$record->name}'? This action cannot be undone.") // Pesan konfirmasi dinamis.
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
                        ->requiresConfirmation() // Meminta konfirmasi.
                        ->modalHeading('Delete selected subcategories')
                        ->modalDescription('Are you sure you want to delete the selected subcategories? This action can be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),

                    // Aksi hapus permanen massal.
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation() // Meminta konfirmasi.
                        ->modalHeading('Permanently delete selected subcategories')
                        ->modalDescription('Are you sure you want to permanently delete the selected subcategories? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),

                    // Aksi kembalikan massal.
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Aksi yang ditampilkan saat tabel kosong.
                Tables\Actions\CreateAction::make()
                    ->label('Create your first subcategory')
                    ->icon('heroicon-m-plus'),
            ])
            ->emptyStateHeading('No menu subcategories yet') // Judul saat tabel kosong.
            ->emptyStateDescription('Once you create your first menu subcategory, it will appear here. Subcategories help organize your menu items.') // Deskripsi saat tabel kosong.
            ->emptyStateIcon('heroicon-o-folder-open') // Icon saat tabel kosong.
            ->striped() // Menambahkan striping pada baris tabel.
            ->defaultSort('created_at', 'desc') // Mengatur pengurutan default tabel berdasarkan tanggal pembuatan (terbaru duluan).
            ->recordTitleAttribute('name') // Menentukan atribut yang digunakan sebagai judul record di beberapa UI Filament.
            // Modifikasi query utama tabel untuk menyertakan record yang dihapus (soft delete).
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->withoutGlobalScopes([SoftDeletingScope::class])
            );
    }

    /**
     * Mendapatkan daftar Relation Managers yang terkait dengan resource ini.
     * Relation Managers memungkinkan pengelolaan relasi model langsung dari halaman resource.
     * Saat ini tidak ada Relation Manager yang didefinisikan secara eksplisit di sini.
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
            'index' => Pages\ListMenuSubcategories::route('/'), // Halaman daftar subkategori menu.
            'create' => Pages\CreateMenuSubcategory::route('/create'), // Halaman pembuatan subkategori menu baru.
            'view' => Pages\ViewMenuSubcategory::route('/{record}'), // Halaman melihat detail subkategori menu.
            'edit' => Pages\EditMenuSubcategory::route('/{record}/edit'), // Halaman mengedit subkategori menu.
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
     * Menampilkan jumlah total subkategori menu.
     *
     * @return string|null Jumlah subkategori menu.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * Mendapatkan warna badge navigasi berdasarkan jumlah subkategori menu.
     * Warna akan berubah tergantung pada kuantitas record.
     *
     * @return string|null Warna badge (e.g., 'primary', 'success', 'warning').
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();

        if ($count > 15) {
            return 'success'; // Lebih dari 15 subkategori: hijau.
        }

        if ($count > 8) {
            return 'warning'; // Lebih dari 8 subkategori: kuning.
        }

        return 'primary'; // 8 subkategori atau kurang: biru.
    }

    /**
     * Mengatur query Eloquent untuk pencarian global.
     * Memuat relasi 'category' dan 'addon' untuk memungkinkan pencarian berdasarkan nama/judul mereka.
     *
     * @return Builder
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category', 'addon']);
    }

    /**
     * Mendapatkan atribut yang dapat dicari secara global.
     * Ini mencakup nama subkategori, nama kategori induk, dan judul add-on.
     *
     * @return array Daftar atribut yang dapat dicari.
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'category.name', 'addon.title'];
    }

    /**
     * Mendapatkan detail hasil pencarian global yang akan ditampilkan.
     * Menampilkan kategori induk, add-on terkait, dan status ketersediaan PDF.
     *
     * @param mixed $record Record yang ditemukan.
     * @return array Detail yang ditampilkan.
     */
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