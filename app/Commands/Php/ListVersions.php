<?php

namespace App\Commands\Php;

use App\PhpVersion;
use App\Site;
use LaravelZero\Framework\Commands\Command;

class ListVersions extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List available sites';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $headers = ['Version Number', 'Default', 'Active'];

        $activeVersions = PhpVersion::active()->get();

        $versions = PhpVersion::orderBy('name', 'asc')
            ->get()
            ->map(function ($version) use ($activeVersions) {
                return [
                    $version->version_number,
                    $version->default ? 'yes' : '-',
                    $activeVersions->contains($version) ? 'yes' : '-',
                ];
            });

        $this->table($headers, $versions);
    }
}