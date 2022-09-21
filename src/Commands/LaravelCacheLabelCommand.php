<?php

namespace Mis3085\LaravelCacheLabel\Commands;

use Illuminate\Console\Command;

class LaravelCacheLabelCommand extends Command
{
    public $signature = 'laravel-cache-label';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
