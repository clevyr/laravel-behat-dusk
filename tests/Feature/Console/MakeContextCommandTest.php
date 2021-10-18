<?php

namespace Clevyr\LaravelBehatDusk\Tests\Feature\Console;

use Clevyr\LaravelBehatDusk\Console\MakeContextCommand;
use Clevyr\LaravelBehatDusk\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class MakeContextCommandTest extends TestCase
{
    /**
     * @var string $behat_config_path
     */
    private string $behat_config_path;

    /**
     * @var string $test_context_path
     */
    private string $test_context_path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->behat_config_path = base_path('behat.yml');
        $this->test_context_path = base_path('features/bootstrap/TestContext.php');

        $this->cleanEnvironment();
    }

    /**
     * Tests the a new contet class is created
     *
     * @test
     */
    public function it_creates_a_new_context_class()
    {
        $this->assertFalse(File::exists($this->test_context_path));

        Artisan::call(MakeContextCommand::class, [
            'name' => 'TestContext'
        ]);

        $this->assertTrue(File::exists($this->test_context_path));
        $this->assertTrue(File::exists($this->behat_config_path));

        $parsed_config = Yaml::parseFile($this->behat_config_path);

        $this->assertContains('TestContext', $parsed_config['default']['suites']['default']['contexts']);

        $expected_contents = <<<CLASS
        <?php

        use Clevyr\LaravelBehatDusk\BehatDuskTestCase;
        use Clevyr\LaravelBehatDusk\Traits\RefreshScenario;
        use Laravel\Dusk\Browser;

        class TestContext extends BehatDuskTestCase
        {
            /**
             * Initializes context.
             */
            public function __construct()
            {
                parent::__construct();
            }
        }

        CLASS;

        $this->assertEquals($expected_contents, file_get_contents($this->test_context_path));
    }

    /**
     * Tests that duplicate entries are not created
     *
     * @test
     */
    public function it_does_not_create_duplicate_context_entry()
    {
        Artisan::call(MakeContextCommand::class, [
            'name' => 'TestContext'
        ]);

        Artisan::call(MakeContextCommand::class, [
            'name' => 'TestContext'
        ]);

        $parsed_config = Yaml::parseFile($this->behat_config_path);
        $contexts = $parsed_config['default']['suites']['default']['contexts'];

        // Checks for duplicates
        $this->assertTrue(count($contexts) === count(array_flip($contexts)));
    }

    /**
     * Tests profile is injected inot the behat yaml file
     *
     * @test
     */
    public function profile_is_injected_into_yml()
    {
        Artisan::call(MakeContextCommand::class, [
            'name' => 'TestContext',
            '--profile' => 'test'
        ]);

        $parsed_config = Yaml::parseFile($this->behat_config_path);

        $this->assertArrayHasKey('test', $parsed_config);
        $this->assertContains('TestContext', $parsed_config['test']['suites']['default']['contexts']);
    }

    /**
     * Tests suite is injected into the behat yaml file
     *
     * @test
     */
    public function suite_is_injected_into_yml()
    {
        Artisan::call(MakeContextCommand::class, [
            'name' => 'TestContext',
            '--suite' => 'test'
        ]);

        $parsed_config = Yaml::parseFile($this->behat_config_path);
        $this->assertArrayHasKey('test', $parsed_config['default']['suites']);
        $this->assertContains('TestContext', $parsed_config['default']['suites']['test']['contexts']);
    }

    /**
     * Resets the file structure
     * @return void
     */
    private function cleanEnvironment() : void
    {
        if (File::exists($this->test_context_path)) {
            unlink($this->test_context_path);
        }

        File::put($this->behat_config_path, Yaml::dump([
            'default' => [
                'suites' => [
                    'default' => [
                        'contexts' => []
                    ]
                ]
            ]
        ], 8, 2));
    }
}
