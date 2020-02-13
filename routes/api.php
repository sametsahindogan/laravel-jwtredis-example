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
Route::post("/login", "AuthController@authenticate");
Route::post("/register", "AuthController@register");
Route::get("/refresh", "AuthController@refresh")->middleware('refreshable');
Route::get("/logout", "AuthController@logout")->middleware('auth');

Route::group(['middleware' => 'auth'], function () {

    Route::group(['prefix' => '/roles'], function () {
        Route::get("/", "RoleController@get");
        Route::post("/", "RoleController@create");
        Route::put("/{id}", "RoleController@update");
    });

    Route::group(['prefix' => '/permissions'], function () {
        Route::get("/", "PermissionController@get");
        Route::post("/", "PermissionController@create");
        Route::put("/{id}", "PermissionController@update");
    });

    Route::group(['prefix' => '/users', 'middleware' => 'role:admin|user'], function () {

        Route::get("/", "UserController@get");

        Route::group(['prefix' => '/{id}'], function () {

            Route::group(['prefix' => '/roles'], function () {
                Route::post("/", "UserController@assignRole");
            });

            Route::group(['prefix' => '/permissions'], function () {
                Route::post("/", "UserController@givePermission");
            });

            Route::group(['prefix' => '/banned'], function () {
                Route::get("/", "UserController@banned");
            });

        });

    });

});
