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
	public function show($slug, $pageId, $id)
	{
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($slug, $pageId, $id)
    {
        $site = Site::where('slug','=',$slug)->first();
        $page = Page::find($pageId);
        $this->pageComponent = PageComponent::find($id);
        $component = Component::find($this->pageComponent->component);
        $cardSets = $site->getCardsByCardSet();
        $dataSets = $site->getDatasets();
        return view('build.pages.components.edit', ['site'=>$site, 'page'=>$page, 'component'=>$component,
            'pageComponent'=>$this->pageComponent, 'dataDefs'=>$component->getProperty('data'),
            'cardSets' => $cardSets, 'dataSets'=>$dataSets]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($slug, $pageId, $id, Request $request)
	{
        $site = Site::where('slug','=',$slug)->first();
        $this->pageComponent = PageComponent::find($id);
        $component = Component::find($this->pageComponent->component);
        $dataDefs = $component->getProperty('data');
        $dataBundle = array();
        foreach ($dataDefs as $def) {
            $type = $def['type'];
            $tag = $def['tag'];
            $data = [];
            $data['type'] = $type;
            $data['items'] = [];
            if ($type == 'card') {
                if ($request->has('selectedCard_'.$tag)) {
                    $data['items'][] = $request->get('selectedCard_'.$tag);
                }
            }
            elseif ($type == 'cardset') {
                if ($request->has('selectedSet_'.$tag)) {
                    $data['items'][] = $request->get('selectedSet_'.$tag);
                }
            }
            elseif ($type == 'dataset' || $type == 'dataset_list') {
                if ($request->has('selectedDataset_'.$tag)) {
                    if ($type == 'dataset') {
                        $data['items'][] = $request->get('selectedDataset_' . $tag);
                    }
                    else {
                        $list = $request->get('selectedDataset_' . $tag);
                        foreach ($list as $item) {
                            $data['items'][] = $item;
                        }
                    }
                }
            }
            $dataBundle[$tag] = $data;
        }
        $this->pageComponent->setProperty('data', $dataBundle);
        $this->pageComponent->save();
        return redirect ("/build/$slug/pages/$pageId");
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
