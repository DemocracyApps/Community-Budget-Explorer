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
use DemocracyApps\GB\Budget\Dataset;
use DemocracyApps\GB\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Budget\AccountChart;

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
	public function index($govId)
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($govId, Request $request)
	{
        $organization = GovernmentOrganization::find($govId);
        $chart = AccountChart::find($request->get('chart'));
        return view('government.dataset.create', array('organization'=>$organization, 'chart'=>$chart));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($govId, Request $request)
	{

        $rules = ['name' => 'required', 'year'=>'required | digits:4', 'type'=>'in:actual,budget'];


        $this->validate($request, $rules);

        if (! $request->hasFile('data')) {
            return redirect()->back()->withInput()->withErrors(array('file'=>'You must select a file to upload'));
        }
        $this->dataset->name = $request->get('name');
        $this->dataset->government_organization = $govId;
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
        \Queue::push('\DemocracyApps\GB\Budget\CSVProcessors\DatasetCSVProcessor', $data);

        return redirect("/governments/$govId");
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($govId, $id)
	{
		$this->dataset = Dataset::find($id);

        $items = \DB::table('data_items')
            ->join('accounts', 'accounts.id', '=', 'data_items.account')
            ->join('account_category_values', 'account_category_values.id', '=', 'data_items.category1')
            ->where('data_items.dataset','=',$id)
            ->orderBy('data_items.category1')
            ->select('account_category_values.name', 'accounts.type', 'data_items.amount')
            ->get();
        $output = array();
        foreach ($items as $item) {
            if (! array_key_exists($item->name, $output)) {
                $output[$item->name] = new \stdClass();
                $output[$item->name]->name = $item->name;
                $output[$item->name]->revenue = 0.0;
                $output[$item->name]->expense = 0.0;
            }
            if ($item->type == Account::EXPENSE)
                $output[$item->name]->expense += $item->amount;
            elseif ($item->type == Account::REVENUE)
                $output[$item->name]->revenue += $item->amount;
        }
        $organization = GovernmentOrganization::find($govId);

        return view('government.dataset.show', array('organization'=>$organization, 'dataset'=>$this->dataset, 'data'=>$output));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($govId, $id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($govId, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($govId, $id)
	{
		//
	}

}
