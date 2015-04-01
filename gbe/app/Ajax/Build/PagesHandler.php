<?php namespace DemocracyApps\GB\Ajax\Build;

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
use DemocracyApps\GB\Sites\Page;
use Illuminate\Http\Request;

class PagesHandler extends BaseAjaxHandler
{

    /**
     * @param $func
     * @param Request $request
     * @return array|null
     */
    static function handle($func, Request $request)
    {
        if ($func == "show_in_menu") {
            return self::show_in_menu($request);
        }
        elseif ($fun = "setOrdinals") {
            return self::setOrdinals($request);
        }
        else {
            return self::notFoundResponse("Ajax function " . $func . " not found in MediaAdmin.SitesHandler");
        }

        return null;
    }


    private static function setOrdinals(Request $request)
    {

        if (!$request->has('changes')) return self::formatErrorResponse('No change specified');
        $changeString = $request->get('changes');
        $changes = json_decode($changeString);

        $fails = 0;
        $successes = 0;
        try {
            foreach ($changes as $change) {
                $page = Page::find($change->id);
                if ($page == null)
                    $fails++;
                else {
                    if ($page->ordinal != $change->ord) {
                        $page->ordinal = $change->ord;
                        $page->save();
                        $successes++;
                    }
                }
            }
            $resp = "Successfully saved order changes.";
        }
        catch (\Exception $ex) {
            $resp = "There were errors attempting to save order changes.";
            return self::formatErrorResponse($resp);
        }
        if ($fails > 0) {
            $resp = "There were errors attempting to save order changes.";
            return self::formatErrorResponse($resp);
        }

        return self::oKResponse($resp, null);

    }
    private static function show_in_menu(Request $request)
    {
        if (!$request->has('page')) return self::formatErrorResponse('No page specified');

        if (!$request->has('show')) return self::formatErrorResponse('No specification of show state');
        $page = Page::find($request->get('page'));
        if (!$page) return self::notFoundResponse('Page not found (ID = '.$request->get('page').')');
        $val = $request->get('show');
        $resp = "";
        if (strtolower($val) == 'true') {
            $page->show_in_menu = true;
            $resp = $page->title . " will show in menu.";
        }
        else {
            $page->show_in_menu = false;
            $resp = $page->title . " will not show in menu.";
        }
        $page->save();

        return self::oKResponse($resp, null);

    }

}