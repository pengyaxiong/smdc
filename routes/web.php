<?php

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

//Route::get('/', function () {
//    return view('welcome');
//});

//菜品列表
Route::get('/', 'IndexController@index');
//确认订单页&确认加菜页
Route::get('/order', 'IndexController@order');

//点菜&加菜
Route::post('/add', 'IndexController@add');
Route::post('/delete', 'IndexController@delete');

//下单
Route::post('/do_order', 'IndexController@do_order');
Route::post('/do_add', 'IndexController@do_add');

//我的订单页
Route::get('/order_info', 'IndexController@order_info');
