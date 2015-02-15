<?php


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

}