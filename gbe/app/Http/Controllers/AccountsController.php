<?php namespace DemocracyApps\GB\Http\Controllers;

use DemocracyApps\GB\Accounts\AccountCategory;
use DemocracyApps\GB\Organization;
use DemocracyApps\GB\Accounts\Account;
use DemocracyApps\GB\Accounts\AccountChart;
use DemocracyApps\GB\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AccountsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$chart = $request->get('chart');
        $chart = AccountChart::find($chart);
        $organization = Organization::find($chart->organization);
        $accounts = Account::where('chart', '=', $chart->id)->get();
        $categories= AccountCategory::where('chart', '=', $chart->id)->orderBy('order')->get();
        return view ('system.account.index', array('chart' => $chart, 'organization'=>$organization,
                                                    'accounts' => $accounts, 'categories' => $categories));
	}

    /**
     * Upload form for CSV file with accounts definitions
     * @return Response
     */
    public function upload(Request $request)
    {
        $chart = $request->get('chart');
        if ($request->method() == 'GET') {
            return view('system.account.upload', array('chartId' => $chart));
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
        return redirect("/system/accounts?chart=".$chart);
    }
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
