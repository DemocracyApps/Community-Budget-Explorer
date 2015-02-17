<?php namespace DemocracyApps\GB\Http\Controllers;

use DemocracyApps\GB\Accounts\AccountCategory;
use DemocracyApps\GB\Accounts\AccountCategoryValue;
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
	public function index()
	{
		//
	}

    public function up (Request $request)
    {
        $current = AccountCategory::find($request->get('category'));
        $target = $current->order - 1;
        $items = AccountCategory::where('chart', '=', $current->chart)->get();

        $done = false;
        foreach ($items as $item) {
            if (! $done && $item->order == $target) { // this is it
                $item->order += 1;
                $current->order -= 1;
                $item->save();
                $current->save();
                $done = true;
                break;
            }
        }
        return redirect("/system/accounts?chart=" . $current->chart);
    }

    public function down (Request $request)
    {
        $current = AccountCategory::find($request->get('category'));
        $target = $current->order + 1;
        $items = AccountCategory::where('chart', '=', $current->chart)->get();

        $done = false;
        foreach ($items as $item) {
            if (! $done && $item->order == $target) { // this is it
                $item->order -= 1;
                $current->order += 1;
                $item->save();
                $current->save();
                $done = true;
                break;
            }
        }
        return redirect("/system/accounts?chart=" . $current->chart);
    }
    /**
     * Upload form for CSV file with category values
     * @return Response
     */
    public function upload(Request $request)
    {
        $category = $request->get('category');
        $category = AccountCategory::find($category);
        if ($request->method() == 'GET') {
            return view('system.account_category.upload', array('category' => $category));
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
        return redirect("/system/accountcategories/".$category->id);
    }


    /**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
        $chart = $request->get('chart');
        return view('system.account_category.create', array('chart'=>$chart));
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
        $chart = $request->get('chart');
        $max = 0;
        $max = AccountCategory::where('chart', '=', $chart)->max('order');
        $this->category->name = $request->get('name');
        $this->category->chart = $chart;
        $this->category->order = $max + 1;
        $this->category->save();

        return redirect('/system/accountcategories/'.$this->category->id);
    }


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, Request $request)
	{
        $category = AccountCategory::find($id);
        $values = AccountCategoryValue::where('category', '=', $id)->get();
        return view('system.account_category.show', array('category'=>$category, 'values'=>$values));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $category = AccountCategory::find($id);
        return view('system.account_category.edit', array('category' => $category));
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

        $this->category = AccountCategory::find($id);
        $this->category->name = $request->get('name');
        $this->category->save();

        return redirect('/system/accountcategories/'.$this->category->id);
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
