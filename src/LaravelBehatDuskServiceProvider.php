<?php

namespace Clevyr\LaravelBehatDusk;

use Clevyr\LaravelBehatDusk\Console\InstallCommand;
use Clevyr\LaravelBehatDusk\Console\LaravelBehatDuskCommand;
use Clevyr\LaravelBehatDusk\Console\MakeContextCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelBehatDuskServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-behat-dusk')
            ->hasCommands([
                LaravelBehatDuskCommand::class,
                MakeContextCommand::class,
                InstallCommand::class,
            ]);
    }
}
