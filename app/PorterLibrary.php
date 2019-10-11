<?php

namespace App;

use App\Exceptions\PorterSetupFailed;
use App\Support\FilePublisher;
use App\Support\Mechanics\Mechanic;
use App\Support\Images\ImageSetRepository;
use App\Support\Contracts\ImageRepository;
use App\Support\Contracts\ImageSetRepository as ImageSetRepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;

class PorterLibrary
{
    /**
     * The docker images sets used by Porter to serve sites.
     *
     * @var ImageSetRepository
     */
    protected $imageSets;

    /**
     * The path of the Porter library directory (e.g. ~/.porter on Mac).
     *
     * @var string
     */
    protected $path;

    /**
     * The system's filesystem.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The file publisher.
     *
     * @var \App\Support\FilePublisher
     */
    protected $filePublisher;

    /**
     * @var Mechanic
     */
    private $mechanic;

    /**
     * @var PorterLibrarySetup
     */
    protected $setup;

    public function __construct(
        FilePublisher $filePublisher,
        Mechanic $mechanic,
        $path = null
    ) {
        $this->filePublisher = $filePublisher;
        $this->setPath(is_null($path) ? config('porter.library_path') : $path);
        $this->mechanic = $mechanic;

        $this->files = $filePublisher->getFilesystem();
        $this->imageSets = new ImageSetRepository([
                resource_path('image_sets'),
                $this->dockerImagesPath(),
            ]);
        $this->setup = new PorterLibrarySetup($this);
    }

    /**
     * Set the Mechanic instance.
     *
     * @param Mechanic $mechanic
     *
     * @return $this
     */
    public function setMechanic(Mechanic $mechanic)
    {
        $this->mechanic = $mechanic;

        return $this;
    }

    /**
     * Set the ImageSetRepository instance.
     *
     * @param  ImageSetRepositoryContract  $imageSets
     *
     * @return $this
     */
    public function setImageSets(ImageSetRepositoryContract $imageSets)
    {
        $this->imageSets = $imageSets;

        return $this;
    }

    /**
     * Return the path for storing container config files.
     *
     * @return string
     */
    public function configPath()
    {
        return $this->path.'/config';
    }

    /**
     * Return the path of the database file.
     *
     * @return string
     */
    public function databaseFile()
    {
        return $this->path.'/database.sqlite';
    }

    /**
     * Return the path of the docker-compose file.
     *
     * @return string
     */
    public function dockerComposeFile()
    {
        return $this->path.'/docker-compose.yaml';
    }

    /**
     * Return the path of additional docker images.
     *
     * @return string
     */
    public function dockerImagesPath()
    {
        return $this->path.'/image-sets';
    }

    /**
     * Return the path where our generated SSL certs live.
     *
     * @return string
     */
    public function sslPath()
    {
        return $this->path.'/ssl';
    }

    /**
     * Return the library path.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Return the path for our additional/customised views
     * For example NGiNX config/ docker-compose views
     * for alternative container structures.
     *
     * @return string
     */
    public function viewsPath()
    {
        return $this->path.'/views';
    }

    /**
     * Set up the library, by creating files, storing the path in .env
     * creating the sqlite database and updating the app config.
     *
     * @param Application $app
     * @param bool        $force
     *
     * @throws PorterSetupFailed
     */
    public function setUp(Application $app, $force = false)
    {
        $this->setup->run($app, $force);
    }

    /**
     * Return the Mechanic instance.
     *
     * @return Mechanic
     */
    public function getMechanic()
    {
        return $this->mechanic;
    }

    /**
     * Register the view paths and locations
     *
     * @param  Application  $app
     *
     * @return void
     */
    public function registerViews(Application $app)
    {
        view()->getFinder()->prependLocation($this->viewsPath());

        $this->imageSets->registerViewNamespaces($app);
    }

    /**
     * Get the current image set to use.
     *
     * @return ImageRepository
     * @throws \Exception
     */
    public function getDockerImageSet()
    {
        return $this->imageSets->getImageRepository(
            setting('docker_image_set', config('porter.default-docker-image-set'))
        );
    }

    /**
     * Return the file system.
     *
     * @return Filesystem
     */
    public function getFileSystem()
    {
        return $this->files;
    }

    /**
     * Set the path.
     *
     * @param  string  $path
     *
     * @return PorterLibrary
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Publish dirs/files.
     *
     * @param $from
     * @param $to
     *
     * @throws \Exception
     */
    public function publish($from, $to)
    {
        $this->filePublisher->publish($from, $to);
    }

    /**
     * Make sure we don't try to seed and migrate (usually in tests).
     *
     * @return $this
     */
    public function dontMigrateAndSeedDatabase()
    {
       $this->setup->dontMigrateAndSeedDatabase();

        return $this;
    }

    /**
     * Check if the library has previously been set up.
     *
     * @return bool
     */
    public function alreadySetUp()
    {
        return $this->setup->alreadySetUp();
    }
}
