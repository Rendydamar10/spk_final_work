<?php

namespace App\Services\Crypto;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CoinGeckoService
{
    private string $baseUrl;

    private ?string $apiKey;

    private string $plan;

    private int $timeoutSeconds;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config(
                'crypto.coingecko.base_url',
                'https://api.coingecko.com/api/v3'
            ),
            '/'
        );

        $this->apiKey = config(
            'crypto.coingecko.api_key'
        );

        $this->plan = strtolower(
            (string) config(
                'crypto.coingecko.plan',
                'demo'
            )
        );

        $this->timeoutSeconds = (int) config(
            'crypto.coingecko.timeout_seconds',
            30
        );

        $this->validateConfiguration();
    }

    /**
     * Membuat HTTP client CoinGecko.
     */
    private function client(): PendingRequest
    {
        $headers = [];

        if (!empty($this->apiKey)) {
            $headers[$this->apiKeyHeader()] =
                $this->apiKey;
        }

        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->withHeaders($headers)
            ->connectTimeout(10)
            ->timeout($this->timeoutSeconds);
    }

    /**
     * Menentukan nama header berdasarkan jenis paket.
     */
    private function apiKeyHeader(): string
    {
        return match ($this->plan) {
            'pro' => 'x-cg-pro-api-key',
            default => 'x-cg-demo-api-key',
        };
    }

    /**
     * Memastikan konfigurasi Demo dan Pro tidak tertukar.
     */
    private function validateConfiguration(): void
    {
        if (!in_array($this->plan, ['demo', 'pro'], true)) {
            throw new RuntimeException(
                'COINGECKO_PLAN harus bernilai demo atau pro.'
            );
        }

        if (empty($this->apiKey)) {
            throw new RuntimeException(
                'COINGECKO_API_KEY belum diisi pada file .env.'
            );
        }

        if (
            $this->plan === 'demo' &&
            str_contains($this->baseUrl, 'pro-api.coingecko.com')
        ) {
            throw new RuntimeException(
                'Konfigurasi CoinGecko tidak sesuai: plan demo '
                .'tidak boleh menggunakan Pro API URL.'
            );
        }

        if (
            $this->plan === 'pro' &&
            !str_contains($this->baseUrl, 'pro-api.coingecko.com')
        ) {
            throw new RuntimeException(
                'Konfigurasi CoinGecko tidak sesuai: plan pro '
                .'harus menggunakan https://pro-api.coingecko.com/api/v3.'
            );
        }
    }

    /**
     * Memeriksa response dari CoinGecko.
     */
    private function ensureSuccessful(
        Response $response,
        string $operation
    ): void {
        if ($response->successful()) {
            return;
        }

        $message = $response->json('error')
            ?? $response->json('status.error_message')
            ?? $response->body();

        if ($response->status() === 401) {
            throw new RuntimeException(
                $operation
                .' gagal dengan HTTP 401. '
                .'API key tidak terkirim, tidak valid, '
                .'atau jenis key tidak sesuai dengan header.'
            );
        }

        if ($response->status() === 403) {
            throw new RuntimeException(
                $operation
                .' gagal dengan HTTP 403. '
                .'Periksa paket CoinGecko, izin endpoint, '
                .'base URL, dan jenis API key.'
            );
        }

        if ($response->status() === 429) {
            $retryAfter = $response->header('Retry-After');

            $retryMessage = $retryAfter
                ? " Coba kembali setelah {$retryAfter} detik."
                : ' Kurangi frekuensi request dan gunakan cache.';

            throw new RuntimeException(
                $operation
                .' gagal dengan HTTP 429. '
                .'Batas request CoinGecko telah tercapai.'
                .$retryMessage
            );
        }

        throw new RuntimeException(
            $operation
            .' gagal dengan HTTP '
            .$response->status()
            .'. Response: '
            .mb_substr((string) $message, 0, 500)
        );
    }

    /**
     * Menguji koneksi dan autentikasi.
     */
    public function ping(): array
    {
        $response = $this->client()->get('/ping');

        $this->ensureSuccessful(
            $response,
            'CoinGecko ping'
        );

        return $response->json() ?? [];
    }

    /**
     * Mengambil daftar data pasar.
     */
    public function markets(
        int $page = 1,
        int $perPage = 100
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min($perPage, 250));

        $response = $this->client()->get(
            '/coins/markets',
            [
                'vs_currency' => config(
                    'crypto.currency',
                    'usd'
                ),
                'order' => 'market_cap_desc',
                'per_page' => $perPage,
                'page' => $page,
                'sparkline' => 'false',
                'price_change_percentage' =>
                    '24h,7d,30d',
            ]
        );

        $this->ensureSuccessful(
            $response,
            'CoinGecko markets'
        );

        return $response->json() ?? [];
    }

    /**
     * Mengambil data pasar berdasarkan ID coin.
     */
    public function marketByIds(array $ids): array
    {
        $ids = collect($ids)
            ->filter(
                fn ($id) =>
                    is_string($id) &&
                    trim($id) !== ''
            )
            ->map(
                fn ($id) =>
                    trim($id)
            )
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return [];
        }

        $response = $this->client()->get(
            '/coins/markets',
            [
                'vs_currency' => config(
                    'crypto.currency',
                    'usd'
                ),
                'ids' => implode(',', $ids),
                'order' => 'market_cap_desc',
                'per_page' => min(count($ids), 250),
                'page' => 1,
                'sparkline' => 'false',
                'price_change_percentage' =>
                    '24h,7d,30d',
            ]
        );

        $this->ensureSuccessful(
            $response,
            'CoinGecko marketByIds'
        );

        return $response->json() ?? [];
    }

    /**
     * Mencari cryptocurrency.
     */
    public function search(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        $cacheKey = 'crypto:coingecko:search:'
            .sha1(mb_strtolower($query));

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(15),
            function () use ($query): array {
                $response = $this->client()->get(
                    '/search',
                    [
                        'query' => $query,
                    ]
                );

                $this->ensureSuccessful(
                    $response,
                    'CoinGecko search'
                );

                return $response->json('coins') ?? [];
            }
        );
    }

    /**
     * Mengambil histori harga untuk perhitungan volatilitas.
     */
    public function marketChart(
        string $coinGeckoId,
        int $days = 30,
        ?string $currency = null
    ): array {
        $coinGeckoId = trim($coinGeckoId);

        if ($coinGeckoId === '') {
            throw new RuntimeException(
                'CoinGecko ID tidak boleh kosong.'
            );
        }

        $days = max(2, min($days, 365));

        $currency ??= config(
            'crypto.currency',
            'usd'
        );

        $ttlSeconds = (int) config(
            'crypto.cache.market_chart_ttl_seconds',
            21600
        );

        $cacheKey = sprintf(
            'crypto:coingecko:market-chart:%s:%s:%d',
            $coinGeckoId,
            $currency,
            $days
        );

        return Cache::remember(
            $cacheKey,
            now()->addSeconds($ttlSeconds),
            function () use (
                $coinGeckoId,
                $days,
                $currency
            ): array {
                $response = $this->client()->get(
                    '/coins/'.$coinGeckoId.
                    '/market_chart',
                    [
                        'vs_currency' => $currency,
                        'days' => $days,
                    ]
                );

                $this->ensureSuccessful(
                    $response,
                    'CoinGecko market chart'
                );

                return $response->json() ?? [];
            }
        );
    }
}