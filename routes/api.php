<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api'], function () {

    //订单状态统计
    Route::get('order_status', 'VisualizationController@order_status');
    //本月热门销量
    Route::get('order_count', 'VisualizationController@order_count');
    //本周销售额
    Route::get('sales_amount', 'VisualizationController@sales_amount');
    //菜品销量情况
    Route::get('sales_count', 'VisualizationController@sales_count');

});