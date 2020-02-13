## About Project

This project simple usage example of [laravel-jwtredis](https://github.com/sametsahindogan/laravel-jwtredis) package.

## Documentation

* `php artisan migrate`<br>

* `php artisan db:seed`

Authentication credentials;
```dotenv
email: admin@admin.com
password: password
```

The routes and what they do are pretty simple. You can examine how it is used [laravel-jwtredis](https://github.com/sametsahindogan/laravel-jwtredis) package.

Here is `api.php` route file;
```php
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
```

