<?php

namespace App\DockerCompose;

use App\PhpVersion;

class YamlBuilder
{
    public function build()
    {
        file_put_contents(
            base_path('docker-compose.yaml'),
            view('docker_compose.base')
                ->with([
                    'home' => setting('home'),
                    'db_host' => setting('db_host'),
                    'activePhpVersions' => PhpVersion::active()->get(),
                ])
                ->render()
        );
    }

    public function destroy()
    {
        @unlink(base_path('docker-compose.yaml'));
    }
}
