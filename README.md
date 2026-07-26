# SPK Crypto SAW

> Sistem Pendukung Keputusan Pemilihan Aset Cryptocurrency Menggunakan Metode Simple Additive Weighting (SAW) Berbasis Web

![Laravel](https://img.shields.io/badge/Laravel-10-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![MySQL](https://img.shields.io/badge/MySQL-8-orange)
![License](https://img.shields.io/badge/License-Academic-green)

---

# Deskripsi

SPK Crypto SAW merupakan aplikasi web yang dibangun sebagai implementasi penelitian skripsi mengenai Sistem Pendukung Keputusan (SPK) untuk membantu pengguna memilih aset cryptocurrency menggunakan metode **Simple Additive Weighting (SAW)**.

Sistem memanfaatkan data cryptocurrency dari **CoinGecko API**, kemudian melakukan proses normalisasi, pembobotan, dan perangkingan sehingga pengguna memperoleh rekomendasi cryptocurrency berdasarkan kriteria yang telah ditentukan.

Aplikasi dibangun menggunakan Laravel Framework dengan konsep MVC dan memiliki dua jenis pengguna, yaitu **Administrator** dan **User**.

---

# Tujuan

Membantu investor maupun calon investor dalam:

- memperoleh informasi cryptocurrency secara terstruktur
- membandingkan beberapa cryptocurrency
- melakukan perangkingan menggunakan metode SAW
- mempermudah proses pengambilan keputusan investasi

---

# Fitur

## User

- Login
- Register
- Dashboard
- Ranking Cryptocurrency
- Watchlist Cryptocurrency
- Compare Coin
- Grafik Harga Cryptocurrency
- Profil Pengguna
- Update Password

---

## Administrator

- Dashboard Admin
- Kelola Cryptocurrency
- Kelola User
- Kelola Bobot Kriteria
- Kelola Kriteria
- Sinkronisasi Data CoinGecko
- Perhitungan SAW

---

# Metode SPK

Metode yang digunakan adalah:

**Simple Additive Weighting (SAW)**

Tahapan:

1. Menentukan alternatif
2. Menentukan kriteria
3. Menentukan bobot
4. Membentuk matriks keputusan
5. Normalisasi
6. Mengalikan dengan bobot
7. Menjumlahkan nilai preferensi
8. Menghasilkan ranking

---

# Kriteria Penilaian

| Kriteria                | Tipe    |
| ----------------------- | ------- |
| Harga                   | Cost    |
| Market Cap              | Benefit |
| Volume 24 Jam           | Benefit |
| Perubahan Harga 24 Jam  | Benefit |
| Perubahan Harga 7 Hari  | Benefit |
| Perubahan Harga 30 Hari | Benefit |
| Volatilitas 30 Hari     | Cost    |

Bobot setiap kriteria dapat diubah oleh Administrator.

---

# Teknologi

Backend

- Laravel 10
- PHP 8.2
- Eloquent ORM

Frontend

- Blade
- Tailwind CSS
- Chart.js
- JavaScript

Database

- MySQL

API

- CoinGecko API

---

# Struktur Sistem

```
User
    │
    ▼
Laravel
    │
    ├── Authentication
    ├── Dashboard
    ├── Ranking
    ├── Watchlist
    ├── Compare Coin
    ├── SAW Engine
    └── CoinGecko Service
                │
                ▼
           CoinGecko API
```

---

# Struktur Folder Penting

```
app/

├── Http/
│   ├── Controllers/
│   └── Middleware/
│
├── Models/
│
├── Services/
│   └── Crypto/
│
├── Console/
│
└── Support/

resources/

├── views/
│
└── components/

database/

├── migrations/
└── seeders/

storage/

└── app/public/coin-logos/
```

---

# Alur Sistem

## Ranking Cryptocurrency

```
User

↓

Pilih Cryptocurrency

↓

Ambil Data CoinGecko

↓

Simpan Database

↓

Hitung SAW

↓

Ranking
```

---

## Watchlist

```
Cari Coin

↓

CoinGecko API

↓

Tambah Watchlist

↓

Dashboard
```

---

## Compare Coin

```
Pilih Coin

↓

2–5 Cryptocurrency

↓

Hitung SAW Lokal

↓

Tampilkan Ranking

↓

Grafik Perbandingan
```

Perhitungan SAW pada menu Compare Coin **tidak mempengaruhi Ranking Saya** karena dihitung secara independen berdasarkan coin yang dipilih.

---

# Penyimpanan Logo Cryptocurrency

Logo cryptocurrency **tidak ditampilkan langsung dari URL CoinGecko**.

Sistem akan:

1. mengambil URL logo dari CoinGecko
2. mengunduh logo
3. menyimpan ke Storage Laravel
4. menampilkan logo lokal

Lokasi penyimpanan:

```
storage/app/public/coin-logos/
```

Browser mengakses melalui:

```
public/storage
```

Keuntungan:

- loading lebih cepat
- tidak bergantung pada CoinGecko
- tidak terjadi broken image
- mudah dipindahkan ke hosting

---

# Struktur Database

Tabel utama

- users
- crypto_coins
- ranking_sets
- ranking_results
- watchlists
- criteria
- criterion_weights

---

# Instalasi

Clone project

```bash
git clone https://github.com/username/spk_crypto_saw.git
```

Masuk folder

```bash
cd spk_final_work
```

Install dependency

```bash
composer install
```

Copy environment

```bash
cp .env.example .env
```

Generate key

```bash
php artisan key:generate
```

Atur database pada file `.env`

```
DB_DATABASE=spk_crypto
DB_USERNAME=root
DB_PASSWORD=
```

Migrasi

```bash
php artisan migrate
```

Seeder

```bash
php artisan db:seed
```

Storage Link

```bash
php artisan storage:link
```

Download logo cryptocurrency

```bash
php artisan crypto:sync-logos --force
```

Jalankan server

```bash
php artisan serve
```

---

# Scheduler

Disarankan menjalankan scheduler Laravel.

Cron Linux

```bash
* * * * * php artisan schedule:run
```

Scheduler digunakan untuk:

- refresh data cryptocurrency
- sinkronisasi CoinGecko
- pembaruan ranking
- sinkronisasi logo

---

# API

Data cryptocurrency diperoleh dari:

CoinGecko API

Data yang digunakan:

- id
- symbol
- name
- image
- current_price
- market_cap
- total_volume
- price_change_percentage_24h
- price_change_percentage_7d
- price_change_percentage_30d
- sparkline
- market_cap_rank

---

# Screenshot

Disarankan menambahkan screenshot berikut:

- Login
- Register
- Dashboard
- Ranking Saya
- Watchlist
- Compare Coin
- Admin Dashboard
- CRUD Cryptocurrency
- CRUD Kriteria
- CRUD Bobot
- Grafik Harga
- Hasil SAW

---

# Keunggulan Sistem

- Menggunakan metode SAW
- Menggunakan CoinGecko API
- Ranking otomatis
- Watchlist pribadi
- Compare Coin
- Grafik harga
- Penyimpanan logo lokal
- Mendukung hosting
- Tidak bergantung pada APP_URL
- Responsive UI
- Multi User
- Laravel MVC

---

# Kekurangan Sistem

- Bergantung pada koneksi internet saat sinkronisasi CoinGecko.
- Data cryptocurrency berupa snapshot sehingga diperlukan proses refresh untuk memperoleh data terbaru.
- Belum mendukung metode SPK lain seperti TOPSIS, MOORA, atau WASPAS.
- Belum menyediakan notifikasi perubahan harga secara real-time.

---

# Pengembangan Selanjutnya

- Integrasi Binance API
- Real-time WebSocket
- Machine Learning Recommendation
- Multi Currency
- Export PDF
- Export Excel
- Mobile App
- Multi Bahasa

---

# Lisensi

Project ini dibuat untuk keperluan akademik sebagai tugas akhir Program Studi Teknik Informatika.

Penggunaan kembali kode diperbolehkan untuk tujuan pembelajaran dengan tetap mencantumkan sumber.

---

# Penulis

**Rendy**

Program Studi Teknik Informatika

Universitas Nahdlatul Ulama Blitar

---

# Pembimbing

(Dapat diisi sesuai dosen pembimbing)

---

# Ucapan Terima Kasih

Penulis mengucapkan terima kasih kepada:

- Allah SWT
- Orang Tua
- Dosen Pembimbing
- Program Studi Teknik Informatika
- Universitas Nahdlatul Ulama Blitar
- CoinGecko sebagai penyedia data cryptocurrency
- Laravel Framework
