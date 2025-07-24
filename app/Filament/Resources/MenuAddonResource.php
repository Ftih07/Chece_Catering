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
use Filament\Forms\Components\Textarea; // Meskipun diimpor, Textarea tidak digunakan di kode yang disediakan.
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString; // Digunakan untuk menampilkan HTML mentah di modal view.

/**
 * Kelas MenuAddonResource
 *
 * Mengatur tampilan dan fungsionalitas CRUD (Create, Read, Update, Delete)
 * untuk model MenuAddon (item tambahan/opsional pada menu) di dalam Filament Admin Panel.
 */
class MenuAddonResource extends Resource
{
    /**
     * Menentukan model Eloquent yang terkait dengan resource ini.
     * Dalam kasus ini, modelnya adalah App\Models\MenuAddon.
     *
     * @var string|null
     */
    protected static ?string $model = MenuAddon::class;

    /**
     * Icon navigasi yang akan ditampilkan di sidebar Filament.
     * Menggunakan icon 'heroicon-o-plus-circle'.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    /**
     * Label navigasi yang akan ditampilkan di sidebar Filament.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Menu Add-ons';

    /**
     * Label untuk satu instance model (singular).
     *
     * @var string|null
     */
    protected static ?string $modelLabel = 'Menu Add-on';

    /**
     * Label untuk beberapa instance model (plural).
     *
     * @var string|null
     */
    protected static ?string $pluralModelLabel = 'Menu Add-ons';

    /**
     * Grup navigasi tempat resource ini akan ditempatkan di sidebar.
     * Membantu dalam mengorganisir navigasi admin.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Menu Management';

    /**
     * Urutan navigasi di dalam grupnya. Angka yang lebih kecil akan tampil lebih dulu.
     * Ini diatur sebagai yang kedua di grup 'Menu Management'.
     *
     * @var int|null
     */
    protected static ?int $navigationSort = 2;

