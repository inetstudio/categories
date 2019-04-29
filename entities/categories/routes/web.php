<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'InetStudio\CategoriesPackage\Categories\Contracts\Http\Controllers\Back',
        'middleware' => ['web', 'back.auth'],
        'prefix' => 'back',
    ],
    function () {
        Route::post('categories/move', 'UtilityControllerContract@move')
            ->name('back.categories.move');

        Route::post('categories/slug', 'UtilityControllerContract@getSlug')
            ->name('back.categories.getSlug');

        Route::post('categories/suggestions', 'UtilityControllerContract@getSuggestions')
            ->name('back.categories.getSuggestions');

        Route::resource(
            'categories', 'ResourceControllerContract',
            [
                'except' => [
                    'show',
                ],
                'as' => 'back',
            ]
        );
    }
);
