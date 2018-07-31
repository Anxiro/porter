<?php

namespace App\Commands\Sites;

use App\Site;
use LaravelZero\Framework\Commands\Command;

class Remove extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:remove {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove a site';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('site') ?: site_from_cwd();

        if (! $site = Site::where('name', $name)->first()) {
            throw new \Exception("Site '{$name}' not found.");
        }

        $site->remove();
    }
}
