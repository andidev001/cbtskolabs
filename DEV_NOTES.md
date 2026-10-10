# Catatan Pengembangan (Dev Notes) - DIGTI CBT

Catatan ini dibuat untuk merekam progres terakhir sebelum jeda pengembangan, agar saat dilanjutkan kembali tidak kehilangan konteks dan mencegah terhapusnya fitur-fitur yang sudah berjalan stabil.

## 🚀 Fitur Terakhir yang Berhasil Diimplementasikan

1. **Role Pengawas (Baru)**
   - **Autentikasi & Akun**: Sudah memiliki *guard* sendiri (`auth:pengawas`), tabel di *database* (`pengawas`), dan seeder (`PengawasSeeder.php`).
   - **Dashboard**: Tampilan khusus untuk Pengawas dengan 4 *card* informatif, desain sudah dirapikan dengan garis batas tipis.
   - **Fitur Pengawas**: 
     - **Status Ujian**: Pengawas bisa melihat status ujian dan merilis/reset token untuk peserta.
     - **Monitoring**: Pengawas bisa memantau siswa, melakukan *force finish*, *unblock*, dan *reset login* peserta.
     - **Berita Acara**: Pengawas bisa mengisi dan mencetak Berita Acara.

2. **Pembaruan Fitur Berita Acara (Semua Role)**
   - Berita Acara sekarang difilter dan dicetak **per-kelas/grup** (tidak lagi digabung seluruh peserta dalam jadwal).
   - Format PDF sudah menggunakan **Bahasa Indonesia** untuk penamaan Hari dan Bulan (menggunakan konfigurasi lokalisasi otomatis pada fungsi Carbon).
   - *Field* `group_id` sudah ditambahkan ke tabel `berita_acaras` melalui *migration*.

3. **Perbaikan Bug (Bugfixes)**
   - **Routing Conflict**: Sebelumnya terjadi tabrakan rute (URL) di `routes/web.php` di mana rute Pengawas "bocor" masuk ke grup rute Admin dan Guru. Hal ini menyebabkan error `Attempt to read property "photo" on null`. **Status saat ini sudah BERSIH dan DIPERBAIKI**.
   - **Idempotency Seeder**: `PengawasSeeder` sudah dimodifikasi menggunakan `firstOrCreate` sehingga aman jika dijalankan berkali-kali di *hosting*.

---

## 🛠️ Status Sistem Saat Ini
- **Kondisi Routing**: Lulus tes (tidak ada error `php artisan route:list`).
- **Kondisi Views**: Lulus tes kompilasi (tidak ada *syntax error* pada semua file `.blade.php`).
- **Kondisi Repositori**: Repositori GitHub sudah bersih dari *file* temporer (*scratch scripts*) maupun *file* *session* sistem.

---

## ⚠️ Hal-hal yang Perlu Diperhatikan Saat Melanjutkan Pengembangan

1. **Database di Hosting / Production**: 
   - Jangan lupa jalankan `php artisan migrate` di *hosting* jika belum.
   - Jalankan `php artisan db:seed --class=PengawasSeeder` untuk memunculkan akun pengawas bawaan di *hosting*.
2. **Penambahan Fitur Baru pada Route**: 
   - Saat menambahkan menu baru di `routes/web.php`, pastikan menambahkannya di dalam **grup prefix/middleware yang tepat** (`admin`, `guru`, `pengawas`, `siswa`).
   - Jangan gunakan *find-and-replace* massal (seperti regex) untuk mengubah rute tanpa memeriksa efeknya pada role lain.
3. **Sistem View (Blade)**:
   - Ingat bahwa Admin menggunakan `layouts.admin`, Guru menggunakan `layouts.guru`, dan Pengawas menggunakan `layouts.pengawas`. Pastikan *controller* mengembalikan *view* yang di-*extend* dengan *layout* yang sesuai dengan role yang login.

*Selamat beristirahat! Anda bisa melanjutkan pengembangan kapan saja dengan aman. Cukup hapus atau perbarui catatan ini jika sudah tidak relevan di masa depan.*
