# Rencana Pengujian Skripsi

## Unit test

- Normalisasi benefit menghasilkan 0 untuk minimum dan 1 untuk maksimum.
- Normalisasi cost menghasilkan 1 untuk minimum dan 0 untuk maksimum.
- Nilai yang sama seluruhnya menghasilkan normalisasi 1.
- Volatilitas cocok dengan sample standard deviation perhitungan manual.
- Data tidak lengkap dikeluarkan dari ranking.
- Total bobot tidak sama dengan 1 ditolak.
- Tipe dan bobot kriteria tidak valid ditolak.
- Skor sama menghasilkan urutan deterministik.

## Integration test

- Refresh CoinGecko menyimpan data, menghubungkan alternatif, dan menghitung ranking.
- Ranking global tidak tercampur dengan ranking pribadi.
- Perubahan bobot menghasilkan kalkulasi ulang yang konsisten.
- Hasil lama yang tidak lagi memenuhi syarat dihapus.
- Kegagalan API dicatat pada log dan ditampilkan secara aman.

## Black-box test

Uji login, otorisasi admin, CRUD kriteria, validasi bobot, refresh data, tambah/hapus alternatif, watchlist, perbandingan, detail perhitungan, pagination, ekspor laporan, dan tampilan responsif.

## UAT

Gunakan minimal lima responden. Nilai kemudahan penggunaan, kejelasan informasi, transparansi perhitungan, kecepatan, dan kesesuaian hasil. Simpan instrumen, jawaban, rekap persentase, dan kesimpulan.

## Bukti yang dilampirkan

- Output `php artisan test`.
- Tabel perhitungan manual dan hasil sistem.
- Screenshot skenario utama.
- Matriks pengujian berisi ID, langkah, data uji, hasil yang diharapkan, hasil aktual, dan status.
