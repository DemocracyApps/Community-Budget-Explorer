<?php namespace DemocracyApps\GB\Services;

require_once base_path().'/vendor/JSON.minify/minify.json.php';


class JsonProcessor
{

    public function minifyJson($s)
    {
        return json_minify($s);
    }

    public function decodeJson($s, $b)
    {
        return json_decode($s, true);
    }
}