<?php

namespace InetStudio\Categories\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class CategoriesBindingsServiceProvider.
 */
class CategoriesBindingsServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public $bindings = [];

    /**
     * CategoriesBindingsServiceProvider constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->bindings = getPackageBindings(__DIR__.'/../Contracts');
    }

    /**
     * Получить сервисы от провайдера.
     *
     * @return array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
