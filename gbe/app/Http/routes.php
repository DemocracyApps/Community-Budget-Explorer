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

Log::info("Top of routes with URI " . \Request::server('REQUEST_URI') .
          " and method " .\Request::server('REQUEST_METHOD'));

Route::get('/', 'HomeController@index');

Route::get('home', 'HomeController@index');

/*************************************************
 *************************************************
 * Sign-up & login pages
 *************************************************
 *************************************************/
require app_path().'/Http/Routes/auth.php';

/*************************************************
 *************************************************
 * Administrative pages for platform admins
 *************************************************
 *************************************************/
Route::group(['prefix' => 'system'], function ()
{
    require __DIR__.'/Routes/system.php';
});

//
//Route::controllers([
//    'auth' => 'Auth\AuthController',
//    'password' => 'Auth\PasswordController',
//]);