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

        if ($request->has('link')) $this->card->link = $request->get('link');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $mtype = $file->getMimeType();
            $ext = '.jpg';
            if ($mtype == 'image/png') {
                $ext = '.png';
            }
            $name = uniqid('pic') . $ext;
            $path = public_path().'/img/cards/'.$name;
            \Image::make(\Input::file('image'))->save($path);
            $this->card->image = '/img/cards/'.$name;
            if (config('gbe.image_storage_filesystem') == 's3') {
                throw new Exception("Need to fix the image path for S3 storage in CardsController");
                $picture = \File::get($path);
                $disk = \Storage::disk('s3');
                $disk->put($name, $picture);
                unlink($path);
            }
        }

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
	public function edit($slug, $id, Request $request)
	{
        $site = Site::where('slug','=',$slug)->first();
        if (! $request->has('cardSet')) return redirect ("/build/$slug/cards");
        $this->card = Card::find($id);
        return view('build.cards.edit', ['site'=>$site, 'card'=>$this->card, 'cardSet'=>$request->get('cardSet')]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($slug, $id, Request $request)
    {
        $rules = ['title'=>'required'];
        $this->validate($request, $rules);

        $site = Site::where('slug','=',$slug)->first();

        $this->card = Card::find($id);

        $this->card->title = $request->get('title');
        if ($request->has('body'))
            $this->card->body = $request->get('body');
        else
            $this->card->body = null;

        $this->card->card_set = $request->get('cardSet');
        $this->card->site = $site->id;

        if ($request->has('link'))
            $this->card->link = $request->get('link');
        else
            $this->card->link = null;

        if ($request->hasFile('image')) {
            if ($this->card->image != null) {
                if (config('gbe.image_storage_filesystem') == 's3') {
                    $disk = \Storage::disk('s3');
                    $disk->delete($this->card->image);
                }
                else {
                    $path = public_path().'/img/cards/'.$this->card->image;
                    //unlink($path);
                }
            }
            $file = $request->file('image');
            $mtype = $file->getMimeType();
            $ext = '.jpg';
            if ($mtype == 'image/png') {
                $ext = '.png';
            }
            $name = uniqid('pic') . $ext;
            $path = public_path().'/img/cards/'.$name;
            \Image::make(\Input::file('image'))->save($path);
            if (config('gbe.image_storage_filesystem') == 's3') {
                throw new Exception("Need to fix the image path for S3 storage in CardsController");
                $picture = \File::get($path);
                $disk = \Storage::disk('s3');
                $disk->put($name, $picture);
                unlink($path);
            }
            $this->card->image = '/img/cards/'.$name;
        }

        $this->card->save();

        $id = $this->card->card_set;
        return redirect("/build/$slug/content?selectedSet=$id");
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
