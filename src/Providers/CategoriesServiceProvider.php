<?php

namespace InetStudio\Categories\Providers;

use Illuminate\Support\ServiceProvider;
use InetStudio\Categories\Events\ModifyCategoryEvent;
use InetStudio\Categories\Console\Commands\SetupCommand;
use InetStudio\Categories\Services\Front\CategoriesService;
use InetStudio\Categories\Console\Commands\CreateFoldersCommand;
use InetStudio\Categories\Http\Requests\Back\SaveCategoryRequest;
use InetStudio\Categories\Http\Controllers\Back\CategoriesController;
use InetStudio\Categories\Contracts\Events\ModifyCategoryEventContract;
use InetStudio\Categories\Transformers\Front\CategoriesSiteMapTransformer;
use InetStudio\Categories\Http\Controllers\Back\CategoriesUtilityController;
use InetStudio\Categories\Contracts\Services\Front\CategoriesServiceContract;
use InetStudio\Categories\Contracts\Http\Requests\Back\SaveCategoryRequestContract;
use InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesControllerContract;
use InetStudio\Categories\Contracts\Transformers\Front\CategoriesSiteMapTransformerContract;
use InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesUtilityControllerContract;

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
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
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
                SetupCommand::class,
                CreateFoldersCommand::class,
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
            __DIR__.'/../../public' => public_path(),
        ], 'public');

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
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    public function registerBindings(): void
    {
        // Events
        $this->app->bind(ModifyCategoryEventContract::class, ModifyCategoryEvent::class);

        // Controllers
        $this->app->bind(CategoriesControllerContract::class, CategoriesController::class);
        $this->app->bind(CategoriesUtilityControllerContract::class, CategoriesUtilityController::class);

        // Requests
        $this->app->bind(SaveCategoryRequestContract::class, SaveCategoryRequest::class);

        // Services
        $this->app->bind(CategoriesServiceContract::class, CategoriesService::class);

        // Transformers
        $this->app->bind(CategoriesSiteMapTransformerContract::class, CategoriesSiteMapTransformer::class);
    }
}
