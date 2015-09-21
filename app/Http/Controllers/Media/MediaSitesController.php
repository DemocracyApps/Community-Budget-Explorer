<?php namespace DemocracyApps\GB\Http\Controllers\Media;

use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Organizations\MediaOrganization;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Http\Request;

class MediaSitesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
    public function index($media_org_id)
    {
        $organization = MediaOrganization::find($media_org_id);
        $sites = Site::where('owner_type','=',Site::MEDIA)
            ->where('owner', '=', $media_org_id)->get();
        return view('media.sites.index', array('organization'=>$organization, 'sites'=>$sites));
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function createSite($media_org_id)
	{
        $organization = MediaOrganization::find($media_org_id);
        $governments = GovernmentOrganization::orderBy('id')->get();
        return view('media.sites.create', array('organization'=>$organization, 'governments'=>$governments));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeSite($media_org_id, Request $request)
	{
        $rules = ['name'=>'required', 'slug'=>'required | unique:sites'];
        $this->validate($request, $rules);

        $site = new Site();
        $site->slug = $request->get('slug');
        $site->name = $request->get('name');
        $site->owner_type = Site::MEDIA;
        $site->owner = $media_org_id;
        $site->government = $request->get('government');
        $site->save();

        return redirect('/media/'.$media_org_id.'/sites');

    }
}
