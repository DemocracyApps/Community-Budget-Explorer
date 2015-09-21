<?php namespace DemocracyApps\GB\Http\Controllers\Government;

use Aws\CloudFront\Exception\Exception;
use DemocracyApps\GB\Data\DataSource;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Organizations\GovernmentOrganization;
use Illuminate\Http\Request;

class GovernmentDataController extends Controller {

    protected $governmentOrganization = null;


    protected $dataSource = null;

    public function __construct(GovernmentOrganization $org, DataSource $dataSource)
    {
        $this->governmentOrganization = $org;
        $this->dataSource = $dataSource;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($govt_org_id)
    {
        $organization = GovernmentOrganization::find($govt_org_id);
        $dataSources = DataSource::where('organization', '=', $govt_org_id)->orderBy('id')->get();

        return view('government.data.index', array('organization'=>$organization, 'dataSources' => $dataSources));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($govt_org_id, Request $request)
    {
        $organization = GovernmentOrganization::find($govt_org_id);
        return view('government.data.create', array('organization'=>$organization));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($govt_org_id, Request $request)
    {
        $rules = ['name' => 'required', 'type' => 'in:file,api'];

        $this->validate($request, $rules);
        $organization = $request->get('organization');
        $this->dataSource->name = $request->get('name');
        $this->dataSource->organization = $organization;
        $this->dataSource->source_type = $request->get('type');
        if ($request->has('description')) $this->dataSource->description = $request->get('description');
        $this->dataSource->save();

        return redirect("/governments/$organization/data");
    }

    public function upload($govt_org_id, Request $request)
    {
        if ($request->method() == 'GET') {
            $organization = GovernmentOrganization::find($govt_org_id);
            $dataSourceId = $request->get('datasource');
            return view('government.data.upload', array('organization' => $organization, 'datasource' => $dataSourceId));
        }
        else { // POST
            $format = $request->get('format');
            if ($format == 'simplebudget') {
                $rules = ['year' => 'required | digits:4', 'year_count'=>'required | integer',
                  'categories' => 'required | integer'];
                $this->validate($request, $rules);

                if (! $request->hasFile('data')) {
                    return redirect()->back()->withInput()->withErrors(array('file'=>'You must select a file to upload'));
                }

            }
            else if ($format == 'simpleproject') {

            }
            else {
                throw new \Exception("Unknown format $format in data upload");
            }
            dd($request);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($govt_org_id, $id)
    {
//        $organization = GovernmentOrganization::find($govt_org_id);
//        $orgUser = GovernmentOrganizationUser::find($id);
//        $user = User::find($orgUser->user_id);
//        return view('government.users.edit', array('organization'=>$organization, 'orgUser'=>$orgUser, 'user'=>$user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($govt_org_id, $id, Request $request)
    {
//        $rules = ['access'=>'required'];
//        $this->validate($request, $rules);
//        $orgUser = GovernmentOrganizationUser::find($id);
//        $orgUser->access = $request->get('access');
//        $orgUser->save();
//
//        return redirect('/governments/'.$govt_org_id.'/users');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}