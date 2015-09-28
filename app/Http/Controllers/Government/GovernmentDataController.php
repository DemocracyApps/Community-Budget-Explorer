<?php namespace DemocracyApps\GB\Http\Controllers\Government;

use Aws\CloudFront\Exception\Exception;
use DemocracyApps\GB\Data\DataSource;
use DemocracyApps\GB\Data\DatasourceAction;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Jobs\ProcessUpload;
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
    public function index($govt_org_id, Request $request)
    {
        $params = ['first'=>1, 'second'=>'abcdefg'];
        $url = 'http://gbe.dev:53821/doit'; // Standard AWS IP for instance queries

//        $data = array("name" => "Hagrid", "age" => "36");
//        $data_string = json_encode($data);
//
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json',
//            'Content-Length: ' . strlen($data_string))
//        );
//
//        $result = curl_exec($ch);
//        curl_close($ch);
//        echo "Now back" . PHP_EOL;
//        dd($result);
//        //DONE

        $organization = GovernmentOrganization::find($govt_org_id);
        $dataSources = DataSource::where('organization', '=', $govt_org_id)->orderBy('id')->get();

        $actions = array();
        foreach ($dataSources as $source) {
            $atmp = DatasourceAction::where('datasource_id', '=', $source->id)->orderBy('id', 'desc')->get();
            if (isset($atmp) && sizeof($atmp) > 0) {
                $actions[$source->id] = $atmp[0];
            }
        }
        //dd($actions[1]->id);
        return view('government.data.index', array('organization'=>$organization,
                                                   'dataSources' => $dataSources, 'actions'=> $actions));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($govt_org_id, Request $request)
    {
        $organization = GovernmentOrganization::find($govt_org_id);
        $sourceId = $request->get('datasource');
        return view('government.data.create', array('organization'=>$organization, 'datasource' => $sourceId));
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
            $parameters = new \stdClass();
            $parameters->type = $format;
            if ($parameters->type == 'simplebudget') {
                $parameters->year_count = $request->get('year_count');
                $parameters->start_year = $request->get('year');
                $parameters->category_count = $request->get('categories');
            }
            else if ($parameters->type = 'simpleproject') {

            }
            $this->dataSource = DataSource::find($request->get('datasource'));
            $this->dataSource->status = 'queued';
            $this->dataSource->status_date = date("M d, Y H:i:s");

            $file = $request->file('data');
            $name = uniqid('upload');
            $file->move('/var/www/cbe/public/downloads', $name);
            $parameters->file_path = '/var/www/cbe/public/downloads/' . $name;

            $this->dataSource->setProperty('upload_parameters', $parameters);
            $this->dataSource->save();

            $job = new ProcessUpload($this->dataSource);

            $this->dispatch($job);
            return redirect("/governments/$govt_org_id/data");
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