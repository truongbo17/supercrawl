<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array)config('backpack.base.web_middleware', 'web'),
        (array)config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('url', 'UrlCrudController');
    Route::crud('crawl-url', 'CrawlUrlCrudController');
    Route::post('url/{id}/update_status_url', 'UrlCrudController@updateStatus');
    Route::get('url/{id}/export_url', 'UrlCrudController@exportUrl');
    Route::post('url/import_url', 'UrlCrudController@importUrl');
    Route::crud('data', 'DataCrudController');
}); // this should be the absolute last line of this file