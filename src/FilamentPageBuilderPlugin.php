<?php

namespace TomatoPHP\FilamentPageBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentPageBuilderPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-page-builder';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): self
    {
        return new FilamentPageBuilderPlugin;
    }
}
