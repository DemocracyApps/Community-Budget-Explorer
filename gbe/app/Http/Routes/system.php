<?php

Route::get('settings', 'SystemController@settings');
Route::get('users', 'SystemController@users');
Route::resource('organizations', 'OrganizationsController');
Route::any('accounts/upload', 'AccountsController@upload');
Route::resource('accounts', 'AccountsController');
Route::get('projects', 'SystemController@projects');
