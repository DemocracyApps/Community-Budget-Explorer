<?php namespace DemocracyApps\GB\Http\Controllers\Build;

use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Sites\CardSet;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Http\Request;

class CardSetsController extends Controller
{

    protected $cardset;

    public function __construct(CardSet $cardset)
    {
        $this->cardset = $cardset;
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
	public function create($slug)
	{
        $site = Site::where('slug','=',$slug)->first();
		return view('build.cardsets.create', ['site'=>$site]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($slug, Request $request)
	{
        $rules = ['name'=>'required'];
        $this->validate($request, $rules);

        $site = Site::where('slug','=',$slug)->first();
        $this->cardset->name = $request->get('name');
        $this->cardset->site = $site->id;
        $this->cardset->save();
        $id = $this->cardset->id;
        return redirect("/build/$slug/content?selectedSet=$id");
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
