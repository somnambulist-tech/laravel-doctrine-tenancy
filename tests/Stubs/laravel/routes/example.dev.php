<?php declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/routes', function () {
    return view('route_test');
});

$settings = [
    'as'         => 'admin.',
    'prefix'     => 'admin/{tenant_owner_id}/on/{tenant_creator_id}/this',
    'namespace'  => 'Somnambulist\Tenancy\Tests\Stubs\Controllers\Admin',
];
Route::group($settings, function () {

    Route::group(['as' => 'media.', 'prefix' => 'media'], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'MediaController@indexAction']);
        Route::get('create', ['as' => 'create', 'uses' => 'MediaController@createAction']);
        Route::post('store', ['as' => 'store', 'uses' => 'MediaController@storeAction']);
        Route::get('{id}/edit', ['as' => 'edit', 'uses' => 'MediaController@editAction']);
        Route::put('{id}/update', ['as' => 'update', 'uses' => 'MediaController@updateAction']);
        Route::delete('{id}/destroy', ['as' => 'destroy', 'uses' => 'MediaController@destroyAction']);
    });
});
