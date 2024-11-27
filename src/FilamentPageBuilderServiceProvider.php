<?php

namespace TomatoPHP\FilamentPageBuilder;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use TomatoPHP\FilamentCms\Livewire\BuilderToolbar;


class FilamentPageBuilderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //Register generate command
        $this->commands([
           \TomatoPHP\FilamentPageBuilder\Console\FilamentPageBuilderInstall::class,
        ]);

        //Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/filament-page-builder.php', 'filament-page-builder');

        //Publish Config
        $this->publishes([
           __DIR__.'/../config/filament-page-builder.php' => config_path('filament-page-builder.php'),
        ], 'filament-page-builder-config');

        //Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        //Publish Migrations
        $this->publishes([
           __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filament-page-builder-migrations');
        //Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-page-builder');

        //Publish Views
        $this->publishes([
           __DIR__.'/../resources/views' => resource_path('views/vendor/filament-page-builder'),
        ], 'filament-page-builder-views');

        //Register Langs
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament-page-builder');

        //Publish Lang
        $this->publishes([
           __DIR__.'/../resources/lang' => base_path('lang/vendor/filament-page-builder'),
        ], 'filament-page-builder-lang');

        //Register Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Livewire::component('builder-toolbar',BuilderToolbar::class);

    }

    public function boot(): void
    {
        //you boot methods here
    }
}
