<?php

namespace Geekstek\FilamentTree;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTreeServiceProvider extends PackageServiceProvider
{
    public static string $name = 'geekstek-filament-tree';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews(static::$name);
    }
}