    /**
     * Mendefinisikan skema formulir untuk membuat atau mengedit add-on menu.
     *
     * @param Form $form Objek Form yang akan dikonfigurasi.
     * @return Form Skema formulir yang sudah dikonfigurasi.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            // Bagian untuk informasi dasar add-on.
            Section::make('Add-on Information')
                ->description('Configure the menu add-on details and description') // Deskripsi singkat untuk bagian ini.
                ->icon('heroicon-m-plus-circle') // Icon untuk bagian ini.
                ->schema([
                    Grid::make(1) // Grid dengan 1 kolom.
                        ->schema([
                            // Bidang input teks untuk judul add-on.
                            TextInput::make('title')
                                ->label('Add-on Title') // Label yang ditampilkan.
                                ->required() // Wajib diisi.
                                ->maxLength(255) // Batas karakter maksimum.
                                ->placeholder('e.g., Extra Cheese, Bacon, Large Size...') // Placeholder teks.
                                ->helperText('Enter a clear and descriptive title for this add-on') // Teks bantuan.
                                ->live(onBlur: true) // Memperbarui bidang lain secara live saat input ini kehilangan fokus.
                                // Callback setelah state diperbarui untuk secara otomatis membuat slug dari judul.
                                ->afterStateUpdated(function (string $context, $state, callable $set) {
                                    if ($context === 'create') {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                }),
                        ]),
                ])
                ->columns(1) // Bagian ini menggunakan 1 kolom.
                ->collapsible(), // Memungkinkan bagian untuk diciutkan/dibentangkan.

            // Bagian untuk deskripsi dan detail add-on.
            Section::make('Description & Details')
                ->description('Provide detailed information about this add-on') // Deskripsi singkat.
                ->icon('heroicon-m-document-text') // Icon untuk bagian ini.
                ->schema([
                    // Rich editor untuk deskripsi add-on, memungkinkan format teks kaya.
                    RichEditor::make('description')
                        ->label('Description') // Label yang ditampilkan.
                        ->placeholder('Describe this add-on, its ingredients, or any special notes...')
                        ->helperText('Optional: Add detailed description, ingredients, or special instructions')
                        // Tombol toolbar yang tersedia di rich editor.
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
                        ->columnSpanFull() // Mengambil seluruh lebar kolom yang tersedia.
                        ->nullable(), // Bidang ini boleh kosong.
                ])
                ->collapsible() // Bagian ini dapat diciutkan/dibentangkan.
                // Kondisi untuk awalannya ciut (collapsed) atau bentang (expanded).
                // Jika record baru (null) atau deskripsi kosong, maka dibentangkan.
                ->collapsed(fn($record) => $record === null ? false : empty($record->description)),
        ]);
    }

    /**
     * Mendefinisikan skema tabel untuk menampilkan daftar add-on menu.
     *
     * @param Table $table Objek Table yang akan dikonfigurasi.
     * @return Table Skema tabel yang sudah dikonfigurasi.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom teks untuk judul add-on.
                TextColumn::make('title')
                    ->label('Add-on Title') // Label kolom.
                    ->searchable() // Memungkinkan pencarian.
                    ->sortable() // Memungkinkan pengurutan.
                    ->weight(FontWeight::SemiBold) // Ketebalan font.
                    ->color('primary') // Warna teks.
                    ->icon('heroicon-m-plus-circle') // Icon di samping teks.
                    ->copyable() // Memungkinkan menyalin teks kolom.
                    ->copyMessage('Title copied!') // Pesan saat disalin.
                    ->copyMessageDuration(1500), // Durasi pesan dalam ms.

                // Kolom teks untuk deskripsi add-on.
                TextColumn::make('description')
                    ->label('Description') // Label kolom.
                    ->html() // Menampilkan konten sebagai HTML (penting untuk RichEditor).
                    ->limit(60) // Batasi teks yang ditampilkan.
                    // Menambahkan tooltip yang menampilkan deskripsi lengkap (tanpa HTML tag) jika terpotong.
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen(strip_tags($state)) <= 60) {
                            return null; // Tidak perlu tooltip jika teks tidak terpotong.
                        }
                        return strip_tags($state); // Tampilkan teks lengkap tanpa HTML di tooltip.
                    })
                    ->placeholder('No description') // Placeholder jika deskripsi kosong.
                    ->color('gray') // Warna teks.
                    ->wrap(), // Memungkinkan teks untuk wrap (pindah baris).

                // Kolom badge untuk menampilkan status add-on.
                BadgeColumn::make('status')
                    ->label('Status') // Label kolom.
                    // Mengambil state status berdasarkan kondisi record.
                    ->getStateUsing(function ($record) {
                        if ($record->deleted_at) {
                            return 'deleted'; // Jika record di-soft-delete.
                        }
                        return empty($record->description) ? 'basic' : 'detailed'; // Jika deskripsi kosong/ada.
                    })
                    // Mengatur warna badge berdasarkan state status.
                    ->color(static function ($state): string {
                        return match ($state) {
                            'deleted' => 'danger',
                            'detailed' => 'success',
                            'basic' => 'warning',
                            default => 'gray',
                        };
                    })
                    // Mengatur format tampilan teks badge berdasarkan state status.
                    ->formatStateUsing(static function ($state): string {
                        return match ($state) {
                            'deleted' => 'Deleted',
                            'detailed' => 'Complete',
                            'basic' => 'Basic',
                            default => 'Unknown',
                        };
                    })
                    // Mengatur icon badge berdasarkan state status.
                    ->icon(static function ($state): string {
                        return match ($state) {
                            'deleted' => 'heroicon-m-x-circle',
                            'detailed' => 'heroicon-m-check-circle',
                            'basic' => 'heroicon-m-exclamation-triangle',
                            default => 'heroicon-m-question-mark-circle',
                        };
                    }),

                // Kolom untuk tanggal pembuatan.
                TextColumn::make('created_at')
                    ->label('Created') // Label kolom.
                    ->dateTime('M j, Y g:i A') // Format tampilan tanggal dan waktu.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable() // Dapat disembunyikan/ditampilkan.
                    ->color('gray') // Warna teks.
                    ->icon('heroicon-m-calendar'), // Icon.

                // Kolom untuk tanggal terakhir diperbarui.
                TextColumn::make('updated_at')
                    ->label('Last Updated') // Label kolom.
                    ->dateTime('M j, Y g:i A') // Format tampilan tanggal dan waktu.
                    ->sortable() // Memungkinkan pengurutan.
                    ->toggleable(isToggledHiddenByDefault: true) // Tersembunyi secara default.
                    ->color('gray') // Warna teks.
                    ->since() // Menampilkan "sejak kapan" (e.g., "3 hours ago").
                    ->icon('heroicon-m-clock'), // Icon.

                // Kolom untuk tanggal penghapusan (jika soft delete diaktifkan).
                TextColumn::make('deleted_at')
                    ->label('Deleted') // Label kolom.
                    ->dateTime('M j, Y g:i A') // Format tampilan tanggal dan waktu.
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

                // Filter untuk add-on yang memiliki deskripsi.
                Tables\Filters\Filter::make('has_description')
                    ->label('Has Description')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('description')->where('description', '!=', '')) // Query: tidak null dan tidak kosong.
                    ->toggle(), // Tampilkan sebagai toggle.

                // Filter untuk add-on yang tidak memiliki deskripsi.
                Tables\Filters\Filter::make('no_description')
                    ->label('No Description')
                    ->query(fn(Builder $query): Builder => $query->whereNull('description')->orWhere('description', '')) // Query: null atau kosong.
                    ->toggle(), // Tampilkan sebagai toggle.

                // Filter untuk add-on yang dibuat dalam 7 hari terakhir.
                Tables\Filters\Filter::make('recent')
                    ->label('Recent (Last 7 days)')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),
            ])
            ->actions([
                // Aksi untuk melihat detail record.
                Tables\Actions\ViewAction::make()
                    ->color('info') // Warna tombol.
                    // Menyesuaikan judul modal view.
                    ->modalHeading(fn($record) => "View Add-on: {$record->title}")
                    // Menyesuaikan konten modal view dengan HTML kustom.
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

                // Aksi untuk mengedit record.
                Tables\Actions\EditAction::make()
                    ->color('warning'), // Warna tombol.

                // Aksi untuk menghapus record (soft delete).
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation() // Meminta konfirmasi sebelum menghapus.
                    ->modalHeading('Delete Add-on')
                    ->modalDescription(fn($record) => "Are you sure you want to delete '{$record->title}'? This action can be undone.") // Pesan konfirmasi dinamis.
                    ->modalSubmitActionLabel('Yes, delete it'),

                // Aksi untuk menghapus record secara permanen (force delete).
                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation() // Meminta konfirmasi sebelum menghapus permanen.
                    ->modalHeading('Permanently Delete Add-on')
                    ->modalDescription(fn($record) => "Are you sure you want to permanently delete '{$record->title}'? This action cannot be undone.") // Pesan konfirmasi dinamis.
                    ->modalSubmitActionLabel('Yes, delete permanently'),

                // Aksi untuk mengembalikan record yang sudah dihapus.
                Tables\Actions\RestoreAction::make()
                    ->color('success'), // Warna tombol.

                // Aksi untuk mereplikasi (menggandakan) record.
                Tables\Actions\ReplicateAction::make()
                    ->color('gray')
                    // Callback sebelum replika disimpan untuk mengubah judul replika.
                    ->beforeReplicaSaved(function ($replica) {
                        $replica->title = $replica->title . ' (Copy)'; // Tambahkan "(Copy)" ke judul.
                    }),
            ])
            ->bulkActions([
                // Grup aksi massal (bulk actions) untuk record yang dipilih.
                Tables\Actions\BulkActionGroup::make([
                    // Aksi hapus massal (soft delete).
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation() // Meminta konfirmasi.
                        ->modalHeading('Delete selected add-ons')
                        ->modalDescription('Are you sure you want to delete the selected add-ons? This action can be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),

                    // Aksi hapus permanen massal.
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->requiresConfirmation() // Meminta konfirmasi.
                        ->modalHeading('Permanently delete selected add-ons')
                        ->modalDescription('Are you sure you want to permanently delete the selected add-ons? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),

                    // Aksi kembalikan massal.
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Aksi yang ditampilkan saat tabel kosong.
                Tables\Actions\CreateAction::make()
                    ->label('Create your first add-on')
                    ->icon('heroicon-m-plus'),
            ])
            ->emptyStateHeading('No menu add-ons yet') // Judul saat tabel kosong.
            ->emptyStateDescription('Once you create your first menu add-on, it will appear here. Add-ons help customers customize their orders.') // Deskripsi saat tabel kosong.
            ->emptyStateIcon('heroicon-o-plus-circle') // Icon saat tabel kosong.
            ->striped() // Menambahkan striping pada baris tabel.
            ->defaultSort('created_at', 'desc') // Mengatur pengurutan default tabel berdasarkan tanggal pembuatan (terbaru duluan).
            ->recordTitleAttribute('title') // Menentukan atribut yang digunakan sebagai judul record di beberapa UI Filament.
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
            'index' => Pages\ListMenuAddons::route('/'), // Halaman daftar add-on menu.
            'create' => Pages\CreateMenuAddon::route('/create'), // Halaman pembuatan add-on menu baru.
            'view' => Pages\ViewMenuAddon::route('/{record}'), // Halaman melihat detail add-on menu.
            'edit' => Pages\EditMenuAddon::route('/{record}/edit'), // Halaman mengedit add-on menu.
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
     * Menampilkan jumlah total add-on menu.
     *
     * @return string|null Jumlah add-on menu.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * Mendapatkan warna badge navigasi berdasarkan jumlah add-on menu.
     * Warna akan berubah tergantung pada kuantitas record.
     *
     * @return string|null Warna badge (e.g., 'primary', 'success', 'warning').
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();

        if ($count > 20) {
            return 'success'; // Lebih dari 20 add-on: hijau.
        }

        if ($count > 10) {
            return 'warning'; // Lebih dari 10 add-on: kuning.
        }

        return 'primary'; // 10 add-on atau kurang: biru.
    }

    /**
     * Mengatur query Eloquent untuk pencarian global.
     * Saat ini tidak memuat relasi apapun.
     *
     * @return Builder
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['']); // Array kosong menunjukkan tidak ada eager loading relasi.
    }

    /**
     * Mendapatkan atribut yang dapat dicari secara global.
     *
     * @return array Daftar atribut yang dapat dicari.
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description']; // Pencarian global akan dilakukan pada kolom 'title' dan 'description'.
    }

    /**
     * Mendapatkan detail hasil pencarian global yang akan ditampilkan.
     *
     * @param mixed $record Record yang ditemukan.
     * @return array Detail yang ditampilkan.
     */
    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Description' => $record->description ? strip_tags($record->description) : 'No description', // Menampilkan deskripsi tanpa tag HTML.
        ];
    }
}
