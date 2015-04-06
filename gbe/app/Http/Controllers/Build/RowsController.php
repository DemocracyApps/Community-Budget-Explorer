<?php namespace DemocracyApps\GB\Http\Controllers\Build;

use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Sites\Page;
use DemocracyApps\GB\Sites\Site;
use DemocracyApps\GB\Sites\Component;
use Illuminate\Http\Request;

class RowsController extends Controller {

    protected $row;

    public function __construct(Component $row)
    {
        $this->row = $row;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($slug)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($slug, $pageId)
    {
        $site = Site::where('slug','=',$slug)->first();
        $page = Page::find($pageId);
        return view('build.pages.rows.create', ['site'=>$site, 'page'=>$page]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($slug, $pageId, Request $request)
    {
        $rules = ['title'=>'required'];
        $this->validate($request, $rules);

        $site = Site::where('slug','=',$slug)->first();
        $this->row->title = $request->get('title');
        $this->row->page_id = $pageId;
        $this->row->save();
        return redirect("/build/$slug/pages/$pageId");
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($slug, $id)
	{
        //
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($slug, $id)
	{
        //
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($slug, $id, Request $request)
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
