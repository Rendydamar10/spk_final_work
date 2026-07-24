# SPK Pemilihan Cryptocurrency — Metode SAW

Aplikasi skripsi berbasis **Laravel 10**, Blade, Tailwind CSS, dan CoinGecko API untuk membantu pengguna menyusun peringkat cryptocurrency menggunakan **Simple Additive Weighting (SAW)**.

## Fitur utama

**Admin:** dashboard, pengelolaan cryptocurrency, kriteria dan bobot, refresh CoinGecko, ranking global, log API, serta ekspor laporan.

**Pengguna:** ranking pribadi, pencarian dan penambahan coin, watchlist, perbandingan coin, grafik harga, dan halaman penjelasan metode.

**Audit perhitungan:** sistem menyimpan nilai mentah, nilai normalisasi, nilai terbobot, skor akhir, peringkat, dan waktu perhitungan untuk setiap alternatif.

## Kriteria default final

| Kode | Kriteria | Atribut | Bobot |
|---|---|---|---:|
| `market_cap` | Kapitalisasi Pasar | Benefit | 0,25 |
| `total_volume` | Volume Transaksi 24 Jam | Benefit | 0,20 |
| `price_change_percentage_24h` | Perubahan Harga 24 Jam | Benefit | 0,05 |
| `price_change_percentage_7d_in_currency` | Perubahan Harga 7 Hari | Benefit | 0,10 |
| `price_change_percentage_30d_in_currency` | Perubahan Harga 30 Hari | Benefit | 0,15 |
| `volatility` | Volatilitas Historis 30 Hari | Cost | 0,25 |

Total bobot kriteria aktif wajib tepat **1,0000**. `market_cap_rank` disimpan sebagai data informatif tetapi dinonaktifkan agar tidak menduplikasi pengaruh kapitalisasi pasar.

> Bobot default adalah konfigurasi awal aplikasi. Dalam naskah skripsi, dasar penetapan bobot harus dijelaskan melalui literatur, pakar, kuesioner, atau metode pembobotan yang digunakan peneliti.

## Rumus yang digunakan

Karena kriteria perubahan harga dapat bernilai negatif, aplikasi menggunakan normalisasi min–max.

Benefit:

```text
rij = (xij - min xj) / (max xj - min xj)
```

Cost:

```text
rij = (max xj - xij) / (max xj - min xj)
```

Nilai preferensi:

```text
Vi = Σ (wj × rij)
```

Jika seluruh alternatif memiliki nilai sama pada suatu kriteria, nilai normalisasi kriteria tersebut ditetapkan `1`. Alternatif yang kehilangan satu atau lebih nilai kriteria aktif tidak diikutkan dalam perhitungan dan tidak diperlakukan sebagai nol.

Volatilitas dihitung sebagai **sample standard deviation** dari return harian selama 30 hari dalam satuan persen.

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate:fresh --seed
php artisan serve
```

Konfigurasi CoinGecko pada `.env`:

```env
COINGECKO_BASE_URL=https://api.coingecko.com/api/v3
COINGECKO_API_KEY=
CRYPTO_VS_CURRENCY=usd
```

Jalankan pengujian:

```bash
php artisan test
```

Pemeriksaan gaya kode:

```bash
./vendor/bin/pint --test
```

## Akun dan hak akses

Seeder dapat digunakan untuk membuat data awal. Untuk mengubah pengguna menjadi admin:

```bash
php artisan tinker
```

```php
$user = App\Models\User::first();
$user->update(['role' => 'admin']);
```

## Artefak skripsi

Dokumen pendukung terdapat di folder `docs/`:

- `METODOLOGI-SAW.md`: definisi kriteria, rumus, asumsi, dan contoh verifikasi.
- `PENGUJIAN-SKRIPSI.md`: skenario black-box, unit, integrasi, dan UAT.
- `CHECKLIST-SIDANG.md`: pemeriksaan akhir sebelum demonstrasi.

## Batasan sistem

Aplikasi adalah sistem pendukung keputusan, bukan pemberi nasihat investasi. Hasil ranking bergantung pada alternatif, data CoinGecko pada waktu pengambilan, kriteria aktif, dan bobot yang digunakan. Ranking dapat berubah ketika salah satu unsur tersebut berubah.
