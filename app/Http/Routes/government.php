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

Route::resource('governments', 'Government\GovernmentOrganizationsController');
Route::resource('governments/{govId}/users', 'Government\GovernmentUsersController');
Route::get('governments/{govId}/data/upload', 'Government\GovernmentDataController@upload');
Route::post('governments/{govId}/data/upload', 'Government\GovernmentDataController@upload');
Route::get('governments/{govId}/data/execute', 'Government\GovernmentDataController@execute');
Route::get('governments/{govId}/data/activate', 'Government\GovernmentDataController@activate');
Route::resource('governments/{govId}/data', 'Government\GovernmentDataController');

Route::get('governments/{govId}/sites', 'Government\GovernmentSitesController@index');
Route::get('governments/{govId}/sites/create', 'Government\GovernmentSitesController@createSite');
Route::post('governments/{govId}/sites', 'Government\GovernmentSitesController@storeSite');

Route::any('governments/{govId}/accounts/upload', 'Government\AccountsController@upload');
Route::resource('governments/{govId}/accounts', 'Government\AccountsController');

Route::any('governments/{govId}/accountcategories/upload', 'Government\AccountCategoriesController@upload');
Route::resource('governments/{govId}/accountcategories', 'Government\AccountCategoriesController');

Route::resource('governments/{govId}/accountcategoryvalues', 'Government\AccountCategoryValuesController');
Route::resource('governments/{govId}/datasets', 'Government\DatasetsController');
