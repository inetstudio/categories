<?php

namespace InetStudio\CategoriesPackage\Categories\Providers;

use Collective\Html\FormBuilder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Загрузка сервиса.
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerFormComponents();
    }

    /**
     * Регистрация команд.
     */
    protected function registerConsoleCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands(
            [
                'InetStudio\CategoriesPackage\Categories\Console\Commands\SetupCommand',
                'InetStudio\CategoriesPackage\Categories\Console\Commands\CreateFoldersCommand',
            ]
        );
    }

    /**
     * Регистрация ресурсов.
     */
    protected function registerPublishes(): void
    {
        $this->publishes(
            [
                __DIR__.'/../../config/categories.php' => config_path('categories.php'),
            ],
            'config'
        );

        $this->mergeConfigFrom(__DIR__.'/../../config/filesystems.php', 'filesystems.disks');

        if (! $this->app->runningInConsole()) {
            return;
        }

        if (Schema::hasTable('categories')) {
            return;
        }

        $timestamp = date('Y_m_d_His', time());
        $this->publishes(
            [
                __DIR__.'/../../database/migrations/create_categories_tables.php.stub' => database_path(
                    'migrations/'.$timestamp.'_create_categories_tables.php'
                ),
            ],
            'migrations'
        );
    }

    /**
     * Регистрация путей.
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    /**
     * Регистрация представлений.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'admin.module.categories');
    }

    /**
     * Регистрация компонентов форм.
     */
    protected function registerFormComponents()
    {
        FormBuilder::component(
            'categories',
            'admin.module.categories::back.forms.fields.categories',
            ['name' => null, 'value' => null, 'attributes' => null]
        );
    }
}
