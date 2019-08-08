<?php

namespace InetStudio\CategoriesPackage\Categories\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class BindingsServiceProvider.
 */
class BindingsServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * @var array
     */
    public $bindings = [
        'InetStudio\CategoriesPackage\Categories\Contracts\Events\Back\ModifyItemEventContract' => 'InetStudio\CategoriesPackage\Categories\Events\Back\ModifyItemEvent',
        'InetStudio\CategoriesPackage\Categories\Contracts\Http\Controllers\Back\ResourceControllerContract' => 'InetStudio\CategoriesPackage\Categories\Http\Controllers\Back\ResourceController',
        'InetStudio\CategoriesPackage\Categories\Contracts\Http\Controllers\Back\UtilityControllerContract' => 'InetStudio\CategoriesPackage\Categories\Http\Controllers\Back\UtilityController',
        'InetStudio\CategoriesPackage\Categories\Contracts\Http\Requests\Back\SaveItemRequestContract' => 'InetStudio\CategoriesPackage\Categories\Http\Requests\Back\SaveItemRequest',
        'InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Resource\DestroyResponseContract' => 'InetStudio\CategoriesPackage\Categories\Http\Responses\Back\Resource\DestroyResponse',
        'InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Resource\FormResponseContract' => 'InetStudio\CategoriesPackage\Categories\Http\Responses\Back\Resource\FormResponse',
        'InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Resource\IndexResponseContract' => 'InetStudio\CategoriesPackage\Categories\Http\Responses\Back\Resource\IndexResponse',
        'InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Resource\SaveResponseContract' => 'InetStudio\CategoriesPackage\Categories\Http\Responses\Back\Resource\SaveResponse',
        'InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Utility\MoveResponseContract' => 'InetStudio\CategoriesPackage\Categories\Http\Responses\Back\Utility\MoveResponse',
        'InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Utility\SlugResponseContract' => 'InetStudio\CategoriesPackage\Categories\Http\Responses\Back\Utility\SlugResponse',
        'InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract' => 'InetStudio\CategoriesPackage\Categories\Http\Responses\Back\Utility\SuggestionsResponse',
        'InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract' => 'InetStudio\CategoriesPackage\Categories\Models\CategoryModel',
        'InetStudio\CategoriesPackage\Categories\Contracts\Services\Back\ItemsServiceContract' => 'InetStudio\CategoriesPackage\Categories\Services\Back\ItemsService',
        'InetStudio\CategoriesPackage\Categories\Contracts\Services\Back\UtilityServiceContract' => 'InetStudio\CategoriesPackage\Categories\Services\Back\UtilityService',
        'InetStudio\CategoriesPackage\Categories\Contracts\Services\Front\FeedsServiceContract' => 'InetStudio\CategoriesPackage\Categories\Services\Front\FeedsService',
        'InetStudio\CategoriesPackage\Categories\Contracts\Services\Front\ItemsServiceContract' => 'InetStudio\CategoriesPackage\Categories\Services\Front\ItemsService',
        'InetStudio\CategoriesPackage\Categories\Contracts\Services\Front\SitemapServiceContract' => 'InetStudio\CategoriesPackage\Categories\Services\Front\SitemapService',
        'InetStudio\CategoriesPackage\Categories\Contracts\Transformers\Back\Utility\SuggestionTransformerContract' => 'InetStudio\CategoriesPackage\Categories\Transformers\Back\Utility\SuggestionTransformer',
        'InetStudio\CategoriesPackage\Categories\Contracts\Transformers\Back\TreeTransformerContract' => 'InetStudio\CategoriesPackage\Categories\Transformers\Back\TreeTransformer',
        'InetStudio\CategoriesPackage\Categories\Contracts\Transformers\Front\Sitemap\ItemTransformerContract' => 'InetStudio\CategoriesPackage\Categories\Transformers\Front\Sitemap\ItemTransformer',
    ];

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
