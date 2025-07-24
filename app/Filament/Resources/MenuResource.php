<?php

namespace App\Filament\Resources;

// Mengimpor kelas-kelas yang diperlukan dari Filament dan model-model aplikasi.
// Ini penting agar kode dapat mengakses fungsionalitas yang disediakan oleh Filament
// untuk membangun antarmuka admin, serta model Menu, MenuCategory, dan MenuSubcategory
// untuk berinteraksi dengan database.
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
use Filament\Forms\Get; // Digunakan untuk mendapatkan nilai field lain secara real-time di form
use Filament\Forms\Set; // Digunakan untuk mengatur nilai field lain secara real-time di form

// Mendefinisikan kelas MenuResource yang memperluas Filament\Resources\Resource.
// Kelas ini bertanggung jawab untuk mendefinisikan bagaimana model 'Menu' akan dikelola
// di panel admin Filament, termasuk form untuk membuat/mengedit, tabel untuk menampilkan,
// dan logika terkait lainnya.
class MenuResource extends Resource
{
    // Properti statis yang menentukan model Eloquent yang terkait dengan resource ini.
    // Filament akan menggunakan model 'Menu' untuk operasi CRUD.
    protected static ?string $model = Menu::class;

    // Properti statis yang menentukan ikon navigasi untuk resource ini di sidebar admin Filament.
    // Menggunakan ikon Heroicons 'clipboard-document-list'.
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    // Properti statis yang menentukan label navigasi yang ditampilkan di sidebar admin.
    protected static ?string $navigationLabel = 'Menus';

    // Properti statis yang menentukan label singular untuk model ini, digunakan di UI Filament.
    protected static ?string $modelLabel = 'Menu Item';

    // Properti statis yang menentukan label plural untuk model ini, digunakan di UI Filament.
    protected static ?string $pluralModelLabel = 'Menu Items';

    // Properti statis yang menentukan grup navigasi di sidebar.
    // Ini membantu mengorganisir resource ke dalam kategori yang lebih besar.
    protected static ?string $navigationGroup = 'Menu Management';

    // Properti statis yang menentukan urutan resource di dalam grup navigasinya.
    // Semakin kecil angkanya, semakin tinggi posisinya.
    protected static ?int $navigationSort = 4;

