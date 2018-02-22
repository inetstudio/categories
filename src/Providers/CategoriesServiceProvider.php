<?php

namespace InetStudio\Categories\Providers;

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
        // Controllers
        $this->app->bind('InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesControllerContract', 'InetStudio\Categories\Http\Controllers\Back\CategoriesController');
        $this->app->bind('InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesUtilityControllerContract', 'InetStudio\Categories\Http\Controllers\Back\CategoriesUtilityController');

        // Events
        $this->app->bind('InetStudio\Categories\Contracts\Events\Back\ModifyCategoryEventContract', 'InetStudio\Categories\Events\Back\ModifyCategoryEvent');

        // Models
        $this->app->bind('InetStudio\Categories\Contracts\Models\CategoryModelContract', 'InetStudio\Categories\Models\CategoryModel');

        // Repositories
        $this->app->bind('InetStudio\Categories\Contracts\Repositories\Back\CategoriesRepositoryContract', 'InetStudio\Categories\Repositories\Back\CategoriesRepository');

        // Requests
        $this->app->bind('InetStudio\Categories\Contracts\Http\Requests\Back\SaveCategoryRequestContract', 'InetStudio\Categories\Http\Requests\Back\SaveCategoryRequest');

        // Responses
        $this->app->bind('InetStudio\Categories\Contracts\Http\Responses\Back\Categories\DestroyResponseContract', 'InetStudio\Categories\Http\Responses\Back\Categories\DestroyResponse');
        $this->app->bind('InetStudio\Categories\Contracts\Http\Responses\Back\Categories\FormResponseContract', 'InetStudio\Categories\Http\Responses\Back\Categories\FormResponse');
        $this->app->bind('InetStudio\Categories\Contracts\Http\Responses\Back\Categories\IndexResponseContract', 'InetStudio\Categories\Http\Responses\Back\Categories\IndexResponse');
        $this->app->bind('InetStudio\Categories\Contracts\Http\Responses\Back\Categories\SaveResponseContract', 'InetStudio\Categories\Http\Responses\Back\Categories\SaveResponse');
        $this->app->bind('InetStudio\Categories\Contracts\Http\Responses\Back\Utility\MoveResponseContract', 'InetStudio\Categories\Http\Responses\Back\Utility\MoveResponse');
        $this->app->bind('InetStudio\Categories\Contracts\Http\Responses\Back\Utility\SlugResponseContract', 'InetStudio\Categories\Http\Responses\Back\Utility\SlugResponse');
        $this->app->bind('InetStudio\Categories\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract', 'InetStudio\Categories\Http\Responses\Back\Utility\SuggestionsResponse');

        // Services
        $this->app->bind('InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract', 'InetStudio\Categories\Services\Back\CategoriesService');
        $this->app->bind('InetStudio\Categories\Contracts\Services\Front\CategoriesServiceContract', 'InetStudio\Categories\Services\Front\CategoriesService');

        // Transformers
        $this->app->bind('InetStudio\Categories\Contracts\Transformers\Back\SuggestionTransformerContract', 'InetStudio\Categories\Transformers\Back\SuggestionTransformer');
        $this->app->bind('InetStudio\Categories\Contracts\Transformers\Back\TreeTransformerContract', 'InetStudio\Categories\Transformers\Back\TreeTransformer');
        $this->app->bind('InetStudio\Categories\Contracts\Transformers\Front\CategoriesSiteMapTransformerContract', 'InetStudio\Categories\Transformers\Front\CategoriesSiteMapTransformer');
    }
}
