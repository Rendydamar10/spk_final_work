<?php

namespace App\Services\Crypto;

use App\Models\CryptoCoin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CoinLogoStorageService
{
    private const DIRECTORY = 'coin-logos';

    /**
     * Sinkronkan logo coin yang sudah disimpan di database.
     * File lama tetap dipakai jika unduhan logo baru gagal.
     */
    public function sync(?CryptoCoin $coin, string $coinGeckoId, ?string $sourceUrl): ?string
    {
        $sourceUrl = $this->validUrl($sourceUrl);

        if ($sourceUrl === null) {
            return $coin?->logo_path;
        }

        $currentPath = $coin?->logo_path;
        $sourceUnchanged = $coin?->logo_source_url === $sourceUrl;

        if ($sourceUnchanged && $currentPath && Storage::disk('public')->exists($currentPath)) {
            return $currentPath;
        }

        $newPath = $this->download($coinGeckoId, $sourceUrl);

        if ($newPath === null) {
            return $currentPath;
        }

        if ($currentPath && $currentPath !== $newPath) {
            Storage::disk('public')->delete($currentPath);
        }

        return $newPath;
    }

    /**
     * Simpan logo hasil pencarian CoinGecko agar daftar pencarian juga tidak
     * memuat gambar langsung dari HTTPS CoinGecko.
     */
    public function attachLocalUrls(array $results): array
    {
        return collect($results)->map(function (array $coin): array {
            $coinGeckoId = (string) ($coin['id'] ?? '');
            $sourceUrl = $this->validUrl($coin['large'] ?? $coin['thumb'] ?? null);

            if ($coinGeckoId === '' || $sourceUrl === null) {
                $coin['local_logo_url'] = null;
                return $coin;
            }

            $cacheKey = 'crypto:coin-logo-source:' . sha1($coinGeckoId);
            $cached = Cache::get($cacheKey);
            $path = is_array($cached) ? ($cached['path'] ?? null) : null;
            $sameSource = is_array($cached) && ($cached['source'] ?? null) === $sourceUrl;

            if (!$sameSource || !$path || !Storage::disk('public')->exists($path)) {
                $newPath = $this->download($coinGeckoId, $sourceUrl, 'search');

                if ($newPath !== null) {
                    if ($path && $path !== $newPath) {
                        Storage::disk('public')->delete($path);
                    }

                    $path = $newPath;
                    Cache::forever($cacheKey, [
                        'source' => $sourceUrl,
                        'path' => $path,
                    ]);
                }
            }

            $coin['local_logo_url'] = $path
                ? '/storage/' . ltrim($path, '/')
                : null;

            return $coin;
        })->all();
    }

    private function download(string $coinGeckoId, string $sourceUrl, string $subdirectory = 'coins'): ?string
    {
        try {
            $response = Http::timeout(15)
                ->retry(2, 300)
                ->accept('image/*')
                ->get($sourceUrl);

            if (!$response->successful() || $response->body() === '') {
                return null;
            }

            $contentType = strtolower((string) $response->header('Content-Type'));
            $extension = $this->extensionFromContentType($contentType);

            if ($extension === null) {
                return null;
            }

            $safeId = Str::slug($coinGeckoId) ?: sha1($coinGeckoId);
            $directory = self::DIRECTORY . '/' . $subdirectory;
            $path = $directory . '/' . $safeId . '-' . sha1($sourceUrl) . '.' . $extension;
            $temporaryPath = $directory . '/.' . $safeId . '-' . Str::random(12) . '.tmp';
            $disk = Storage::disk('public');

            $disk->put($temporaryPath, $response->body());

            if (!$disk->exists($temporaryPath) || $disk->size($temporaryPath) === 0) {
                $disk->delete($temporaryPath);
                return null;
            }

            if ($disk->exists($path)) {
                $disk->delete($temporaryPath);
                return $path;
            }

            $disk->move($temporaryPath, $path);

            return $path;
        } catch (Throwable $e) {
            logger()->warning('Gagal menyimpan logo coin ke storage lokal.', [
                'coingecko_id' => $coinGeckoId,
                'source_url' => $sourceUrl,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function extensionFromContentType(string $contentType): ?string
    {
        return match (true) {
            str_contains($contentType, 'image/png') => 'png',
            str_contains($contentType, 'image/jpeg') => 'jpg',
            str_contains($contentType, 'image/webp') => 'webp',
            str_contains($contentType, 'image/gif') => 'gif',
            str_contains($contentType, 'image/svg+xml') => 'svg',
            default => null,
        };
    }

    private function validUrl(mixed $url): ?string
    {
        if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return str_starts_with($url, 'https://') || str_starts_with($url, 'http://')
            ? $url
            : null;
    }
}