<?php
/**
 *
 * This file is part of the Government Budget Explorer (GBE).
 *
 *  The GBE is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GBE is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the GBE.  If not, see <http://www.gnu.org/licenses/>.
 */

Route::get('settings', 'SystemController@settings');
Route::get('users', 'SystemController@users');
Route::resource('organizations', 'Organizations\GovernmentOrganizationsController');
Route::any('accounts/upload', 'AccountsController@upload');
Route::resource('accounts', 'AccountsController');
Route::any('accountcategories/up', 'AccountCategoriesController@up');
Route::any('accountcategories/down', 'AccountCategoriesController@down');
Route::any('accountcategories/upload', 'AccountCategoriesController@upload');
Route::resource('accountcategories', 'AccountCategoriesController');
Route::resource('accountcategoryvalues', 'AccountCategoryValuesController');
Route::get('projects', 'SystemController@projects');
Route::resource('datasets', 'DatasetsController');
