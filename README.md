# Catatan Perubahan CBT


## Fitur yang Sudah Dikerjakan & Berfungsi
1. **Pilihan Kelas di Hasil Ujian**: Pada menu Hasil Ujian, sudah terdapat dropdown untuk filter kelas/grup yang terintegrasi dengan tabel DataTables Server Side dan tombol Export Excel.
2. **DataTables Server Side Yajra**: Sudah diimplementasikan di tabel Hasil Ujian.
3. **Export Excel & PDF di Leger Peserta**: Fitur Export Excel dan PDF pada Leger Peserta sudah ditambahkan dan dapat berjalan normal, asalkan pengguna telah memilih filter *Event* dan *Kelas*.
4. **Tema Warna Login**: Tombol dan aksen pada halaman Login Siswa serta Admin menggunakan warna biru dongker (`#1e40af`).
5. **Route Login Admin**: Akses `/admin` akan langsung dialihkan secara otomatis ke rute halaman Login Admin.
6. **Fitur Anti Curang**: 
   - Fitur *enforce_focus* aktif sesuai dengan Pengaturan Ujian.
   - Apabila siswa berpindah tab/aplikasi (blur/visibility hidden), sistem akan langsung **memblokir** ujian tersebut.
   - Siswa akan logout paksa dan pada dashboard berstatus **Diblokir**.
   - Siswa **TIDAK BISA** melanjutkan ujian sebelum Admin / Guru Mapel melakukan **Reset** hasil ujiannya (dengan cara menghapus data hasil ujian milik siswa tersebut di panel admin).
7. **Unblock (Buka Blokir) tanpa Hapus Hasil Ujian**: Pada menu *Status Peserta* (Monitoring), admin kini dapat melakukan klik tombol **Buka Blokir** untuk siswa yang terblokir. Ini akan mengubah status dari *Diblokir* menjadi *Berlangsung*, sehingga siswa bisa login dan melanjutkan ujiannya tanpa kehilangan jawaban sebelumnya.
8. **Pengaturan Sesi**: 
   - Sistem kini memiliki menu **Master Data -> Pengaturan Sesi**. Admin dapat menambah, mengedit, dan menghapus sesi ujian (misal: Sesi 1 = 07:30 - 09:30).
   - Validasi sesi diterapkan langsung secara *realtime* di Dashboard Siswa dan proses akses/memulai ujian.
   - Sisa waktu dihitung bukan hanya berdasarkan batas waktu jadwal, tetapi juga dibatasi oleh batas akhir waktu Sesi yang dikonfigurasi. Siswa di luar jam sesi otomatis mendapat status ujiannya "Belum Dimulai" atau "Selesai" (tidak bisa mengakses).
9. **Routing & Tampilan Login Guru**:
   - Rute `/guru` secara otomatis dialihkan ke halaman form login khusus guru.
   - Warna tombol dan tema pada halaman Login Guru juga telah disamakan menjadi warna biru dongker (`#1e40af`) untuk menjaga konsistensi.

## UPDATE: Implentasi 9 Tipe Soal (AKM)
10. **Implementasi Keseluruhan 9 Tipe Soal Baru**:
    - Termasuk: PG, PG Kompleks, Menjodohkan (MJ), Mengurutkan (MT), Benar/Salah (BS), Setuju/Tidak (ST), Isian Singkat (IS), Uraian (UR), dan Listening (LS).
    - Tabel `questions` telah dimodifikasi (kolom `type` menjadi VARCHAR, `audio_path`, dan `complex_options` berbasis JSON).
    - Antarmuka **Tambah dan Edit Soal** di panel **Guru dan Admin** sudah sangat dinamis sesuai dengan jenis soal, lengkap dengan *header* dan instruksi pengisian untuk tipe Menjodohkan dan Mengurutkan.
11. **Lembar Ujian Siswa Dinamis (AJAX)**:
    - Halaman Ujian Siswa (`exam.blade.php`) dirender sepenuhnya secara reaktif via JavaScript dan AJAX.
    - Soal tipe baru dirender sesuai formatnya (Dropdown untuk MJ & MT, Kotak Centang (Checkbox) untuk PGK, Radio untuk BS/ST/PG, Textarea untuk Essay/IS).
    - Khusus untuk tipe Menjodohkan dan Mengurutkan, pilihan jawaban otomatis diacak (*shuffle*) secara aman dari server tanpa membocorkan kunci jawaban di sisi siswa.
12. **Penilaian Otomatis (Auto-Grading)**:
    - Seluruh soal objektif baru dinilai secara otomatis di `Siswa\ExamController.php` pada fungsi `finish()`.
    - Sistem menerapkan model **Mutlak (All-or-Nothing)**: centang lebih/kurang satu pada PGK = poin 0.
13. **Import Soal Word Mendukung 9 Tipe Soal**:
    - Fitur **Import dari Word (Docx)** kini tersedia di panel **Admin** dan panel **Guru**.
    - Proses ekstraksi dari Word mengenali berbagai tipe format jika disematkan kode khusus (seperti `TIPE: PGK`, `TIPE: MJ`, `KIRI:)`, `KANAN:)`, dll).
    - File **Download Template** (otomatis _generate_ contoh format soal baru) sudah diperbarui dengan instruksi lengkap untuk seluruh tipe.

---

## CATATAN UNTUK PENGEMBANGAN BERIKUTNYA (TO-DO LIST)
Bila ingin meneruskan pengembangan aplikasi ini di sesi selanjutnya, ini adalah hal-hal yang perlu dicek/diselesaikan:

1. **Menu Pengolahan / Analisis (Leger Hasil Ujian)**
   - **Tugas**: Pastikan ekspor Excel/Cetak pada Analisis Butir Soal dan Leger Nilai sudah membaca kolom-kolom persentase baru (seperti nilai PGK, nilai Menjodohkan) dengan tepat. 
   - **Konteks**: Saat ini Penilaian di akhir ujian (Siswa ExamController) sudah diakumulasi ke dalam persentase, namun modul Analisis dan Laporan di Admin mungkin perlu *upgrade* agar tidak sekedar membaca poin "Benar / Salah PG".
2. **Scoring Parsial (Opsi)**
   - **Tugas**: Jika pihak sekolah menghendaki agar tipe soal **PGK** (Pilihan Ganda Kompleks) menggunakan poin parsial (misal centang benar 2 dari total 3 dapat sebagian poin, bukan nol), logika perhitungannya harus diedit di `App\Http\Controllers\Siswa\ExamController.php` function `finish()`.
3. **Audio Listening pada Import Word**
   - **Konteks**: Saat ini Import Word hanya membaca teks dan mengambil gambar. Ia tidak bisa mengekstrak lampiran file Audio/MP3 dari Word.
   - **Tugas**: Jika guru meng-_import_ soal Listening dari Word, guru tetap harus masuk ke fitur "Edit Soal" secara manual di Web untuk meng-_upload_ file MP3-nya. Tidak ada _error_, tapi pastikan pengguna/guru mengetahui keterbatasan ini.
