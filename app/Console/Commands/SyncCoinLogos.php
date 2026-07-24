<?php

namespace App\Console\Commands;

use App\Models\CryptoCoin;
use App\Services\Crypto\CoinLogoStorageService;
use Illuminate\Console\Command;

class SyncCoinLogos extends Command
{
    protected $signature = 'crypto:sync-logos {--force : Periksa ulang seluruh logo meskipun file lokal sudah ada}';

    protected $description = 'Unduh dan sinkronkan logo CoinGecko ke storage lokal';

    public function handle(CoinLogoStorageService $logoStorage): int
    {
        $query = CryptoCoin::query()->orderBy('id');

        if (!$this->option('force')) {
            $query->where(function ($builder) {
                $builder->whereNull('logo_path')
                    ->orWhereNull('logo_source_url');
            });
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('Semua logo coin sudah tersinkronisasi.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();
        $updated = 0;

        $query->chunkById(50, function ($coins) use ($logoStorage, $bar, &$updated) {
            foreach ($coins as $coin) {
                $sourceUrl = $coin->image ?: $coin->logo_source_url;
                $path = $logoStorage->sync($coin, $coin->coingecko_id, $sourceUrl);

                if ($path !== null) {
                    $coin->forceFill([
                        'logo_path' => $path,
                        'logo_source_url' => $sourceUrl,
                    ])->save();
                    $updated++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info($updated.' logo berhasil disimpan atau diperbarui.');

        return self::SUCCESS;
    }
}
