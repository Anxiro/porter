<?php

namespace App\Providers;

use App\Porter;
use App\PorterLibrary;
use App\Support\Console\Cli;
use App\Support\Console\ConsoleWriter;
use App\Support\Console\ServerBag;
use App\Support\Contracts\Cli as CliContract;
use App\Support\Images\Organiser\Organiser;
use App\Support\Mechanics\ChooseMechanic;
use App\Support\Mechanics\Mechanic;
use App\Support\Ssl\CertificateBuilder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(PorterLibrary::class)->registerViews($this->app);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CertificateBuilder::class, function () {
            return new CertificateBuilder(
                app(CliContract::class),
                app(Filesystem::class),
                app(Mechanic::class),
                app(PorterLibrary::class)->sslPath()
            );
        });

        $this->app->bind(ConsoleWriter::class);

        $this->app->bind(CliContract::class, Cli::class);

        $this->app->bind(Organiser::class, function () {
            return new Organiser(
                app(PorterLibrary::class)->getDockerImageSet(),
                app(CliContract::class),
                app(FileSystem::class)
            );
        });

        $this->app->singleton(Porter::class);
        $this->app->singleton(PorterLibrary::class);
        $this->app->singleton(ServerBag::class);

        $this->app->bind(Mechanic::class, function () {
            return ChooseMechanic::forOS();
        });
    }
}
