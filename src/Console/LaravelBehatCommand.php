<?php

namespace Clevyr\LaravelBehatDusk\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

class LaravelBehatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lbd';

    /**
     * @var string[]
     */
    protected $binary = ['vendor/behat/behat/bin/behat'];

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
     * @return void
     */
    public function handle(): void
    {
        $options = collect($_SERVER['argv'])
            ->slice(2)
            ->values()
            ->all();

        $process = (new Process(array_merge($this->binary, $options), null))
            ->setTimeout(null);

        try {
            $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            throw $e;
        }
    }
}
