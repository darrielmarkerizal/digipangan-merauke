<?php

namespace Modules\Product\Console;

use Illuminate\Console\Command;
use Modules\Product\Models\ProductInteraction;

class PurgeProductInteractionsCommand extends Command
{
    protected $signature = 'interactions:purge {--months=24}';

    protected $description = 'Hapus baris product_interactions yang lebih tua dari N bulan (default 24).';

    public function handle(): int
    {
        $months = max(1, (int) $this->option('months'));
        $cutoff = now()->subMonths($months);

        $deleted = ProductInteraction::where('occurred_at', '<', $cutoff)->delete();

        $this->info("Menghapus {$deleted} interaksi lebih tua dari {$months} bulan.");

        return self::SUCCESS;
    }
}
