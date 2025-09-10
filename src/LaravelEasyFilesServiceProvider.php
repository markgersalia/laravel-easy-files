<?php

namespace Markgersalia\LaravelEasyFiles;

use Markgersalia\LaravelEasyFiles\Commands\LaravelEasyFilesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelEasyFilesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-easy-files')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_files_table')
            ->hasCommand(LaravelEasyFilesCommand::class);
    }

    public function boot()
    {
        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/easy-files.php' => config_path('easy-files.php'),
        ], 'config');

    }
}
