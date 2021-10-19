<?php

namespace Clevyr\LaravelBehatDusk\Tests\Feature\Console;

use Clevyr\LaravelBehatDusk\Console\InstallCommand;
use Clevyr\LaravelBehatDusk\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallCommandTest extends TestCase
{
    /**
     * @var string
     */
    public string $config_path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config_path = config_path('behat-dusk.php');

        $this->deleteFiles();
    }

    /**
     * Tests that the configuration file is published
     * @test test_config_is_published
     */
    public function config_is_published()
    {
        $this->assertFalse($this->configExists());

        Artisan::call(InstallCommand::class);

        $this->assertTrue($this->configExists());
    }

    /**
     * Resets file structure
     */
    private function deleteFiles()
    {
        if ($this->configExists()) {
            unlink($this->config_path);
        }
    }

    /**
     * Checks if the config file exists
     *
     * @return bool
     */
    private function configExists(): bool
    {
        return File::exists($this->config_path);
    }
}
