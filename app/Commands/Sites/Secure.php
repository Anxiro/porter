<?php

namespace App\Commands\Sites;

use App\Porter;
use App\Site;
use App\Ssl\CertificateBuilder;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Secure extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'secure {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Secure a site';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('site') ?: site_from_cwd();

        if (! $name) {
            throw new \Exception("Site '{$name}' not found.");
        }

        $site = Site::firstOrCreateForName($name);
        $site->secure();
    }
}
