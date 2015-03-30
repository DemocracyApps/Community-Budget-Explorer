<?php namespace DemocracyApps\GB\Http\Controllers\Government;
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
use DemocracyApps\GB\Budget\Account;
use DemocracyApps\GB\Budget\AccountChart;
use DemocracyApps\GB\Budget\Dataset;
use DemocracyApps\GB\Http\Controllers\Controller;
use DemocracyApps\GB\Organizations\GovernmentOrganization;

use Illuminate\Http\Request;

class GovernmentOrganizationsController extends Controller {

    protected $organization = null;

    function __construct (GovernmentOrganization $org)
    {
        $this->organization = $org;
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return redirect('/');
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		return view('government.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$rules = ['name' => 'required'];
        $this->validate($request, $rules);

        $this->organization->name = $request->get('name');
        $this->organization->save();

        // Now create the default chart of accounts
        $chart = new AccountChart();
        $chart->name = "default";
        $chart->government_organization = $this->organization->id;
        $chart->save();
        return redirect('/governments/'.$this->organization->id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $org = GovernmentOrganization::find($id);
        if ($org == null) return redirect('/system/organizations');
        $charts = AccountChart::where('government_organization', '=', $org->id)->get();
        $datasets = Dataset::where('government_organization', '=', $org->id)->get();
        return view("government.show", array('organization' => $org, 'charts' => $charts, 'datasets'=>$datasets));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id, Request $request)
	{
        $org = GovernmentOrganization::find($id);
        if ($org == null) return redirect('/system/organizations');
        return view('government.edit', array('organization' => $org));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
        $rules = ['name' => 'required'];
        $this->validate($request, $rules);

        $this->organization = GovernmentOrganization::find($id);
        $this->organization->name = $request->get('name');
        $this->organization->save();

        return redirect('/governments/'.$this->organization->id);
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $org = GovernmentOrganization::find($id);
        if ($org != null) {
            $org->delete();
        }
        return redirect('/system/governments');
	}

}
