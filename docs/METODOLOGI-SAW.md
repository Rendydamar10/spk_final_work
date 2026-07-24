# Metodologi SAW yang Diimplementasikan

## Tujuan keputusan

Sistem membantu menyusun prioritas cryptocurrency berdasarkan performa pasar dan risiko historis. Hasil sistem bersifat rekomendasi komparatif pada himpunan alternatif yang sedang dihitung, bukan prediksi keuntungan.

## Tahapan

1. Mengambil data alternatif dari CoinGecko.
2. Menghapus stablecoin dari ranking global.
3. Menghitung volatilitas historis dari harga penutupan harian.
4. Memastikan setiap alternatif memiliki seluruh nilai kriteria aktif.
5. Memvalidasi tipe kriteria dan total bobot.
6. Membentuk matriks keputusan.
7. Melakukan normalisasi min–max benefit/cost.
8. Mengalikan nilai normalisasi dengan bobot.
9. Menjumlahkan nilai terbobot menjadi skor preferensi.
10. Mengurutkan skor menurun; jika sama, market cap dan ID CoinGecko digunakan sebagai tie-breaker deterministik.

## Alasan normalisasi min–max

Rumus rasio SAW klasik dapat menghasilkan perilaku yang sulit ditafsirkan ketika nilai benefit negatif. Min–max memetakan setiap kriteria ke rentang 0–1 secara konsisten, termasuk ketika perubahan harga bernilai negatif. Pemilihan ini wajib dinyatakan sebagai adaptasi metode pada Bab Metodologi dan digunakan sama dalam perhitungan manual.

## Aturan kualitas data

- Nilai `null`, string kosong, nonnumerik, `INF`, dan `NaN` tidak diterima.
- Alternatif tidak lengkap dikeluarkan dari kalkulasi.
- Total bobot kriteria aktif harus 1,0000 dengan toleransi 0,0001.
- Tipe kriteria hanya `benefit` atau `cost`.
- Kode kriteria aktif harus unik.

## Verifikasi manual minimum

Gunakan sedikitnya tiga alternatif dan enam kriteria. Dokumentasikan matriks awal, nilai minimum/maksimum, matriks normalisasi, perkalian bobot, skor akhir, dan urutan ranking. Bandingkan hingga toleransi pembulatan yang ditentukan dengan keluaran aplikasi.
