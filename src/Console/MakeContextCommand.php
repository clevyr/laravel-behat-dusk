<?php

namespace Clevyr\LaravelBehatDusk\Console;

use Exception;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class MakeContextCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'lbd:make
                            {name : The name of the class}
                            {--profile=default : Create under the profile in the Behat Config}
                            {--suite=default : Create under the suite in the Behat Config}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Laravel Behat context class and adds it to the default suite context';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Behat Context';

    /**
     * @var string $suite
     */
    protected $suite = 'default';

    /**
     * @var string[] $file_default
     */
    protected $file_default = [
        'default' => [
            'suites' => [
                'default' => [
                    'contexts' => ['FeatureContext']
                ]
            ]
        ]
    ];

    /**
     * @return bool|void|null
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function handle()
    {
        $generator = parent::handle();

        // If generator successfully runs it will return null
        // We only want to append to the behat config if the generator runs successfully
        if (is_null($generator)) {
            $this->appendToBehatConfig();
        }
    }

    /**
     * Appends
     * @throws Exception
     */
    private function appendToBehatConfig() : void
    {
        $context_file_name = $this->getNameInput();

        $behat_config = base_path('behat.yml');

        if (!file_exists($behat_config)) {
            throw new Exception('Please run the install command before creating a Context File');
        }

        $config = Yaml::parseFile($behat_config);

        if (!$this->contextExists($config, $context_file_name)) {
            $this->setContextToBehatConfig($config, $context_file_name, $behat_config);
        }
    }

    /**
     * Sets the Context File name to the Behat config
     *
     * @param array $config
     * @param string $context_file_name
     * @param string $path
     */
    private function setContextToBehatConfig(array $config, string $context_file_name, string $path) : void
    {
        $profile = $this->option('profile');
        $suite = $this->option('suite');

        $config[$profile]['suites'][$suite]['contexts'][] = $context_file_name;

        $this->files->replace($path, Yaml::dump($config, 8, 2));
    }

    /**
     * Checks if the Context already exists
     *
     * @param array $config
     * @param string $context_file_name
     * @return bool
     */
    private function contextExists(array $config, string $context_file_name) : bool
    {
        return in_array($context_file_name, $config['default']['suites']['default']['contexts']);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/test.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel->basePath().'/features/bootstrap/'.$name.'.php';
    }
}
