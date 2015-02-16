<?php namespace DemocracyApps\GB\Http\Controllers;

use DemocracyApps\GB\Accounts\Account;
use DemocracyApps\GB\Accounts\AccountChart;
use DemocracyApps\GB\Http\Controllers\Controller;
use DemocracyApps\GB\Organization;

use Illuminate\Http\Request;

class OrganizationsController extends Controller {

    protected $organization = null;

    function __construct (Organization $org)
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
        $organizations = Organization::orderBy('id')->get();
        return view('system.organization.index', array('organizations' => $organizations));
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		return view('system.organization.create');
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
        $chart->organization = $this->organization->id;
        $chart->save();
        return redirect('/system/organizations');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $org = Organization::find($id);
        if ($org == null) return redirect('/system/organizations');
        $charts = AccountChart::where('organization', '=', $org->id)->get();
        return view("system.organization.show", array('organization' => $org, 'charts' => $charts));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id, Request $request)
	{
        $org = Organization::find($id);
        if ($org == null) return redirect('/system/organizations');
        return view('system.organization.edit', array('organization' => $org));
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

        $this->organization = Organization::find($id);
        $this->organization->name = $request->get('name');
        $this->organization->save();

        return redirect('/system/organizations');
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $org = Organization::find($id);
        if ($org != null) {
            $org->delete();
        }
        return redirect('/system/organizations');
	}

}
