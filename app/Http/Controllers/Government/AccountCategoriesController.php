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
use DemocracyApps\GB\Budget\AccountCategoryValue;
use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AccountCategoriesController extends Controller {

    protected $category = null;

    function __construct (AccountCategory $category)
    {
        $this->category = $category;
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
     * Upload form for CSV file with category values
     * @return Response
     */
    public function upload($govId, Request $request)
    {
        $category = $request->get('category');
        $category = AccountCategory::find($category);
        if ($request->method() == 'GET') {
            return view('government.account_category.upload', array('category' => $category, 'governmentId'=>$govId));
        }
        else if ($request->method() == 'POST') {

            if ($request->hasFile('categories')) {
                $file = $request->file('categories');
                $data = array();
                $data['category'] = $category->id;
                $data['userId'] = \Auth::user()->id;
                $name = uniqid('upload');
                $file->move('/var/www/gbe/public/downloads', $name);
                $data['filePath'] = '/var/www/gbe/public/downloads/' . $name;
                $notification = new \DemocracyApps\GB\Utility\Notification;
                $notification->user_id = $data['userId'];
                $notification->status = 'Scheduled';
                $notification->type = 'CategoriesUpload';
                $notification->save();
                $data['notificationId'] = $notification->id;
                \Queue::push('\DemocracyApps\GB\Accounts\CSVProcessors\AccountCategoryCSVProcessor', $data);
            }
        }
        return redirect("/governments/$govId/accountcategories/".$category->id);
    }


    /**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($govId, Request $request)
	{
        $chart = $request->get('chart');
        return view('government.account_category.create', array('chart'=>$chart));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
    public function store($govId, Request $request)
    {
        $rules = ['name' => 'required'];
        $this->validate($request, $rules);
        $chart = $request->get('chart');
        $max = 0;
        $max = AccountCategory::where('chart', '=', $chart)->max('order');
        $this->category->name = $request->get('name');
        $this->category->chart = $chart;
        $this->category->order = $max + 1;
        $this->category->save();

        return redirect("/governments/$govId/accountcategories/$this->category->id");
    }


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($govId, $id, Request $request)
	{
        $category = AccountCategory::find($id);
        $values = AccountCategoryValue::where('category', '=', $id)->get();
        return view('government.account_category.show', array('category'=>$category, 'values'=>$values, 'governmentId'=>$govId));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($govId, $id)
	{
        $category = AccountCategory::find($id);
        return view('government.account_category.edit', array('category' => $category, 'governmentId'=>$govId));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($govId, $id, Request $request)
	{
        $rules = ['name' => 'required'];
        $this->validate($request, $rules);

        $this->category = AccountCategory::find($id);
        $this->category->name = $request->get('name');
        $this->category->save();

        return redirect('/governments/'.$govId.'/accountcategories/'.$this->category->id);
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
