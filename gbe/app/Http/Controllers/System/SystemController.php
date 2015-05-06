<?php namespace DemocracyApps\GB\Http\Controllers\System;
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
use DemocracyApps\GB\Budget\AccountChart;
use DemocracyApps\GB\Http\Controllers\Controller;
use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Organizations\MediaOrganization;
use DemocracyApps\GB\Services\JsonProcessor;
use DemocracyApps\GB\Sites\Component;
use DemocracyApps\GB\Sites\Layout;
use DemocracyApps\GB\Users\User;
use Illuminate\Http\Request;

class SystemController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function settings(Request $request)
    {
        return view('system.settings', array());
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function users(Request $request)
    {
        $users = User::orderBy('id')->get();
        return view('system.users', array('users' => $users));
    }

    public function governments(Request $request)
    {
        $organizations = GovernmentOrganization::orderBy('id')->get();
        return view('system.governments', array('organizations' => $organizations));
    }

    /**
     * Show the form for creating a new government.
     *
     * @return Response
     */
    public function createGovernment(Request $request)
    {
        return view('system.government.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function storeGovernment(Request $request)
    {
        $rules = ['name' => 'required'];
        $this->validate($request, $rules);

        $organization = new GovernmentOrganization();
        $organization->name = $request->get('name');
        $organization->save();

        // Now create the default chart of accounts
        $chart = new AccountChart();
        $chart->name = "default";
        $chart->government_organization = $organization->id;
        $chart->save();
        return redirect('/system/governments');
    }

    public function media(Request $request)
    {
        $organizations = MediaOrganization::orderBy('id')->get();
        return view('system.media', array('organizations' => $organizations));
    }

    /**
     * Show the form for creating a new government.
     *
     * @return Response
     */
    public function createMedia(Request $request)
    {
        return view('system.media.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function storeMedia(Request $request)
    {
        $rules = ['name' => 'required'];
        $this->validate($request, $rules);

        $organization = new MediaOrganization();
        $organization->name = $request->get('name');
        $organization->save();

        return redirect("/media/$organization->id");
    }

    public function layouts()
    {
        $layouts = Layout::orderBy('id')->get();
        return view('system.layouts', array('layouts' => $layouts));
    }

    public function createLayout()
    {
        return view('system.layouts.create');
    }

    public function storeLayout(Request $request)
    {
        $rules = ['name' => 'required'];
        $this->validate($request, $rules);

        $layout = new Layout();
        $layout->name = $request->get('name');
        if ($request->has('description')) $layout->description = $request->get('description');

        $layout->public = true;
        $layout->owner = \Auth::user()->id;
        // Now load in the file
        if ($request->hasFile('specification')) {
            $specification = $this->loadLayout($request->file('specification'));
            if ($specification == null) {
                return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
            }
            $layout->specification = $specification;
        }

        $layout->type = Layout::BOOTSTRAP;
        $layout->owner = \Auth::user()->id;
        $layout->public = true;


        $layout->save();
        return redirect('/system/layouts');
    }

    public function editLayout($id)
    {
        $layout = Layout::find($id);
        return view('system.layouts.edit', array('layout' => $layout));
    }

    public function updateLayout($id, Request $request)
    {
        $rules = ['name' => 'required'];
        $this->validate($request, $rules);

        $layout = Layout::find($id);
        $layout->name = $request->get('name');
        if ($request->has('description')) $layout->description = $request->get('description');

        // Now load in the file
        if ($request->hasFile('specification')) {
            $specification = $this->loadLayout($request->file('specification'));
            if ($specification == null) {
                return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
            }
            $layout->specification = $specification;
        }

        $layout->save();
        return redirect('/system/layouts');
    }

    private function loadLayout($file)
    {
        $jp = new JsonProcessor();

        $specification = \File::get($file->getRealPath());

        $str = $jp->minifyJson($specification);
        $cfig = $jp->decodeJson($str, true);
        if (!$cfig) {
            return null;
        }
        return $specification;
    }

    private function loadComponentSpecification($file)
    {
        $jp = new JsonProcessor();

        $specification = \File::get($file->getRealPath());

        $str = $jp->minifyJson($specification);
        $cfig = $jp->decodeJson($str, true);
        if (!$cfig) {
            return null;
        }
        return $cfig;
    }

    public function components()
    {
        $components = Component::orderBy('id')->get();
        return view('system.components', array('components' => $components));
    }

    public function createComponent()
    {
        return view('system.components.create');
    }

    public function storeComponent(Request $request)
    {
        $rules = ['name' => 'required'];
        $this->validate($request, $rules);

        $component = new Component();
        $component->name = $request->get('name');
        if ($request->has('description')) $component->description = $request->get('description');

        // Now load in the file
        if ($request->hasFile('specification')) {
            $specification = $this->loadComponentSpecification($request->file('specification'));
            if ($specification == null) {
                return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
            }
            if (array_key_exists('data', $specification)) {
                $component->setProperty('data', $specification['data']);
            }

        }

        $component->type = Component::CORE;
        $component->owner = \Auth::user()->id;

        $component->save();
        return redirect('/system/components');
    }

    public function showComponent($id)
    {
        $component = Component::find($id);
        return view('system.components.show', array('component' => $component));
    }

    public function editComponent($id)
    {
        $component = Component::find($id);
        return view('system.components.edit', array('component' => $component));
    }

    public function deleteComponent($id)
    {
        $component = Component::find($id);
        $component->delete();
        return redirect('/system/components');
    }

    public function updateComponent($id, Request $request)
    {
        $rules = ['name' => 'required'];
        $this->validate($request, $rules);

        $component = Component::find($id);
        $component->name = $request->get('name');
        if ($request->has('description')) {
            $component->description = $request->get('description');
        }
        else {
            $component->description = null;
        }

        // Now load in the file
        if ($request->hasFile('specification')) {
            $specification = $this->loadComponentSpecification($request->file('specification'));
            if ($specification == null) {
                return \Redirect::back()->withInput()->withErrors(array('fileerror' => 'JSON not well-formed'));
            }
            if (array_key_exists('data', $specification)) {
                $component->setProperty('data', $specification['data']);
            }

        }

        $component->save();
        return redirect('/system/components');
    }
}
