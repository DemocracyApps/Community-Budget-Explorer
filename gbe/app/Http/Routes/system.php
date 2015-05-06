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

Route::get('settings', 'System\SystemController@settings');
Route::get('users', 'System\SystemController@users');

Route::get('governments', 'System\SystemController@governments');
Route::get('governments/create', 'System\SystemController@createGovernment');
Route::post('governments', 'System\SystemController@storeGovernment');

Route::get('media', 'System\SystemController@media');
Route::get('media/create', 'System\SystemController@createMedia');
Route::post('media', 'System\SystemController@storeMedia');

Route::get('layouts', 'System\SystemController@layouts');
Route::get('layouts/create', 'System\SystemController@createLayout');
Route::post('layouts', 'System\SystemController@storeLayout');
Route::get('layouts/{layoutId}/edit', 'System\SystemController@editLayout');
Route::put('layouts/{layoutId}', 'System\SystemController@updateLayout');

Route::get('components', 'System\SystemController@components');
Route::get('components/create', 'System\SystemController@createComponent');
Route::post('components', 'System\SystemController@storeComponent');
Route::get('components/{componentId}/edit', 'System\SystemController@editComponent');
Route::put('components/{componentId}', 'System\SystemController@updateComponent');
Route::get('components/{componentId}', 'System\SystemController@showComponent');
Route::delete('components/{componentId}', 'System\SystemController@deleteComponent');
