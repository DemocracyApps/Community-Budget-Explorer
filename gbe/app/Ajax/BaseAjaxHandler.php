<?php namespace DemocracyApps\GB\Ajax;
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
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class BaseAjaxHandler {

    abstract static function handle ($func, Request $request);

    protected static function oKResponse ($message, $data) {
        $resp = [
            'message' => $message,
            'status_code'	=> Response::HTTP_OK,
            'data' => $data
        ];
        return $resp;
    }

    protected static function errorResponse ($code, $message) {
        $resp = [
            'status_code'	=> $code,
            'error' => [
                'message' 		=> $message,
                'status_code'	=> $code
            ]
        ];
        return $resp;
    }

    protected static function notFoundResponse ($message) {
        return self::errorResponse(Response::HTTP_NOT_FOUND, $message);
    }

    protected static function formatErrorResponse ($message) {
        return self::errorResponse(Response::HTTP_BAD_REQUEST, $message);
    }
}