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
use DemocracyApps\GB\Budget\AccountCategory;
use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Budget\Account;
use DemocracyApps\GB\Budget\AccountChart;
use DemocracyApps\GB\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AccountsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($govId, Request $request)
	{
        $organization = GovernmentOrganization::find($govId);
		$chart = $request->get('chart');
        $chart = AccountChart::find($chart);
        $accounts = Account::where('chart', '=', $chart->id)->get();
        $categories= AccountCategory::where('chart', '=', $chart->id)->get();
        return view ('government.account.index', array('chart' => $chart, 'organization'=>$organization,
                                                    'accounts' => $accounts, 'categories' => $categories));
	}

    /**
     * Upload form for CSV file with accounts definitions
     * @return Response
     */
    public function upload($govId, Request $request)
    {
        $chart = $request->get('chart');
        if ($request->method() == 'GET') {
            return view('government.account.upload', array('chartId' => $chart, 'governmentId' => $govId));
        }
        else if ($request->method() == 'POST') {

            if ($request->hasFile('accounts')) {
                $file = $request->file('accounts');
                $data = array();
                $data['chart'] = $chart;
                $data['userId'] = \Auth::user()->id;
                $name = uniqid('upload');
                $file->move('/var/www/gbe/public/downloads', $name);
                $data['filePath'] = '/var/www/gbe/public/downloads/' . $name;
                $notification = new \DemocracyApps\GB\Utility\Notification;
                $notification->user_id = $data['userId'];
                $notification->status = 'Scheduled';
                $notification->type = 'AccountsUpload';
                $notification->save();
                $data['notificationId'] = $notification->id;
                \Queue::push('\DemocracyApps\GB\Accounts\CSVProcessors\AccountCSVProcessor', $data);
            }
        }
        return redirect("/governments/'.$govId.'/accounts?chart=".$chart);
    }
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($govId)
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($govId)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($govId, $id)
	{
		//
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
