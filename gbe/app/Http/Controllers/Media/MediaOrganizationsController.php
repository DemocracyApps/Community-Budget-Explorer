<?php namespace DemocracyApps\GB\Http\Controllers\Media;
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
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Organizations\MediaOrganization;
use Illuminate\Http\Request;

class MediaOrganizationsController extends Controller {

    protected $organization = null;

    function __construct (MediaOrganization $org)
    {
        $this->organization = $org;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        return view('media.create');
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

        $this->organization->name = $request->get('name');
        if ($request->has('description')) {
            $this->organization->description = $request->get('description');
        }
        $this->organization->save();
        \Log::info("Now go to /media/$this->organization->id");
        return redirect('/media/'.$this->organization->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $org = MediaOrganization::find($id);
        if ($org == null) return redirect('/system/media');
        return view("media.show", array('organization' => $org));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id, Request $request)
    {
        $org = MediaOrganization::find($id);
        if ($org == null) return redirect('/system/media');
        return view('media.edit', array('organization' => $org));
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

        $this->organization = MediaOrganization::find($id);
        $this->organization->name = $request->get('name');
        if ($request->has('description')) {
            $this->organization->description = $request->get('description');
        }
        $this->organization->save();

        return redirect('/media/'.$this->organization->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $org = MediaOrganization::find($id);
        if ($org != null) {
            $org->delete();
        }
        return redirect('/system/media');
    }

}