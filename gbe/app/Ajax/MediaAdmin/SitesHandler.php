<?php namespace DemocracyApps\GB\Ajax\MediaAdmin;

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

use DemocracyApps\GB\Ajax\BaseAjaxHandler;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Http\Request;

class SitesHandler extends BaseAjaxHandler
{

    /**
     * @param $func
     * @param Request $request
     * @return array|null
     */
    static function handle($func, Request $request)
    {
        if ($func == "publish") {
            return self::publish($request);
        } else {
            return self::notFoundResponse("Ajax function " . $func . " not found in MediaAdmin.SitesHandler");
        }

        return null;
    }


    private static function publish(Request $request)
    {
        if (!$request->has('site')) return self::formatErrorResponse('No site specified');

        if (!$request->has('published')) return self::formatErrorResponse('No specification of published state');
        $site = Site::find($request->get('site'));
        if (!$site) return self::notFoundResponse('Site not found (ID = '.$request->get('site').')');
        $val = $request->get('published');
        $resp = "";
        if (strtolower($val) == 'true') {
            $site->published = true;
            $resp = $site->name . " has been published.";
        }
        else {
            $site->published = false;
            $resp = $site->name . " has been unpublished.";
        }
        $site->save();

        return self::oKResponse($resp, null);

    }

}