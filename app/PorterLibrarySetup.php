<?php


namespace App;


use App\Exceptions\PorterSetupFailed;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Foundation\Application;

class PorterLibrarySetup
{
    /**
     * @var PorterLibrary
     */
    protected $library;

    /**
     * Should we migrate and seed the database?
     *
     * @var bool
     */
    protected $shouldMigrateAndSeedDatabase = true;

    /**
     * PorterLibrarySetup constructor.
     *
     * @param  PorterLibrary  $library
     */
    public function __construct(PorterLibrary $library)
    {
        $this->library = $library;
    }

    public function run(Application $app, $force = false)
    {
        if ($this->alreadySetUp() && !$force) {
            throw new PorterSetupFailed(
                "The porter library already exists at '{$this->library->path()}'. ".
                'You can use the --force flag to continue.'
            );
        }

        if (!$this->library->path()) {
            $this->library->setPath($this->library->getMechanic()->getUserHomePath().'/.porter');

            $this->moveExistingConfig();
            $this->publishEnv();
            $this->updateEnv();
        }

        if (!$this->library->path()) {
            throw new PorterSetupFailed('Failed detecting and setting the library path for Porter.');
        }

        $this->publishConfigFiles();
        $this->createDirectoryStructure();
        $this->createDatabase();
        $this->updateAppConfig($app);

        if ($this->shouldMigrateAndSeedDatabase) {
            Artisan::call('migrate:fresh');
            Artisan::call('db:seed');
        }

        $app->instance(PorterLibrary::class, $this->library);
    }

    /**
     * Check if the library has previously been set up.
     *
     * @return bool
     */
    public function alreadySetUp()
    {
        return $this->library->path() && $this->library->getFileSystem()->exists($this->library->path());
    }

    /**
     * Publish the .env.example file to .env.
     *
     * @throws PorterSetupFailed
     */
    protected function publishEnv()
    {
        try {
            $this->library->publish(base_path('.env.example'), base_path('.env'));
        } catch (\Exception $e) {
            throw new PorterSetupFailed('Failed publishing the .env file');
        }
    }

    /**
     * Move any existing config at the path to a backup directory
     * So we can avoid wiping out data/settings completely.
     */
    protected function moveExistingConfig()
    {
        if (!$this->alreadySetUp()) {
            return;
        }

        $this->library->getFileSystem()->moveDirectory($this->library->path(), $this->library->path().'_'.now()->format('YmdHis'));
    }

    /**
     * Create the sqlite database.
     */
    protected function createDatabase()
    {
        $this->library->getFileSystem()->put($this->library->databaseFile(), '');
    }

    /**
     * Update the .env file values with the new library path.
     *
     * @throws PorterSetupFailed
     */
    protected function updateEnv()
    {
        try {
            $envContent = $this->library->getFileSystem()->get(base_path('.env'));
            $envContent = preg_replace('/LIBRARY_PATH=.*\n/', "LIBRARY_PATH=\"{$this->library->path()}\"\n",
                $envContent);
            $this->library->getFileSystem()->put(base_path('.env'), $envContent);
        } catch (\Exception $e) {
            throw new PorterSetupFailed('Failed changing library path in the .env file', 0, $e);
        }
    }

    /**
     * Update core parts of the app config.
     *
     * @param Application $app
     */
    protected function updateAppConfig(Application $app)
    {
        $app['config']->set('database.connections.default.database', $this->library->databaseFile());
        $app['config']->set('porter.library_path', $this->library->path());
    }

    /**
     * Publish the container config files to the library config dir.
     *
     * @throws PorterSetupFailed
     */
    protected function publishConfigFiles()
    {
        try {
            $this->library->publish(resource_path('stubs/config'), $this->library->configPath());
        } catch (\Exception $e) {
            throw new PorterSetupFailed('Failed publishing the container configuration files');
        }
    }

    /**
     * Make sure we don't try to seed and migrate (usually in tests).
     *
     * @return $this
     */
    public function dontMigrateAndSeedDatabase()
    {
        $this->shouldMigrateAndSeedDatabase = false;

        return $this;
    }

    /**
     * Create the directory structure in the library path.
     */
    protected function createDirectoryStructure()
    {
        $directories = [$this->library->sslPath(), $this->library->viewsPath().'/nginx'];

        foreach ($directories as $directory) {
            if (!$this->library->getFileSystem()->isDirectory($directory)) {
                $this->library->getFileSystem()->makeDirectory($directory, 0755, true);
            }
        }
    }
}
