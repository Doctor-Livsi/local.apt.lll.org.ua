<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $basePath = database_path('migrations');

        $directories = collect((new Finder())->in($basePath)->directories())
            ->map(fn ($dir) => $dir->getRealPath())
            ->push($basePath)
            ->toArray();

        $this->loadMigrationsFrom($directories);
    }
}
