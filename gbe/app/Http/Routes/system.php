<?php

Route::get('settings', 'SystemController@settings');
Route::get('users', 'SystemController@users');
Route::resource('organizations', 'OrganizationsController');
Route::get('projects', 'SystemController@projects');
