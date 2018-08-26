<?php

namespace InetStudio\Categories\Providers;

use Collective\Html\FormBuilder;
use Illuminate\Support\ServiceProvider;

/**
 * Class CategoriesServiceProvider.
 */
class CategoriesServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
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
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'InetStudio\Categories\Console\Commands\SetupCommand',
                'InetStudio\Categories\Console\Commands\CreateFoldersCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/categories.php' => config_path('categories.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateCategoriesTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_categories_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_categories_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация путей.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    /**
     * Регистрация представлений.
     *
     * @return void
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'admin.module.categories');
    }

    /**
     * Регистрация компонентов форм.
     *
     * @return void
     */
    protected function registerFormComponents()
    {
        FormBuilder::component('categories', 'admin.module.categories::back.forms.fields.categories', ['name' => null, 'value' => null, 'attributes' => null]);
    }
}
