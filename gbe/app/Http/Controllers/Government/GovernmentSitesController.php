<?php namespace DemocracyApps\GB\Http\Controllers\Government;

use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Http\Request;

class GovernmentSitesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
    public function index($govt_org_id)
    {
        $organization = GovernmentOrganization::find($govt_org_id);
        $sites = Site::where('owner_type','=',Site::GOVERNMENT)
            ->where('owner', '=', $govt_org_id)->get();
        return view('government.sites.index', array('organization'=>$organization, 'sites'=>$sites));
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function createSite($govt_org_id)
	{
        $organization = GovernmentOrganization::find($govt_org_id);
        return view('government.sites.create', array('organization'=>$organization));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeSite($govt_org_id, Request $request)
	{
        $rules = ['name'=>'required', 'slug'=>'required | unique:sites'];
        $this->validate($request, $rules);

        $site = new Site();
        $site->slug = $request->get('slug');
        $site->name = $request->get('name');
        $site->owner_type = Site::GOVERNMENT;
        $site->owner = $govt_org_id;
        $site->government = $govt_org_id;
        $site->save();

        return redirect('/governments/'.$govt_org_id.'/sites');

    }
}
