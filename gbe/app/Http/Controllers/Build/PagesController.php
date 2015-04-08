<?php namespace DemocracyApps\GB\Http\Controllers\Build;

use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Services\JsonProcessor;
use DemocracyApps\GB\Sites\Component;
use DemocracyApps\GB\Sites\Layout;
use DemocracyApps\GB\Sites\Page;
use DemocracyApps\GB\Sites\PageComponent;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Http\Request;

class PagesController extends Controller {

    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($slug)
    {
        $site = Site::where('slug','=',$slug)->first();
        $pages = Page::where('site','=',$site->id)->orderBy('ordinal')->get();
        $list=Layout::all();
        $layouts = array();
        foreach ($list as $item) {
            $layouts[$item->id] = $item;
        }
        return view('build.pages', array('site'=>$site, 'pages'=>$pages, 'layouts'=>$layouts));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($slug)
    {
        $site = Site::where('slug','=',$slug)->first();
        $layouts = Layout::where('public','=',true)->orderBy('id')->get();
        return view('build.pages.create', ['site'=>$site, 'layouts'=>$layouts]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($slug, Request $request)
    {
        $rules = ['title'=>'required', 'short_name'=>'required'];
        $this->validate($request, $rules);

        $site = Site::where('slug','=',$slug)->first();
        $this->page->title = $request->get('title');
        $this->page->short_name = $request->get('short_name');
        if ($request->has('layout')) {
            $layout = $request->layout;
            if ($layout > 0) $this->page->layout = $layout;
        }
        if ($request->has('description')) $this->page->description = $request->get('description');
        $this->page->site = $site->id;
        $this->page->save();
        $this->page->ordinal = $this->page->id;
        $this->page->save();
        return redirect("/build/$slug/pages");
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($slug, $id)
	{
        $site = Site::where('slug','=',$slug)->first();
        $this->page = Page::find($id);
        $list=Layout::all();
        $lId = 1;
        if ($this->page->layout != null) $lId = $this->page->layout;
        $layout = Layout::find($lId);
        $jp = new JsonProcessor();

        $str = $jp->minifyJson($layout->specification);
        $cfig = $jp->decodeJson($str, true);
        if ( ! $cfig) {
            throw new \Exception("Unable to part layout specification " . $layout->name);
        }
        $layout->specification = $cfig;

        $targets = array();
        foreach($layout->specification['rows'] as $row) {
            foreach($row['columns'] as $column) {
                $targets[] = $column['id'];
            }
        }
        $pageComponents = PageComponent::where('page','=',$id)->get();
        $list = Component::all();
        $components = array();
        foreach ($list as $item) {
            $components[$item->id] = $item;
        }
        return view('build.pages.show', array('site'=>$site, 'page'=> $this->page, 'targets'=>$targets,
                    'layout'=>$layout, 'pageComponents'=>$pageComponents, 'components'=>$components));
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($slug, $id)
	{
        $site = Site::where('slug','=',$slug)->first();
        $this->page = Page::find($id);
        $layouts = Layout::where('public','=',true)->orderBy('id')->get();
        return view('build.pages.edit', ['site'=>$site, 'page'=>$this->page,'layouts'=>$layouts]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($slug, $id, Request $request)
	{
        $rules = ['title'=>'required', 'short_name'=>'required'];
        $this->validate($request, $rules);

        $site = Site::where('slug','=',$slug)->first();
        $this->page = Page::find($id);
        $this->page->title = $request->get('title');
        $this->page->short_name = $request->get('short_name');
        if ($request->has('layout')) {
            $layout = $request->layout;
            if ($layout > 0) $this->page->layout = $layout;
        }
        else {
            $this->page->layout = null;
        }
        if ($request->has('description')) $this->page->description = $request->get('description');
        $this->page->site = $site->id;
        $this->page->save();
        return redirect("/build/$slug/pages/" . $this->page->id);
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
