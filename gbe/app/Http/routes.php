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


//Log::info("Top of routes with URI " . \Request::server('REQUEST_URI') .
//          " and method " .\Request::server('REQUEST_METHOD'));


Route::group(['domain'=>'gbetest.dev'], function() {
    Route::get('/', 'HomeController@index1');
});

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
Route::group(['prefix' => 'system', 'middleware' => 'cnp.system'], function ()
{
    require __DIR__.'/Routes/system.php';
});

Route::group(['prefix' => 'api/v1'], function () {
    require __DIR__.'/Routes/apiv1.php';
});
