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
            'message' => 'User not logged'
        ]
    );
})->name('login');


Route::get('/users', 'UsersController@index');
Route::get('/users/{id}', 'UsersController@show');
Route::patch('/users/{id}', 'UsersController@update');
Route::post('/login', 'UsersController@login');
Route::post('/register', 'UsersController@register');


Route::get('/logout', 'UsersController@logout')
    ->middleware('auth:api');

Route::get('customers', 'CustomerController@index')->name('customer.index')
    ->middleware('auth:api');

Route::post('customer', 'CustomerController@store')
    ->name('customer.store');



Route::get('wallets', 'CustomerWalletController@index')
    ->name('wallets')
    ->middleware('auth:api');

Route::post('wallets', 'CustomerWalletController@store')
    ->name('wallets.store')
    ->middleware('auth:api');

Route::patch('wallets/{id}', 'CustomerWalletController@update')
    ->name('wallets.update')
    ->middleware('auth:api');

Route::get('wallets/{id}', 'CustomerWalletController@show')
    ->name('wallets.show')
    ->middleware('auth:api');


Route::get('products','ProductController@index')
    ->name('products');

Route::post('products','ProductController@store')
    ->name('products.store');

Route::patch('products/{id}','ProductController@update')
    ->name('products.update');

Route::get('products/{id}','ProductController@show')
    ->name('products.show');



Route::get('visit_reason','VisitReasonController@index');
Route::post('visit_reason','VisitReasonController@store');
Route::patch('visit_reason/{id}','VisitReasonController@update');
Route::get('visit_reason/{id}','VisitReasonController@show');
