<?php

namespace Canvas\Tests\Unit\Console;

use Canvas\Tests\TestCase;

class InstallCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_install_assets_and_configuration()
    {
        $this->artisan('canvas:install')
            ->expectsOutput('Publishing the assets...')
            ->expectsOutput('Publishing the configuration file...')
            ->expectsOutput('Running the database migrations...')
            ->expectsQuestion('Do you want to generate a default setup for the frontend? (controller, routes, views)', 'Yes')
            ->expectsOutput('Scaffolding a default controller with blog views and routes...')
            ->expectsOutput('Canvas is installed and ready to use. Enjoy!')
            ->assertExitCode(0);
        $this->assertFileExists(config_path('canvas.php'));
        $this->assertDirectoryExists(public_path('vendor/canvas'));
    }
}
