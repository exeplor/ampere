<?php

namespace Ampere\Commands;

use Ampere\AmpereServiceProvider;
use Ampere\Services\Config;
use Ampere\Services\StubBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ampere:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install ampere system';

    /**
     * @var StubBuilder
     */
    protected $stubBuilder;

    /**
     * @var string
     */
    protected $controllersNamespace = 'App\Http\Controllers';

    /**
     * @var string
     */
    protected $controllersLocation = 'app/Http/Controllers';

    /**
     * InstallCommand constructor.
     * @param StubBuilder $stubBuilder
     */
    public function __construct(StubBuilder $stubBuilder)
    {
        parent::__construct();

        $this->stubBuilder = $stubBuilder;
    }

    /**
     * Handle command
     */
    public function handle()
    {
        $this->alert('Welcome to Ampere installation service');

        $config = [
            'name' => 'admin',
            'config' => [
                'url_prefix' => 'admin',
                'db_prefix' => 'admin_',
                'space' => 'Admin',
                'public_vendor_name' => 'vendor/admin',
                'namespace' => 'Admin',
                'views_folder' => 'admin',
                'route_prefix' => 'admin'
            ]
        ];

        $configurationRequired = $this->confirm('You want to configure your project?', true);
        if ($configurationRequired) {
            $config = $this->configureProject($config);
        }

        $this->publishConfig($config);
        $this->publishViews($config);
        $this->publishControllers($config);
        $this->publishMigrations($config);
        $this->publishPublicAssets($config);
        $this->publishResources($config);

        $this->alert('Project configured');

        Config::load($config['name']);
        Config::useSpace($config['name']);

        Artisan::call('ampere:migrate');

        $this->comment('Migration completed' . PHP_EOL);
        $this->alert('Ampere ready for work');
    }

    /**
     * @param array $config
     * @return array
     */
    private function configureProject(array $config): array
    {
        $configPrefix = $this->ask('Enter name of config (skip to default)', $config['name']);
        $config['name'] = strtolower($configPrefix);
        $config['config']['views_folder'] = $config['name'];
        $config['config']['route_prefix'] = $config['name'];

        $configPrefix = $this->ask('Enter url prefix', $config['config']['url_prefix']);
        $config['config']['url_prefix'] = preg_replace('#[^a-z/-]#', '', strtolower($configPrefix));

        $configPrefix = $this->ask('Enter db tables prefix (skip to empty prefix)');
        $value = trim(preg_replace('/[^a-z_-]/', '', strtolower($configPrefix)));

        if (empty($value)) {
            $config['config']['db_prefix'] = null;
        } else {
            $value = preg_replace('/_$/','', $value);
            $config['config']['db_prefix'] = $value . '_';
        }

        $this->warn('Good. Now you need to define namespace of your controllers (skip to default).');
        $value = readline(" > \e[0;36;40m" . $this->getNamespace('') . "\e[0m");
        $value = array_filter(explode(' ', ucwords(implode(' ', preg_split('#/|\\\#', preg_replace('/[^a-z\\/\\\]/', '', $value))))));

        $config['config']['space'] = implode('/', $value);
        $config['config']['namespace'] = implode('\\', $value);

        $this->warn(PHP_EOL . 'Enter public assets path (skip to default).');
        $value = readline(" > \e[0;36;40m/public/vendor/\e[0m");
        $config['config']['public_vendor_name'] = 'vendor/' . strtolower(preg_replace('/[^a-z0-9-_]/', '', $value));

        echo PHP_EOL;

        return $config;
    }

    /**
     * @param string $name
     * @return string
     */
    private function getNamespace(string $name): string
    {
        return 'App\Http\Controllers\\' . $name;
    }

    /**
     * @param array $config
     * @return bool
     */
    private function publishConfig(array $config): bool
    {
        $configParams = $config['config'];
        if (!empty($configParams['space'])) {
            $configParams['space'] = '/' . $configParams['space'];
            $configParams['namespace'] = '\\' . $configParams['namespace'];
        }

        $this->stubBuilder->setStub('config')
            ->setTargetPath('config/ampere/' . $config['name'] . '.php')
            ->setParams($configParams);

        if ($this->stubBuilder->isFileExists()) {
            $this->error('Config file "config/ampere/' . $config['name'] . '.php" already exist.');
            $confirm = $this->confirm('Do your really want to rewrite this file?', false);

            if (!$confirm) {
                $this->error('Config file not created');
                return false;
            }
        }

        return $this->stubBuilder->create();
    }

    /**
     * @param array $config
     * @return bool
     */
    private function publishPublicAssets(array $config): bool
    {
        $assetsPath = ampere_path('public');
        $assetsTargetPath = public_path($config['config']['public_vendor_name']);

        return File::copyDirectory($assetsPath, $assetsTargetPath);
    }

    /**
     * @param array $config
     * @return bool
     */
    private function publishViews(array $config): bool
    {
        $viewsPath = ampere_path('templates/views');
        $viewsTargetPath = resource_path('views/' . $config['name']);

        return File::copyDirectory($viewsPath, $viewsTargetPath);
    }

    /**
     * @param array $config
     * @return bool
     */
    private function publishControllers(array $config): bool
    {
        $controllersPath = ampere_path('templates/controllers');

        $files = File::allFiles($controllersPath);

        foreach($files as $file) {
            $content = $file->getContents();

            $params = [
                'namespace' => implode('\\', array_filter([$this->controllersNamespace, $config['config']['namespace'], $file->getRelativePath()]))
            ];

            foreach($params as $field => $param) {
                $content = str_replace('{' . $field . '}', $param, $content);
            }

            $targetPathList = [
                $this->controllersLocation,
                str_replace('\\', '/', $config['config']['namespace']),
                $file->getRelativePath(),
                $file->getFilenameWithoutExtension()
            ];

            $targetPath = base_path(implode('/', array_filter($targetPathList)));

            $targetFolder = dirname($targetPath);

            if (!file_exists($targetFolder)) {
                File::makeDirectory($targetFolder, 0755, true);
            }

            if (!File::put($targetPath, $content)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $config
     * @return bool
     */
    private function publishMigrations(array $config): bool
    {
        $migrationPathName = '2019_06_06_000000_ampere_' . $config['name'];
        $migrationName = ucfirst($config['name']);

        $this->stubBuilder->setStub('migration')
            ->setTargetPath('database/migrations/' . $migrationPathName . '.php')
            ->setParams(['name' => $migrationName, 'db_prefix' => $config['config']['db_prefix']]);

        if ($this->stubBuilder->isFileExists()) {
            $this->error('Migration "' . $migrationPathName . '" already exist.');
            $confirm = $this->confirm('Do your really want to rewrite this file?', false);

            if (!$confirm) {
                $this->error('Migration file not created');
                return false;
            }
        }

        return $this->stubBuilder->create();
    }

    /**
     * @param array $config
     * @return bool
     */
    private function publishResources(array $config): bool
    {
        $resourcesPath = ampere_path('templates/resources');
        $resourcesTargetPath = resource_path('ampere/' . $config['name']);

        $targetFolder = dirname($resourcesTargetPath);

        if (!file_exists($targetFolder)) {
            File::makeDirectory($targetFolder, 0755, true);
        }

        return File::copyDirectory($resourcesPath, $resourcesTargetPath);
    }
}
