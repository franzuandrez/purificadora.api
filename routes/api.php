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

Route::group(['middleware' => ['auth:api']], function () {

    Route::get('/users', 'UsersController@index');

    Route::get('/users/{id}', 'UsersController@show');

    Route::patch('/users/{id}', 'UsersController@update');


    Route::post('/logout', 'UsersController@logout');


    Route::post('/register', 'UsersController@register');
    Route::get('customers', 'CustomerController@index')->name('customer.index');

    Route::get('customer/{id}', 'CustomerController@show')->name('customer.show');
    Route::post('customer', 'CustomerController@store')->name('customer.store');


    Route::get('wallets', 'CustomerWalletController@index')->name('wallets');

    Route::post('wallets', 'CustomerWalletController@store')->name('wallets.store');


    Route::post('wallets/associate', 'CustomerWalletController@associate');

    Route::patch('wallets/{id}', 'CustomerWalletController@update')->name('wallets.update');

    Route::get('wallets/{id}', 'CustomerWalletController@show')->name('wallets.show');

    Route::get('all_wallets', 'CustomerWalletController@wallets');


    Route::get('products', 'ProductController@index')->name('products');
    Route::post('products', 'ProductController@store')->name('products.store');
    Route::get('products/{id}', 'ProductController@show')->name('products.show');


    Route::get('visit_reason', 'VisitReasonController@index');
    Route::post('visit_reason', 'VisitReasonController@store');
    Route::patch('visit_reason/{id}', 'VisitReasonController@update');
    Route::get('visit_reason/{id}', 'VisitReasonController@show');


    Route::post('sales', 'SalesController@store')->name('sales.store');
    Route::get('sales', 'SalesController@index')->name('sales');
    Route::get('sales/{id}', 'SalesController@show')->name('sales.show');

    Route::get('visits', 'VisitsController@index')->name('visits');
    Route::post('visits', 'VisitsController@store')->name('visits.store');
    Route::get('visits/{id}', 'VisitsController@show')->name('visits.show');


    Route::get('summary', 'SummaryController@index');
    Route::get('inventory', 'InventoryController@index');


});

Route::post('/login', 'UsersController@login');
Route::get('login', function () {
    return response()->json(
        [
            'success' => false,
            'message' => 'Usuario no logeado'
        ], 401
    );
})->name('login');
