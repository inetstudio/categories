<?php

use InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesControllerContract;
use InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesUtilityControllerContract;

Route::group([
    'middleware' => ['web', 'back.auth'],
    'prefix' => 'back'
], function () {
    Route::post('categories/move', CategoriesUtilityControllerContract::class.'@move')->name('back.categories.move');
    Route::post('categories/slug', CategoriesUtilityControllerContract::class.'@getSlug')->name('back.categories.getSlug');
    Route::post('categories/suggestions', CategoriesUtilityControllerContract::class.'@getSuggestions')->name('back.categories.getSuggestions');

    Route::resource('categories', CategoriesControllerContract::class, ['except' => [
        'show',
    ], 'as' => 'back']);
});
