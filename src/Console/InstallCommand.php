<?php

namespace Clevyr\LaravelBehatDusk\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class InstallCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'lbd:install';

    private $behat_config_path;

    /**
     * @var string
     */
    protected $description = 'Install the Laravel Behat Dusk package';

    public function handle()
    {
        $this->behat_config_path = base_path('behat.yml');

        $this->info('Installing Laravel Behat Dusk Package...');
        $this->installConfig();

        if (! File::exists($this->behat_config_path)) {
            $this->info('Installing Behat...');
            $this->installBehat();
            $this->info('Installing Laravel Dusk...');
            $this->installLaravelDusk();
        } else {
            $this->info('Skipping Behat Initialization as it is already installed');
        }
    }

    /**
     * Copies the config file
     *
     * @return void
     */
    private function installConfig(): void
    {
        if (! $this->configExists()) {
            $this->publishConfiguration();
            $this->info('Configuration published');
        }
    }

    /**
     * Installs Behat
     *
     * @return void
     */
    private function installBehat(): void
    {
        $features_path = base_path('features');
        $bootstrap_path = base_path('features/bootstrap');

        if (! File::exists($this->behat_config_path)) {
            File::put($this->behat_config_path, Yaml::dump(Helpers::defaultBehatConfig(), 8, 2));
        }

        if (! File::exists($features_path)) {
            File::makeDirectory($bootstrap_path, 0755, true);
        }

        if (! File::exists(base_path('features/bootstrap/FeatureContext'))) {
            $this->call(MakeContextCommand::class, ['name' => 'FeatureContext']);
        }
    }

    /**
     * Installs Laravel Dusk
     */
    private function installLaravelDusk(): void
    {
        $this->call(\Laravel\Dusk\Console\InstallCommand::class);
    }

    /**
     * Checks if the config exists
     *
     * @return bool
     */
    private function configExists(): bool
    {
        return File::exists(config_path('behat-dusk.php'));
    }

    /**
     * Publishes the configuration file
     *
     * @return void
     */
    private function publishConfiguration(): void
    {
        $params = [
            '--provider' => 'Clevyr\LaravelBehatDusk\LaravelBehatDuskServiceProvider',
            '--tag' => 'behat-dusk-config',
        ];

        $this->call('vendor:publish', $params);
    }
}
