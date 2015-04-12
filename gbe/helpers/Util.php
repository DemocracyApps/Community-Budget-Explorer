<?php
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

class Util {

    public static function viewPath ($base) {
        $path = base_path().'/resources/views';
        if ($base != null) {
            $path .= '/'.$base;
        }
        return $path;
    }
    public static function viewAsset ($base) {
        $path = base_path().'/resources/views';
        if ($base != null) {
            $path .= '/'.$base;
        }
        return $path;
    }

    public static function ajaxPath($section = null, $page = null) {
        $path = url('ajax');
        if ($section != null) $path .= '/' . $section;
        if ($page != null) $path .= '/' . $page;
        return $path;
    }

    public static function apiPath() {
        $path = url('api/v1');
        return $path;
    }

}