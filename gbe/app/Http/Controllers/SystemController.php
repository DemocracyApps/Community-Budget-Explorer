<?php namespace DemocracyApps\GB\Http\Controllers;
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
use DemocracyApps\GB\Budget\AccountChart;
use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Users\User;
use Illuminate\Http\Request;

class SystemController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function settings(Request $request)
    {
        return view('system.settings', array());
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function users(Request $request)
    {
        $users = User::orderBy('id')->get();
        return view('system.users', array('users' => $users));
    }

    public function governments (Request $request)
    {
        $organizations = GovernmentOrganization::orderBy('id')->get();
        return view('system.governments', array('organizations' => $organizations));
    }

    /**
     * Show the form for creating a new government.
     *
     * @return Response
     */
    public function createGovernment (Request $request)
    {
        return view('system.government.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function storeGovernment(Request $request)
    {
        $rules = ['name' => 'required'];
        $this->validate($request, $rules);

        $organization = new GovernmentOrganization();
        $organization->name = $request->get('name');
        $organization->save();

        // Now create the default chart of accounts
        $chart = new AccountChart();
        $chart->name = "default";
        $chart->government_organization = $organization->id;
        $chart->save();
        return redirect('/system/governments');
    }

}
