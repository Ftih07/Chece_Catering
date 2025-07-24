<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class Handler
 *
 * Kelas ini bertanggung jawab untuk menangani (handle) semua exception (kesalahan)
 * yang terjadi di aplikasi Laravel. Ini adalah pusat penanganan error global.
 *
 * Extends Illuminate\Foundation\Exceptions\Handler untuk mendapatkan fungsionalitas
 * dasar penanganan exception dari Laravel.
 */
class Handler extends ExceptionHandler
{
    /**
     * $dontReport
     *
     * Daftar tipe exception yang TIDAK akan dilaporkan (logged).
     * Exception yang ada di daftar ini akan tetap memicu respons error,
     * tetapi tidak akan tercatat di log aplikasi (misalnya, di file storage/logs).
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        // Contoh: \App\Exceptions\CustomIgnoredException::class,
    ];

    /**
     * $dontFlash
     *
     * Daftar input HTTP request yang TIDAK akan disimpan (flash) ke sesi
     * ketika terjadi exception validasi. Ini penting untuk keamanan
     * agar data sensitif seperti password tidak muncul kembali di form
     * setelah terjadi error validasi.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * register()
     *
     * Metode ini digunakan untuk mendaftarkan callback atau closure kustom
     * untuk penanganan exception. Anda bisa mendefinisikan logika penanganan
     * error spesifik di sini.
     *
     * @return void
     */
    public function register(): void
    {
        // Contoh:
        // $this->reportable(function (Throwable $e) {
        //     // Kirim notifikasi ke Slack atau email ketika terjadi error tertentu
        // });
    }

    /**
     * render()
     *
     * Metode ini bertanggung jawab untuk mengubah exception menjadi HTTP response
     * yang akan dikirimkan kembali ke browser pengguna.
     *
     * Di sini, kita mengimplementasikan logika kustom untuk menampilkan halaman
     * error yang lebih "cantik" sesuai dengan kode status HTTP.
     *
     * @param  \Illuminate\Http\Request  $request Objek request HTTP yang memicu exception.
     * @param  \Throwable  $exception Objek exception yang terjadi.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Tangani error HTTP seperti 403 (Unauthorized), 404 (Not Found), 503 (Service Unavailable), dll.
        // Mengecek apakah exception yang terjadi adalah instance dari HttpExceptionInterface.
        if ($exception instanceof HttpExceptionInterface) {
            // Mengambil kode status HTTP dari exception (misal: 403, 404, 500, 503).
            $statusCode = $exception->getStatusCode();

            // Mengecek apakah kode status ada di dalam array yang ingin kita tangani secara kustom.
            // Di sini, kita secara eksplisit menangani 403, 404, 500, dan 503.
            if (in_array($statusCode, [403, 404, 500, 503])) {
                // Mengecek apakah ada view (file blade) kustom untuk error dengan kode status tersebut.
                // Contoh: Apakah ada file `resources/views/errors/404.blade.php`?
                if (view()->exists("errors.$statusCode")) {
                    // Jika view ada, tampilkan view error kustom tersebut dengan kode status yang sesuai.
                    return response()->view("errors.$statusCode", [], $statusCode);
                }
            }
        }

        // Tangani error selain itu (exception tak terduga yang bukan HttpException atau yang tidak ditangani di atas).
        // Semua error yang tidak ditangani secara spesifik di atas akan diarahkan ke halaman error 500.
        // Ini memastikan bahwa pengguna selalu melihat halaman error yang konsisten dan informatif.
        return response()->view("errors.500", [], 500);

        // Opsi lain: Jika ingin kembali ke perilaku default Laravel untuk semua error yang tidak ditangani kustom.
        // Ini akan menggunakan template error bawaan Laravel atau Ignition (jika terinstal di lingkungan dev).
        // return parent::render($request, $exception);
    }
}
