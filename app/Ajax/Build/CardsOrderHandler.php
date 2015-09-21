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
use DemocracyApps\GB\Sites\Card;
use Illuminate\Http\Request;

class CardsOrderHandler extends BaseAjaxHandler
{

    /**
     * @param $func
     * @param Request $request
     * @return array|null
     */
    static function handle($func, Request $request)
    {
        if ($func == "setOrdinals") {
            return self::setOrdinals($request);
        } else {
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
                $card = Card::find($change->id);
                if ($card == null)
                    $fails++;
                else {
                    $card->ordinal = $change->ord;
                    $card->save();
                    $successes++;
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

}