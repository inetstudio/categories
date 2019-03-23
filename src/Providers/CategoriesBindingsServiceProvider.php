<?php

namespace InetStudio\Categories\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class CategoriesBindingsServiceProvider.
 */
class CategoriesBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Categories\Contracts\Events\Back\ModifyCategoryEventContract' => 'InetStudio\Categories\Events\Back\ModifyCategoryEvent',
        'InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesControllerContract' => 'InetStudio\Categories\Http\Controllers\Back\CategoriesController',
        'InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesUtilityControllerContract' => 'InetStudio\Categories\Http\Controllers\Back\CategoriesUtilityController',
        'InetStudio\Categories\Contracts\Http\Requests\Back\SaveCategoryRequestContract' => 'InetStudio\Categories\Http\Requests\Back\SaveCategoryRequest',
        'InetStudio\Categories\Contracts\Http\Responses\Back\Categories\DestroyResponseContract' => 'InetStudio\Categories\Http\Responses\Back\Categories\DestroyResponse',
        'InetStudio\Categories\Contracts\Http\Responses\Back\Categories\FormResponseContract' => 'InetStudio\Categories\Http\Responses\Back\Categories\FormResponse',
        'InetStudio\Categories\Contracts\Http\Responses\Back\Categories\IndexResponseContract' => 'InetStudio\Categories\Http\Responses\Back\Categories\IndexResponse',
        'InetStudio\Categories\Contracts\Http\Responses\Back\Categories\SaveResponseContract' => 'InetStudio\Categories\Http\Responses\Back\Categories\SaveResponse',
        'InetStudio\Categories\Contracts\Http\Responses\Back\Utility\MoveResponseContract' => 'InetStudio\Categories\Http\Responses\Back\Utility\MoveResponse',
        'InetStudio\Categories\Contracts\Http\Responses\Back\Utility\SlugResponseContract' => 'InetStudio\Categories\Http\Responses\Back\Utility\SlugResponse',
        'InetStudio\Categories\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract' => 'InetStudio\Categories\Http\Responses\Back\Utility\SuggestionsResponse',
        'InetStudio\Categories\Contracts\Models\CategoryModelContract' => 'InetStudio\Categories\Models\CategoryModel',
        'InetStudio\Categories\Contracts\Repositories\CategoriesRepositoryContract' => 'InetStudio\Categories\Repositories\CategoriesRepository',
        'InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract' => 'InetStudio\Categories\Services\Back\CategoriesService',
        'InetStudio\Categories\Contracts\Services\Front\CategoriesServiceContract' => 'InetStudio\Categories\Services\Front\CategoriesService',
        'InetStudio\Categories\Contracts\Transformers\Back\SuggestionTransformerContract' => 'InetStudio\Categories\Transformers\Back\SuggestionTransformer',
        'InetStudio\Categories\Contracts\Transformers\Back\TreeTransformerContract' => 'InetStudio\Categories\Transformers\Back\TreeTransformer',
        'InetStudio\Categories\Contracts\Transformers\Front\CategoriesSiteMapTransformerContract' => 'InetStudio\Categories\Transformers\Front\CategoriesSiteMapTransformer',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return  array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
