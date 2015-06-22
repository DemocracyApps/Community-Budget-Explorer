<?php namespace DemocracyApps\GB\Http\Controllers;
use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Sites\Site;

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
class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$sites = Site::where('published','=',true)->orderBy('name')->get();
		foreach ($sites as $site) {
			$site->governmentName = GovernmentOrganization::where('id','=',$site->government)->first()->name;
		}
		return view('home', array('sites' => $sites));
	}
    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index1()
    {

        return "Yupper";
    }

}
