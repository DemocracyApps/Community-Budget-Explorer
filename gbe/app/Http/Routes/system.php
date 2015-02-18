<?php

Route::get('settings', 'SystemController@settings');
Route::get('users', 'SystemController@users');
Route::resource('organizations', 'OrganizationsController');
Route::any('accounts/upload', 'AccountsController@upload');
Route::resource('accounts', 'AccountsController');
Route::any('accountcategories/up', 'AccountCategoriesController@up');
Route::any('accountcategories/down', 'AccountCategoriesController@down');
Route::any('accountcategories/upload', 'AccountCategoriesController@upload');
Route::resource('accountcategories', 'AccountCategoriesController');
Route::resource('accountcategoryvalues', 'AccountCategoryValuesController');
Route::get('projects', 'SystemController@projects');
Route::resource('datasets', 'DatasetsController');
