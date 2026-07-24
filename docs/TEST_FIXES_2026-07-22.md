# Perbaikan Test Suite — 22 Juli 2026

Perbaikan ini dibuat berdasarkan hasil `php artisan test` yang menunjukkan 18 kegagalan.

## Perubahan

1. Helper criteria pada `SawServiceTest` dan `RankingScopeTest` kini memakai `updateOrCreate`, karena migration optimasi memang sudah memasukkan criteria bawaan.
2. Test bobot tidak valid menonaktifkan criteria bawaan sebelum mengaktifkan satu criterion dengan total bobot 0,75.
3. Kolom `criteria.type` diubah dari ENUM menjadi `VARCHAR(20)`. Dengan demikian, nilai domain yang salah dapat diterima sementara oleh persistence layer lalu ditolak secara eksplisit oleh `SawService` dengan `DomainException`.
4. Migration kompatibilitas ditambahkan untuk instalasi MySQL yang sudah ada.
5. `APP_KEY` testing ditambahkan ke `phpunit.xml` agar halaman Blade yang memakai session, CSRF, dan enkripsi tidak menghasilkan HTTP 500 hanya karena environment test tidak memiliki `.env`.

## Menjalankan ulang

```bash
php artisan optimize:clear
php artisan migrate
php artisan test
```

Untuk database pengembangan yang boleh dihapus seluruhnya:

```bash
php artisan migrate:fresh --seed
php artisan test
```
