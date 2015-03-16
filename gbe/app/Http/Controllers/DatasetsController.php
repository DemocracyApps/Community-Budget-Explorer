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
use DemocracyApps\GB\Accounts\Dataset;
use DemocracyApps\GB\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DemocracyApps\GB\Organization;
use DemocracyApps\GB\Accounts\AccountChart;

class DatasetsController extends Controller {

    protected $dataset = null;

    function __construct (Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
        $organization = Organization::find($request->get('organization'));
        $chart = AccountChart::find($request->get('chart'));
        return view('system.dataset.create', array('organization'=>$organization, 'chart'=>$chart));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{

        $rules = ['name' => 'required', 'year'=>'required | digits:4', 'type'=>'in:actual,budget'];


        $this->validate($request, $rules);

        if (! $request->hasFile('data')) {
            return redirect()->back()->withInput()->withErrors(array('file'=>'You must select a file to upload'));
        }
        $this->dataset->name = $request->get('name');
        $this->dataset->organization = $request->get('organization');
        $this->dataset->chart = $request->get('chart');
        $this->dataset->year = $request->get('year');
        $this->dataset->type = $request->get('type');
        if ($request->has('description')) $this->dataset->description = $request->get('description');
        $this->dataset->save();

        $file = $request->file('data');
        $data = array();
        $data['dataset'] = $this->dataset->id;
        $data['userId'] = \Auth::user()->id;
        $name = uniqid('upload');
        $file->move('/var/www/gbe/public/downloads', $name);
        $data['filePath'] = '/var/www/gbe/public/downloads/' . $name;
        $notification = new \DemocracyApps\GB\Utility\Notification;
        $notification->user_id = $data['userId'];
        $notification->status = 'Scheduled';
        $notification->type = 'DatasetUpload';
        $notification->save();
        $data['notificationId'] = $notification->id;
        \Queue::push('\DemocracyApps\GB\Accounts\CSVProcessors\DatasetCSVProcessor', $data);

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
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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
