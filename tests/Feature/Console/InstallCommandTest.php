<?php

namespace Clevyr\LaravelBehatDusk\Tests\Feature\Console;

use Clevyr\LaravelBehatDusk\Console\InstallCommand;
use Clevyr\LaravelBehatDusk\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallCommandTest extends TestCase
{
    /**
     * @var array
     */
    private array $files;

    /**
     * @var array
     */
    private array $folders;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = [
            config_path('behat-dusk.php'),
            base_path('behat.yml'),
            base_path('tests/DuskTestCase.php'),
            base_path('features/bootstrap/FeatureContext.php'),
        ];

        $this->folders = [
            base_path('features'),
            base_path('tests/Browser'),
        ];

        $this->deleteFiles();
    }

    /**
     * Tests that the files are published
     *
     * @test
     */
    public function files_are_created()
    {
        foreach ($this->files as $file) {
            $this->assertFileDoesNotExist($file);
        }

        foreach ($this->folders as $folder) {
            $this->assertDirectoryDoesNotExist($folder);
        }

        Artisan::call(InstallCommand::class);

        foreach ($this->files as $file) {
            $this->assertFileExists($file);
        }

        foreach ($this->folders as $folder) {
            $this->assertDirectoryExists($folder);
        }
    }

    /**
     * Resets file structure
     */
    private function deleteFiles()
    {
        File::delete($this->files);

        foreach ($this->folders as $folder) {
            File::deleteDirectory($folder);
        }
    }
}
