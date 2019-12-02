<?php

namespace App\Providers;

use App\Support\DockerSync\EventSubscriber as DockerSyncSubscriber;
use App\Support\Mutagen\EventSubscriber as MutagenSubscriber;

class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        MutagenSubscriber::class,
        DockerSyncSubscriber::class,
    ];
}
