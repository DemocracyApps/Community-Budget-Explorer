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

Route::resource('organizations',                    'API\v1\OrganizationsController', ['only'=>['index','show']]);
Route::resource('organizations/{orgId}/accounts',   'API\v1\AccountsController',    ['only'=>['index','show']]);
Route::resource('organizations/{orgId}/datasets',   'API\v1\DatasetsController',    ['only'=>['index','show']]);
Route::resource('organizations/{orgId}/categories', 'API\v1\CategoriesController',  ['only'=>['index','show']]);

