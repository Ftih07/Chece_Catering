<?php

namespace App\Http\Middleware;

use Closure;                // Mengimpor Closure untuk tipe parameter callback.
use Illuminate\Http\Request; // Mengimpor kelas Request untuk mendapatkan detail permintaan HTTP.
use Symfony\Component\HttpFoundation\Response; // Mengimpor kelas Response untuk tipe return.
use App\Models\Visit;      // Mengimpor model Visit untuk menyimpan data kunjungan ke database.

/**
 * Class TrackVisitor
 *
 * Middleware ini bertanggung jawab untuk melacak setiap kunjungan ke website.
 * Setiap kali permintaan HTTP melewati middleware ini, ia akan mencatat
 * informasi tentang pengunjung ke dalam tabel 'visits' di database.
 */
class TrackVisitor
{
    /**
     * handle()
     *
     * Metode ini adalah inti dari middleware. Ia dieksekusi untuk setiap permintaan
     * yang melewati middleware ini.
     *
     * @param  \Illuminate\Http\Request  $request Objek permintaan HTTP saat ini.
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next Closure yang mewakili permintaan selanjutnya dalam antrean middleware.
     * @return \Symfony\Component\HttpFoundation\Response Mengembalikan respons dari permintaan selanjutnya.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Membuat record baru di tabel 'visits' menggunakan model Visit.
        Visit::create([
            'ip_address'  => $request->ip(),          // Mencatat alamat IP pengunjung.
            'user_agent'  => $request->userAgent(),   // Mencatat string User-Agent browser pengunjung.
            'url'         => $request->fullUrl(),    // Mencatat URL lengkap yang diminta oleh pengunjung.
            'referrer'    => $request->headers->get('referer'), // Mencatat URL dari mana pengunjung datang (jika ada).
            'path'        => $request->path(),        // Mencatat jalur URL relatif (tanpa domain).
        ]);

        // Meneruskan permintaan ke middleware atau controller selanjutnya dalam antrean.
        // Ini memastikan bahwa alur aplikasi berlanjut seperti biasa setelah data kunjungan dicatat.
        return $next($request);
    }
}
