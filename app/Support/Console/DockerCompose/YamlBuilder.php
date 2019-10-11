<?php

namespace App\Support\Console\DockerCompose;

use App\Models\PhpVersion;
use App\PorterLibrary;
use App\Support\Contracts\ImageRepository;
use Illuminate\Filesystem\Filesystem;

class YamlBuilder
{
    /** @var PorterLibrary */
    private $porterLibrary;

    public function __construct(PorterLibrary $porterLibrary)
    {
        $this->porterLibrary = $porterLibrary;
    }

    /**
     * Build the docker-compose.yaml file.
     *
     * @param $imageSet
     *
     * @throws \Throwable
     */
    public function build(ImageRepository $imageSet)
    {
        $this->porterLibrary->getFileSystem()->put(
            $this->porterLibrary->dockerComposeFile(),
            $this->renderDockerComposeFile($imageSet)
        );
    }

    /**
     * Render the docker compose file.
     *
     * @param ImageRepository $imageSet
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function renderDockerComposeFile(ImageRepository $imageSet)
    {
        return view("{$imageSet->getName()}::base")->with([
            'home'              => setting('home'),
            'host_machine_name' => setting('host_machine_name'),
            'activePhpVersions' => PhpVersion::active()->get(),
            'useMysql'          => setting('use_mysql') === 'on',
            'useRedis'          => setting('use_redis') === 'on',
            'useBrowser'        => setting('use_browser') === 'on',
            'useDns'            => setting('use_dns') === 'on' || setting_missing('use_dns'),
            'imageSet'          => $imageSet,
            'libraryPath'       => $this->porterLibrary->path(),
        ])->render();
    }

    /**
     * Destroy the docker-compose.yaml file.
     */
    public function destroy()
    {
        $this->porterLibrary->getFileSystem()->delete($this->porterLibrary->dockerComposeFile());
    }
}
