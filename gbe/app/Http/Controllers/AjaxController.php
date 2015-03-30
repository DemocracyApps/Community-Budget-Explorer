<?php namespace DemocracyApps\GB\Http\Controllers;
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

use DemocracyApps\GB\Http\Controllers\API\APIController;
use DemocracyApps\GB\Http\Requests;

use Illuminate\Http\Request;

class AjaxController extends APIController {


    public function main($section, $page, $func, Request $request)
    {
        \Log::info("In ajax controller with section " . $section);
        $className = "\\DemocracyApps\\GB\\Ajax\\" . ucfirst($section) . "\\" . ucfirst($page). "Handler";

        $reflectionMethod = new \ReflectionMethod($className, 'handle');
        $response = $reflectionMethod->invokeArgs(null, array($func, $request));
        return $this->setStatusAndRespond($response);
    }

}
