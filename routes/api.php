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
Route::get('login', function () {
    return response()->json(
        [
            'success' => false,
            'message' => 'Usuario no logeado'
        ], 401
    );
})->name('login');


Route::get('/users', 'UsersController@index')
    ->middleware('auth:api');
Route::get('/users/{id}', 'UsersController@show')
    ->middleware('auth:api');
Route::patch('/users/{id}', 'UsersController@update')
    ->middleware('auth:api');
Route::get('/logout', 'UsersController@logout')
    ->middleware('auth:api');
Route::post('/login', 'UsersController@login');
Route::post('/register', 'UsersController@register');


Route::get('customers', 'CustomerController@index')->name('customer.index')
    ->middleware('auth:api');
Route::get('customer/{id}', 'CustomerController@show')->name('customer.show');
Route::post('customer', 'CustomerController@store')->name('customer.store')
    ->middleware('auth:api');


Route::get('wallets', 'CustomerWalletController@index')->name('wallets')
    ->middleware('auth:api');
Route::post('wallets', 'CustomerWalletController@store')->name('wallets.store')
    ->middleware('auth:api');
Route::patch('wallets/{id}', 'CustomerWalletController@update')->name('wallets.update')
    ->middleware('auth:api');
Route::get('wallets/{id}', 'CustomerWalletController@show')->name('wallets.show')
    ->middleware('auth:api');
Route::get('all_wallets', 'CustomerWalletController@wallets');


Route::get('products', 'ProductController@index')->name('products');
Route::post('products', 'ProductController@store')->name('products.store');
Route::patch('products/{id}', 'ProductController@update')->name('products.update');
Route::get('products/{id}', 'ProductController@show')->name('products.show');


Route::get('visit_reason', 'VisitReasonController@index');
Route::post('visit_reason', 'VisitReasonController@store');
Route::patch('visit_reason/{id}', 'VisitReasonController@update');
Route::get('visit_reason/{id}', 'VisitReasonController@show');


Route::post('sales', 'SalesController@store')->name('sales.store')->middleware('auth:api');
Route::get('sales', 'SalesController@index')->name('sales')->middleware('auth:api');

Route::get('visits', 'VisitsController@index')->name('visits');
Route::post('visits', 'VisitsController@store')->name('visits.store')->middleware('auth:api');
Route::get('visits/{id}', 'VisitsController@show')->name('visits.show');


Route::get('summary', 'SummaryController@index');
Route::get('inventory', 'InventoryController@index');

