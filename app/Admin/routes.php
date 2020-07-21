<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');

    $router->resource('categories', 'CategoryController');

    $router->resource('food', 'FoodController');

    $router->resource('desks', 'DeskController');

    $router->resource('orders', 'OrderController');

    $router->resource('configs', 'ConfigController');

    $router->resource('customers', 'CustomerController');

    $router->resource('bills', 'BillController');
});
