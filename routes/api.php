<?php

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

//Auth
Route::post('/login', 'Auth\LoginController@login');
Route::post('/register', 'Auth\RegisterController@register');

//Merchant
Route::middleware(['auth:api', 'merchant'])->get('merchant/product', 'Merchant\Product\ProductController@index');
Route::middleware(['auth:api', 'merchant'])->post('merchant/product', 'Merchant\Product\ProductController@create');
Route::middleware(['auth:api', 'merchant'])->post('merchant/product/{code}', 'Merchant\Product\ProductController@update');
Route::middleware(['auth:api', 'merchant'])->delete('merchant/product/{code}', 'Merchant\Product\ProductController@delete');
Route::middleware(['auth:api', 'merchant'])->get('merchant/category', 'Merchant\Category\CategoryController@index');

//User
Route::middleware(['auth:api', 'user'])->get('user/transaction/history', 'User\Transaction\TransactionController@history');
Route::middleware(['auth:api', 'user'])->post('user/balance/topup', 'User\Balance\BalanceController@topup');
Route::middleware(['auth:api', 'user'])->get('user/balance/history', 'User\Balance\BalanceController@history');
Route::middleware(['auth:api', 'user'])->get('user/products', 'Products\ProductController@index');
Route::middleware(['auth:api', 'user'])->post('user/transaction/create', 'User\Transaction\TransactionController@buy');

//Reward
Route::middleware(['auth:api', 'user'])->get('rewards', 'User\Reward\RewardController@index');
Route::middleware(['auth:api', 'user'])->post('rewards/buy', 'User\Reward\RewardController@buy');
