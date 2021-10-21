<?php

namespace Clevyr\LaravelBehatDusk\Console;

use Laravel\Dusk\Console\DuskCommand;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class LaravelBehatDuskCommand extends DuskCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lbd
                           {--browse : Open a browser instead of using headless mode}
                           {--without-tty : Disable output to TTY}
                           {--pest : Run the tests using Pest}';

    /**
     * Console command description
     *
     * @var string
     */
    protected $description = 'Runs the Behat tests for the application';

    /**
     * LaravelBehatCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->ignoreValidationErrors();
    }

    /**
     *
     */
    public function handle()
    {
        $options = collect($_SERVER['argv'])
            ->slice(2)
            ->diff(['--profile', '--scenario'])
            ->values()
            ->all();

        return $this->withDuskEnvironment(function () use ($options) {
            $process = (new Process(array_merge(
                $this->binary(),
                $options
            ), null, $this->env()))->setTimeout(null);

            try {
                $process->setTty(! $this->option('without-tty'));
            } catch (RuntimeException $e) {
                $this->output->writeln('Warning: '.$e->getMessage());
            }

            try {
                return $process->run(function ($type, $line) {
                    $this->output->write($line);
                });
            } catch (ProcessSignaledException $e) {
                if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                    throw $e;
                }
            }
        });
    }

    protected function binary()
    {
        return ['vendor/behat/behat/bin/behat'];
    }
}
