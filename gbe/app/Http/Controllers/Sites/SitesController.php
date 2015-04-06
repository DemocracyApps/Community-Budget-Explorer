<?php namespace DemocracyApps\GB\Http\Controllers\Sites;

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

use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Services\JsonProcessor;
use DemocracyApps\GB\Sites\Layout;
use DemocracyApps\GB\Sites\Page;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Http\Request;

class SitesController extends Controller {

	public function page($slug, $pageName, Request $request)
    {
        $site = Site::where('slug','=',$slug)->first();

        $page = Page::where('short_name','=',$pageName)->where('site','=',$site->id)->first();


        $layout = ($page->layout == null)?null:Layout::find($page->layout);

        $jp = new JsonProcessor();

        $str = $jp->minifyJson($layout->specification);
        $cfig = $jp->decodeJson($str, true);
        if ( ! $cfig) {
            throw new \Exception("Unable to part layout specification " . $layout->name);
        }
        $layout->specification = $cfig;

        $pages = Page::where('site','=',$site->id)->where('show_in_menu','=',true)->orderBy('ordinal')->get();
        return view('sites.page', array('site'=>$site, 'pages'=>$pages, 'page'=>$page, 'layout'=>$layout));
    }

}
