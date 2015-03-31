<?php namespace DemocracyApps\GB\Http\Controllers\Build;

use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Sites\Card;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Http\Request;

class CardsController extends Controller {
    protected $card;

    public function __construct (Card $card)
    {
        $this->card = $card;
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
    public function create($slug, Request $request)
    {
        $site = Site::where('slug','=',$slug)->first();
        if (! $request->has('cardSet')) return redirect ("/build/$slug/cards");
        return view('build.cards.create', ['site'=>$site, 'cardSet'=>$request->get('cardSet')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($slug, Request $request)
    {
        $rules = ['title'=>'required'];
        $this->validate($request, $rules);

        $site = Site::where('slug','=',$slug)->first();
        $this->card->title = $request->get('title');
        if ($request->has('body')) $this->card->body = $request->get('body');
        $this->card->card_set = $request->get('cardSet');
        $this->card->site = $site->id;
        $this->card->save();
        $this->card->ordinal = $this->card->id;
        $this->card->save(); // Not sure how else to do this.

        $id = $this->card->card_set;
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