    /**
     * Mendefinisikan skema form untuk resource Menu.
     * Fungsi ini mengatur field-field yang akan muncul saat membuat atau mengedit item menu.
     *
     * @param Form $form Objek Form yang akan dikonfigurasi.
     * @return Form Objek Form yang sudah dikonfigurasi.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            // Bagian pertama form: Klasifikasi Menu.
            // Digunakan untuk mengkategorikan item menu.
            Section::make('Menu Classification')
                ->description('Organize your menu item by category and subcategory') // Deskripsi bagian.
                ->icon('heroicon-m-folder') // Ikon untuk bagian ini.
                ->schema([
                    // Menggunakan Grid dengan 2 kolom untuk menata field kategori dan subkategori.
                    Grid::make(2)
                        ->schema([
                            // Field Select untuk memilih Kategori Menu.
                            Select::make('menu_category_id')
                                ->label('Menu Category') // Label field.
                                ->relationship('category', 'name') // Menghubungkan ke model MenuCategory, menampilkan kolom 'name'.
                                ->required() // Field ini wajib diisi.
                                ->searchable() // Memungkinkan pencarian dalam daftar pilihan.
                                ->preload() // Memuat semua opsi saat form dimuat.
                                ->live() // Membuat field ini "live", artinya perubahan pada field ini akan memicu update pada field lain (misal subkategori).
                                ->placeholder('Select a category') // Placeholder untuk field.
                                ->helperText('Choose the main category for this menu item') // Teks bantuan.
                                ->afterStateUpdated(function (Set $set) {
                                    // Callback setelah nilai kategori diupdate.
                                    // Mengatur menu_subcategory_id menjadi null untuk mereset subkategori saat kategori berubah.
                                    $set('menu_subcategory_id', null);
                                })
                                ->createOptionForm([
                                    // Form mini untuk membuat kategori baru langsung dari field select.
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter category name'),
                                ])
                                ->createOptionModalHeading('Create New Category'), // Judul modal untuk membuat kategori baru.

                            // Field Select untuk memilih Subkategori Menu.
                            Select::make('menu_subcategory_id')
                                ->label('Menu Subcategory') // Label field.
                                ->relationship('subcategory', 'name') // Menghubungkan ke model MenuSubcategory, menampilkan kolom 'name'.
                                ->searchable() // Memungkinkan pencarian.
                                ->preload() // Memuat semua opsi saat form dimuat.
                                ->placeholder('Select a subcategory (optional)') // Placeholder.
                                ->helperText('Optional: Choose a more specific subcategory') // Teks bantuan.
                                ->options(function (Get $get) {
                                    // Opsi subkategori difilter berdasarkan kategori yang dipilih.
                                    // Menggunakan Get untuk mendapatkan nilai menu_category_id secara real-time.
                                    $categoryId = $get('menu_category_id');
                                    if (!$categoryId) {
                                        return []; // Jika tidak ada kategori dipilih, kembalikan array kosong.
                                    }
                                    // Mengambil subkategori yang terkait dengan categoryId yang dipilih.
                                    return MenuSubcategory::where('menu_category_id', $categoryId)
                                        ->pluck('name', 'id');
                                })
                                ->createOptionForm([
                                    // Form mini untuk membuat subkategori baru.
                                    // Membutuhkan pemilihan kategori induk terlebih dahulu.
                                    Select::make('menu_category_id')
                                        ->label('Parent Category')
                                        ->relationship('category', 'name')
                                        ->required(),
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter subcategory name'),
                                ])
                                ->createOptionModalHeading('Create New Subcategory'), // Judul modal untuk membuat subkategori baru.
                        ]),
                ])
                ->columns(2) // Menentukan layout kolom untuk konten di dalam bagian ini.
                ->collapsible(), // Memungkinkan bagian ini untuk dilipat/dibuka.

            // Bagian kedua form: Informasi Menu.
            // Berisi detail dasar tentang item menu seperti nama, harga, dan deskripsi.
            Section::make('Menu Information')
                ->description('Enter the basic details about this menu item')
                ->icon('heroicon-m-information-circle')
                ->schema([
                    // Menggunakan Grid dengan 2 kolom untuk menata nama dan harga.
                    Grid::make(2)
                        ->schema([
                            // Field TextInput untuk Nama Menu.
                            TextInput::make('name')
                                ->label('Menu Name')
                                ->required() // Wajib diisi.
                                ->maxLength(255) // Batas panjang karakter.
                                ->placeholder('e.g., Grilled Chicken Sandwich')
                                ->helperText('Enter the name of the menu item')
                                ->live(onBlur: true) // Membuat field "live" saat kehilangan fokus.
                                ->afterStateUpdated(function (string $context, $state, callable $set) {
                                    // Callback setelah nilai nama diupdate.
                                    // Jika dalam mode 'create', secara otomatis mengisi field 'slug' dari nama.
                                    if ($context === 'create') {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                })
                                ->columnSpan(1), // Mengatur rentang kolom field ini dalam grid.

                            // Field TextInput untuk Harga.
                            TextInput::make('price')
                                ->label('Price')
                                ->numeric() // Hanya menerima input numerik.
                                ->prefix('IDR') // Menambahkan prefix 'IDR'.
                                ->placeholder('0')
                                ->helperText('Enter the price in Indonesian Rupiah')
                                ->nullable() // Boleh kosong.
                                ->minValue(0) // Nilai minimum 0.
                                ->maxValue(999999999) // Nilai maksimum.
                                ->step(100) // Langkah penambahan/pengurangan nilai.
                                ->columnSpan(1),
                        ]),

                    // Field MarkdownEditor untuk Deskripsi.
                    MarkdownEditor::make('description')
                        ->label('Description')
                        ->placeholder('Describe this menu item, its ingredients, preparation method, or any special notes...')
                        ->helperText('Optional: Provide a detailed description of the menu item')
                        ->columnSpanFull() // Mengambil seluruh lebar kolom yang tersedia.
                        ->nullable() // Boleh kosong.
                        ->toolbarButtons([
                            // Menentukan tombol-tombol toolbar yang tersedia di editor Markdown.
                            'bold',
                            'italic',
                            'strike',
                            'bulletList',
                            'orderedList',
                            'link',
                        ]),
                ])
                ->columns(2) // Mengatur layout kolom untuk konten dalam bagian ini.
                ->collapsible(), // Memungkinkan bagian ini untuk dilipat/dibuka.

            // Bagian ketiga form: Visual & Media.
            // Digunakan untuk mengunggah gambar item menu.
            Section::make('Visual & Media')
                ->description('Upload an image to showcase this menu item')
                ->icon('heroicon-m-camera')
                ->schema([
                    // Field FileUpload untuk gambar menu.
                    FileUpload::make('image')
                        ->label('Menu Image')
                        ->image() // Mengkhususkan untuk gambar.
                        ->directory('menu-items') // Direktori penyimpanan gambar.
                        ->imageEditor() // Mengaktifkan editor gambar.
                        ->imageEditorAspectRatios([
                            // Rasio aspek yang diizinkan untuk editor gambar.
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->maxSize(5120) // Ukuran maksimum file (5MB).
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']) // Tipe file yang diterima.
                        ->helperText('Optional: Upload an appetizing image of this menu item (Max: 5MB)')
                        ->imagePreviewHeight('250') // Tinggi preview gambar.
                        ->loadingIndicatorPosition('left')
                        ->panelAspectRatio('16:9')
                        ->panelLayout('integrated')
                        ->removeUploadedFileButtonPosition('right')
                        ->uploadButtonPosition('left')
                        ->uploadProgressIndicatorPosition('left')
                        ->nullable() // Boleh kosong.
                        ->columnSpanFull(),
                ])
                ->collapsible() // Memungkinkan bagian ini untuk dilipat/dibuka.
                ->collapsed(fn($record) => $record === null ? false : empty($record->image)),
            // Secara default, bagian ini akan dilipat jika record sudah ada dan tidak memiliki gambar.
            // Saat membuat record baru ($record === null), bagian ini akan terbuka.

            // Bagian keempat form: Varian Menu.
            // Digunakan untuk menambahkan opsi atau varian untuk item menu.
            Section::make('Menu Variants')
                ->description('Add different variants or options for this menu item')
                ->icon('heroicon-m-squares-plus')
                ->schema([
                    // Field Repeater untuk mengelola daftar varian.
                    Repeater::make('variants')
                        ->relationship() // Menghubungkan ke relasi 'variants' pada model Menu.
                        ->label('') // Label kosong karena label per item akan diambil dari 'itemLabel'.
                        ->schema([
                            // Field TextInput untuk Nama Varian di dalam repeater.
                            TextInput::make('name')
                                ->label('Variant Name')
                                ->placeholder('e.g., Large, Medium, Spicy, Extra Sauce...')
                                ->helperText('Enter the name of this variant')
                                ->nullable()
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ])
                        // Menentukan label untuk setiap item dalam repeater.
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Menu Variant')
                        ->addActionLabel('Add New Variant') // Label tombol tambah varian.
                        ->reorderableWithButtons() // Memungkinkan pengaturan ulang urutan item dengan tombol.
                        ->collapsible() // Memungkinkan setiap item dalam repeater dilipat.
                        ->cloneable() // Memungkinkan duplikasi item.
                        ->deleteAction(
                            // Mengatur aksi hapus untuk setiap item repeater agar memerlukan konfirmasi.
                            fn($action) => $action->requiresConfirmation()
                        )
                        ->defaultItems(0) // Jumlah item default saat form dibuka.
                        ->minItems(0) // Jumlah minimum item.
                        ->maxItems(20) // Jumlah maksimum item.
                        ->grid(1) // Menata item repeater dalam 1 kolom.
                        ->columnSpanFull(),
                ])
                ->collapsible() // Memungkinkan bagian ini untuk dilipat/dibuka.
                ->collapsed(fn($record) => $record === null ? true : $record->variants->isEmpty()),
            // Secara default, bagian ini akan dilipat jika record baru (belum ada varian)
            // atau record sudah ada tetapi tidak memiliki varian.
        ]);
    }

    /**
     * Mendefinisikan kolom-kolom untuk tabel daftar menu.
     * Fungsi ini mengatur bagaimana item menu akan ditampilkan dalam daftar di panel admin.
     *
     * @param Table $table Objek Table yang akan dikonfigurasi.
     * @return Table Objek Table yang sudah dikonfigurasi.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom untuk menampilkan gambar menu.
                ImageColumn::make('image')
                    ->label('Image')
                    ->circular() // Bentuk gambar lingkaran.
                    ->size(50) // Ukuran gambar.
                    ->defaultImageUrl(url('/images/placeholder-food.png')) // Gambar placeholder jika tidak ada gambar.
                    ->tooltip('Menu image'), // Tooltip saat hover.

                // Kolom untuk Nama Menu.
                TextColumn::make('name')
                    ->label('Menu Name')
                    ->searchable() // Memungkinkan pencarian berdasarkan nama.
                    ->sortable() // Memungkinkan pengurutan berdasarkan nama.
                    ->weight(FontWeight::SemiBold) // Berat font semi-bold.
                    ->color('primary') // Warna teks.
                    ->icon('heroicon-m-clipboard-document-list') // Ikon di samping teks.
                    ->copyable() // Memungkinkan menyalin teks ke clipboard.
                    ->copyMessage('Menu name copied!') // Pesan saat disalin.
                    ->copyMessageDuration(1500) // Durasi pesan copy (ms).
                    ->limit(30) // Membatasi panjang teks yang ditampilkan.
                    ->tooltip(function (TextColumn $column): ?string {
                        // Menampilkan tooltip dengan nama lengkap jika teks terpotong.
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                // Kolom untuk Nama Kategori.
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge() // Menampilkan sebagai badge.
                    ->color('info') // Warna badge.
                    ->icon('heroicon-m-tag') // Ikon di samping badge.
                    ->placeholder('No category') // Placeholder jika tidak ada kategori.
                    ->limit(20),

                // Kolom untuk Nama Subkategori.
                TextColumn::make('subcategory.name')
                    ->label('Subcategory')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-folder-open')
                    ->placeholder('No subcategory')
                    ->toggleable() // Memungkinkan kolom ini disembunyikan/ditampilkan.
                    ->limit(20),

                // Kolom untuk Harga.
                TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR') // Memformat sebagai mata uang IDR.
                    ->sortable()
                    ->color('warning')
                    ->icon('heroicon-m-currency-dollar')
                    ->placeholder('Not set')
                    ->tooltip('Click to sort by price'),

                // Kolom Badge untuk menampilkan jumlah varian.
                BadgeColumn::make('variants_count')
                    ->label('Variants')
                    ->counts('variants') // Menghitung jumlah relasi 'variants'.
                    ->color(static function ($state): string {
                        // Logika warna badge berdasarkan jumlah varian.
                        if ($state === 0) {
                            return 'gray';
                        }
                        if ($state <= 3) {
                            return 'info';
                        }
                        return 'success';
                    })
                    ->icon(static function ($state): string {
                        // Logika ikon badge berdasarkan jumlah varian.
                        if ($state === 0) {
                            return 'heroicon-m-squares-2x2';
                        }
                        return 'heroicon-m-squares-plus';
                    }),

                // Kolom Icon untuk menunjukkan apakah ada deskripsi.
                IconColumn::make('has_description')
                    ->label('Description')
                    ->getStateUsing(fn($record) => !empty($record->description)) // Mengambil state true/false berdasarkan keberadaan deskripsi.
                    ->boolean() // Menampilkan sebagai ikon boolean (check/cross).
                    ->trueIcon('heroicon-o-document-text') // Ikon jika true.
                    ->falseIcon('heroicon-o-x-mark') // Ikon jika false.
                    ->trueColor('success') // Warna ikon jika true.
                    ->falseColor('gray') // Warna ikon jika false.
                    ->tooltip(function ($record) {
                        // Tooltip yang berbeda berdasarkan keberadaan deskripsi.
                        return $record->description ? 'Has description' : 'No description';
                    }),

                // Kolom Badge untuk menampilkan status kelengkapan data menu.
                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        // Logika untuk menentukan status kelengkapan data.
                        if ($record->deleted_at) {
                            return 'deleted'; // Jika record dihapus sementara (soft deleted).
                        }

                        $score = 0; // Skor kelengkapan data.
                        if ($record->name) $score++;
                        if ($record->price) $score++;
                        if ($record->description) $score++;
                        if ($record->image) $score++;
                        if ($record->menu_category_id) $score++;

                        // Menentukan status berdasarkan skor.
                        return match ($score) {
                            5 => 'complete',
                            4 => 'good',
                            3 => 'fair',
                            2 => 'basic',
                            default => 'incomplete',
                        };
                    })
                    ->color(static function ($state): string {
                        // Mengatur warna badge berdasarkan status.
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
                        // Memformat teks yang ditampilkan di badge.
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
                        // Mengatur ikon badge berdasarkan status.
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

                // Kolom untuk tanggal pembuatan.
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y') // Format tanggal dan waktu.
                    ->sortable()
                    ->toggleable() // Memungkinkan disembunyikan/ditampilkan.
                    ->color('gray')
                    ->icon('heroicon-m-calendar'),

                // Kolom untuk tanggal terakhir diupdate.
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true) // Tersembunyi secara default.
                    ->color('gray')
                    ->since() // Menampilkan waktu relatif (misal "2 days ago").
                    ->icon('heroicon-m-clock'),
            ])
            ->filters([
                // Filter untuk menampilkan record yang dihapus sementara.
                Tables\Filters\TrashedFilter::make()
                    ->label('Show Deleted Records')
                    ->placeholder('All Records')
                    ->trueLabel('Only Deleted')
                    ->falseLabel('Without Deleted'),

                // Filter Select untuk kategori menu.
                SelectFilter::make('menu_category_id')
                    ->label('Filter by Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple() // Memungkinkan pemilihan banyak kategori.
                    ->placeholder('All Categories'),

                // Filter Select untuk subkategori menu.
                SelectFilter::make('menu_subcategory_id')
                    ->label('Filter by Subcategory')
                    ->relationship('subcategory', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('All Subcategories'),

                // Filter kustom untuk item yang memiliki harga.
                Tables\Filters\Filter::make('has_price')
                    ->label('Has Price')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('price')) // Query untuk filter.
                    ->toggle(), // Menampilkan sebagai toggle switch.

                // Filter kustom untuk item yang memiliki gambar.
                Tables\Filters\Filter::make('has_image')
                    ->label('Has Image')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('image'))
                    ->toggle(),

                // Filter kustom untuk item yang memiliki varian.
                Tables\Filters\Filter::make('has_variants')
                    ->label('Has Variants')
                    ->query(fn(Builder $query): Builder => $query->has('variants')) // Menggunakan relasi 'has'.
                    ->toggle(),

                // Filter kustom untuk item yang datanya lengkap.
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

                // Filter kustom untuk rentang harga.
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        // Form di dalam filter untuk input min/max harga.
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
                        // Logika query berdasarkan input rentang harga.
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
                        // Menampilkan indikator filter aktif.
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
                // Aksi untuk melihat detail item menu.
                Tables\Actions\ViewAction::make()
                    ->color('info')
                    ->modalHeading(fn($record) => "View Menu: {$record->name}") // Judul modal.
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
                                    <h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Category</h3>
                                    <p class='text-gray-600 dark:text-gray-300'>" . ($record->category?->name ?? 'No category') . "</p>
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

                // Aksi untuk mengedit item menu.
                Tables\Actions\EditAction::make()
                    ->color('warning'),

                // Aksi untuk menghapus sementara (soft delete) item menu.
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation() // Membutuhkan konfirmasi.
                    ->modalHeading('Delete Menu Item')
                    ->modalDescription(fn($record) => "Are you sure you want to delete '{$record->name}'? This action can be undone.")
                    ->modalSubmitActionLabel('Yes, delete it'),

                // Aksi untuk menghapus permanen item menu.
                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Permanently Delete Menu Item')
                    ->modalDescription(fn($record) => "Are you sure you want to permanently delete '{$record->name}'? This action cannot be undone.")
                    ->modalSubmitActionLabel('Yes, delete permanently'),

                // Aksi untuk mengembalikan item menu yang dihapus sementara.
                Tables\Actions\RestoreAction::make()
                    ->color('success'),
            ])
            ->bulkActions([
                // Grup aksi massal (bulk actions) untuk beberapa item yang dipilih.
                Tables\Actions\BulkActionGroup::make([
                    // Aksi hapus massal (soft delete).
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected menu items')
                        ->modalDescription('Are you sure you want to delete the selected menu items? This action can be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),

                    // Aksi hapus permanen massal.
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Permanently delete selected menu items')
                        ->modalDescription('Are you sure you want to permanently delete the selected menu items? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),

                    // Aksi pulihkan massal.
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Aksi yang ditampilkan saat tabel kosong.
                Tables\Actions\CreateAction::make()
                    ->label('Create your first menu item')
                    ->icon('heroicon-m-plus'),
            ])
            ->emptyStateHeading('No menu items yet') // Judul saat tabel kosong.
            ->emptyStateDescription('Once you create your first menu item, it will appear here. Start building your delicious menu!') // Deskripsi saat tabel kosong.
            ->emptyStateIcon('heroicon-o-clipboard-document-list') // Ikon saat tabel kosong.
            ->striped() // Membuat baris tabel bergaris (striped).
            ->defaultSort('created_at', 'desc') // Pengurutan default tabel.
            ->recordTitleAttribute('name') // Atribut yang digunakan sebagai judul record di beberapa UI Filament.
            ->modifyQueryUsing(
                // Memodifikasi query dasar untuk tabel.
                // Dalam kasus ini, memastikan bahwa item yang dihapus sementara juga disertakan
                // agar filter 'TrashedFilter' dapat berfungsi.
                fn(Builder $query) =>
                $query->withoutGlobalScopes([SoftDeletingScope::class])
            );
    }

    /**
     * Mengembalikan array relasi yang terkait dengan resource ini.
     * Saat ini kosong, tetapi dapat digunakan untuk menambahkan relasi manager.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Mengembalikan array definisi halaman untuk resource ini.
     * Ini menentukan rute URL untuk daftar, membuat, melihat, dan mengedit item menu.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'), // Halaman daftar menu.
            'create' => Pages\CreateMenu::route('/create'), // Halaman membuat menu baru.
            'view' => Pages\ViewMenu::route('/{record}'), // Halaman melihat detail menu.
            'edit' => Pages\EditMenu::route('/{record}/edit'), // Halaman mengedit menu.
        ];
    }

    /**
     * Mengembalikan query Eloquent dasar untuk resource ini.
     * Ini digunakan secara internal oleh Filament untuk mengambil data.
     * Di sini, scope SoftDeletingScope dinonaktifkan untuk memastikan data yang soft-deleted juga bisa diakses.
     * Ini penting agar fungsi seperti 'RestoreAction' dapat bekerja.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Mengembalikan jumlah total record dari model terkait.
     * Digunakan untuk menampilkan badge jumlah di navigasi sidebar.
     *
     * @return string|null Jumlah record sebagai string, atau null jika tidak ingin menampilkan badge.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * Mengembalikan warna badge navigasi berdasarkan jumlah record.
     * Memberikan indikasi visual tentang volume data.
     *
     * @return string|null Nama warna (misal 'success', 'warning', 'primary').
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();

        if ($count > 50) {
            return 'success'; // Lebih dari 50 item, warna hijau.
        }

        if ($count > 20) {
            return 'warning'; // Lebih dari 20 item, warna kuning.
        }

        return 'primary'; // Kurang dari atau sama dengan 20 item, warna biru standar.
    }

    /**
     * Mengembalikan query Eloquent yang digunakan untuk pencarian global.
     * Memuat relasi 'category', 'subcategory', dan 'variants' agar data mereka bisa dicari.
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category', 'subcategory', 'variants']);
    }

    /**
     * Mengembalikan array atribut yang dapat dicari secara global.
     * Ini menentukan kolom mana yang akan dipertimbangkan saat pengguna melakukan pencarian global
     * di panel admin Filament.
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description', 'category.name', 'subcategory.name'];
    }

    /**
     * Mengembalikan detail tambahan untuk hasil pencarian global.
     * Detail ini ditampilkan di bawah judul hasil pencarian untuk memberikan konteks lebih lanjut.
     *
     * @param mixed $record Record yang ditemukan dari pencarian.
     * @return array Array asosiatif detail.
     */
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
