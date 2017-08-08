<?php

namespace InetStudio\Categories;

use Illuminate\Support\ServiceProvider;

class CategoriesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/admin', 'admin.module.categories');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->mergeConfigFrom(
            __DIR__.'/../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateCategoriesTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../database/migrations/create_categories_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_categories_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register('Phoenix\EloquentMeta\ServiceProvider');
        $this->app->register('Cviebrock\EloquentSluggable\ServiceProvider');
        $this->app->register('Spatie\MediaLibrary\MediaLibraryServiceProvider');
    }
}
