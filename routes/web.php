<?php

Route::group([
    'namespace' => 'InetStudio\Categories\Contracts\Http\Controllers\Back',
    'middleware' => ['web', 'back.auth'],
    'prefix' => 'back',
], function () {
    Route::post('categories/move', 'CategoriesUtilityControllerContract@move')->name('back.categories.move');
    Route::post('categories/slug', 'CategoriesUtilityControllerContract@getSlug')->name('back.categories.getSlug');
    Route::post('categories/suggestions', 'CategoriesUtilityControllerContract@getSuggestions')->name('back.categories.getSuggestions');

    Route::resource('categories', 'CategoriesControllerContract', ['except' => [
        'show',
    ], 'as' => 'back']);
});
