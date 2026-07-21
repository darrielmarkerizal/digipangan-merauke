<?php

namespace Modules\Media\Console;

use Illuminate\Console\Command;
use Modules\Media\Services\TemporaryMediaService;

class ClearTemporaryMediaCommand extends Command
{
    protected $signature = 'media:clear-temp';

    protected $description = 'Clear temporary media files older than 24 hours';

    public function handle(TemporaryMediaService $service)
    {
        $this->info('Clearing temporary media files...');

        $count = $service->clearExpiredMedia(24);

        $this->info("Cleared $count temporary media folders/files.");
    }
}
