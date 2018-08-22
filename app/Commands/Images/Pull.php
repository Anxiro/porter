<?php

namespace App\Commands\Images;

use App\Porter;
use LaravelZero\Framework\Commands\Command;

class Pull extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'images:pull';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Pull the selected set of container images';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->pullImages();
    }
}
