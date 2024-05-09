<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->group(function() {

    Route::prefix('auth')->group(function() {
        Route::post('/register', 'AuthController@register')->name('create-account');
        Route::post('/login', 'AuthController@login')->name('login');
    });

    Route::get('categories/all', 'CategoriesController@index')->name('get-categories');
    Route::get('locations/all', 'LocationController@index')->name('get-locations');
    Route::get('menu/all', 'MenuController@index')->name('get-menu');
    Route::get('menu/admin/all', 'MenuController@indexAdmin')->name('get-menu-admin');
    Route::get('menu/protein', 'MenuController@protein')->name('get-protein');
    Route::post('protein/add', 'MenuController@proteinAdd')->name('add-protein');
    Route::post('create-order', 'OrderController@create')->name('create-order');
    Route::post('webhook', 'OrderController@create')->name('webhook');

    Route::post('complete-order', 'OrderController@complete')->name('complete-order');
    Route::post('order/state', 'OrderController@state')->name('state');
    Route::post('confirm_order', 'OrderController@confirm')->name('confirm');
    Route::delete('protein/delete/{id}', 'MenuController@proteinDelete')->name('delete-protein');
    Route::get('totalsales', 'MenuController@totalSales')->name('total-sales');
    Route::get('pendingorders', 'MenuController@pendingOrders')->name('pending-orders');
    Route::get('proccessed', 'MenuController@proccessedOrders')->name('proccessed-orders');

    Route::prefix('orders')->group(function() {
        Route::get('/all', 'OrderController@index')->name('get-orders');
        Route::get('/{id}', 'OrderController@order')->name('get-order');
    });

    Route::group(['middleware' => ['jwt.auth', 'admin']], function() {
        Route::prefix('users')->group(function() {
            Route::get('/all', 'AuthController@getUsers')->name('get-users');
        });
        

        
        
        Route::prefix('categories')->group(function() {
            Route::post('/add', 'CategoriesController@create')->name('add-category');
            Route::post('/{id}/update', 'CategoriesController@update')->name('update-category');
            Route::delete('/{id}/delete', 'CategoriesController@destroy')->name('drop-category');
        });

        Route::prefix('locations')->group(function() {
           
            Route::post('/add', 'LocationController@store')->name('add-location');
            Route::delete('/{id}/delete', 'LocationController@destroy')->name('drop-location');
        });
        

        Route::prefix('menu')->group(function() {
            Route::post('/add', 'MenuController@create')->name('add-menu');
            Route::post('/update/{id}', 'MenuController@update')->name('update-menu');
            Route::delete('/{id}/delete', 'MenuController@destroy')->name('drop-menu');
             Route::get('{id}', 'MenuController@show')->name('get-menu');
        });
    });

});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
