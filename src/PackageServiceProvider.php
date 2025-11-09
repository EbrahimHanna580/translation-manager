<?php
namespace EbrahimHanna\TranslationManager;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__.'/config/laravel-translations.php' => config_path('laravel-translations.php'),
        ], 'config');
    }

    public function register()
    {
        // Merge the package's config with the application's config
        $this->mergeConfigFrom(
            __DIR__.'/config/laravel-translations.php', 'laravel-translations'
        );
    }
}
