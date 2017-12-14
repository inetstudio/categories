<?php

Route::group(['namespace' => 'InetStudio\Categories\Http\Controllers\Back'], function () {
    Route::group(['middleware' => 'web', 'prefix' => 'back'], function () {
        Route::group(['middleware' => 'back.auth'], function () {
            Route::post('categories/move', 'CategoriesController@move')->name('back.categories.move');
            Route::post('categories/slug', 'CategoriesController@getSlug')->name('back.categories.getSlug');
            Route::post('categories/suggestions', 'CategoriesController@getSuggestions')->name('back.categories.getSuggestions');
            Route::resource('categories', 'CategoriesController', ['except' => [
                'show',
            ], 'as' => 'back']);
        });
    });
});
