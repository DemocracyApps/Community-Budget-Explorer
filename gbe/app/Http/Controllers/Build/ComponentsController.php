<?php namespace DemocracyApps\GB\Http\Controllers\Build;

use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Sites\Page;
use DemocracyApps\GB\Sites\PageComponent;
use DemocracyApps\GB\Sites\Site;
use DemocracyApps\GB\Sites\Component;
use Illuminate\Http\Request;

class ComponentsController extends Controller {

    protected $pageComponent;

    public function __construct(PageComponent $pageComponent)
    {
        $this->pageComponent = $pageComponent;
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
        $components = Component::all();
        return view('build.pages.components.create', ['site'=>$site, 'page'=>$page, 'components'=>$components]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($slug, $pageId, Request $request)
    {
        $site = Site::where('slug','=',$slug)->first();
        $this->pageComponent->component = $request->get('component');
        $this->pageComponent->page = $pageId;
        $this->pageComponent->save();
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
