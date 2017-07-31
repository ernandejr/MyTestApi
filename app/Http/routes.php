<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
  Route::get('/', function () {
      return response()->json(['message' => 'MyTest API', 'status' => 'Connected']);;
  });
  Route::get('/validation/{validationtoken}', 'ApiAuthController@validateToken');
  Route::group(['middleware' => 'cors'], function(){
  	Route::post('/sign_up', 'ApiAuthController@create');
  	Route::post('/auth_login', 'ApiAuthController@userAuth');
  	Route::get('/profile', 'ApiAuthController@getProfile');
  	Route::get('/logout', 'ApiAuthController@logout');
  });
