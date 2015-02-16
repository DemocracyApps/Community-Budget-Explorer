<?php

Route::get('auth/login', 'Auth\AuthController@login');
Route::post('auth/login', 'Auth\AuthController@login');
Route::get('auth/loginfb', 'Auth\AuthController@loginfb');
Route::get('auth/logintw', 'Auth\AuthController@logintw');

Route::get('auth/register', 'Auth\AuthController@register');
Route::post('auth/register', 'Auth\AuthController@register');
Route::get('auth/logout', 'Auth\AuthController@logout');

Route::any('auth/thanks', 'Auth\AuthController@thanks');
Route::get('auth/confirm', 'Auth\AuthController@confirm');
Route::get('auth/confirm/{status}', 'Auth\AuthController@confirmResponse');
