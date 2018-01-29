<?php

Route::group([
    'namespace' => 'InetStudio\Categories\Http\Controllers\Back',
    'middleware' => ['web', 'back.auth'],
    'prefix' => 'back'
], function () {
    Route::resource('categories', 'CategoriesController', ['except' => [
        'show',
    ], 'as' => 'back']);

    Route::post('categories/move', 'CategoriesUtilityController@move')->name('back.categories.move');
    Route::post('categories/slug', 'CategoriesUtilityController@getSlug')->name('back.categories.getSlug');
    Route::post('categories/suggestions', 'CategoriesUtilityController@getSuggestions')->name('back.categories.getSuggestions');
});
