<?php

use DemocracyApps\GB\Services\JsonProcessor;
use DemocracyApps\GB\Sites\Component;
use DemocracyApps\GB\Sites\Layout;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{


    public function run()
    {

        $jp = new JsonProcessor();

        // Read the layouts directory
        $path = base_path()."/resources/definitions/layouts";
        $lFiles = scandir($path);
        foreach ($lFiles as $file) {
            \Log::info("Importing layout from " . $file);
            if (ends_with ($file, '.json')) {
                echo "Importing layout from " . $file . PHP_EOL;

                $s = file_get_contents("$path/$file");

                $str = $jp->minifyJson($s);
                $cfig = $jp->decodeJson($str, true);
                if ( ! $cfig) {
                    throw new \Exception("Error parsing layout file " . $file);
                }
                $layout = new Layout();
                if (array_key_exists('name', $cfig)) {
                    $layout->name = $cfig['name'];
                }
                else {
                    $layout->name = $file;
                }
                $layout->specification = $s;
                if (array_key_exists('description', $cfig)) {
                    $layout->description = $cfig['description'];
                }
                $layout->owner = 1;
                $layout->type = Layout::BOOTSTRAP;
                $layout->public = true;
                $layout->save();
            }
        }

        // Read the components directory
        $path = base_path()."/resources/definitions/components";
        $lFiles = scandir($path);
        foreach ($lFiles as $file) {
            \Log::info("Importing component from " . $file);
            if (ends_with ($file, '.json')) {
                echo "Importing component from " . $file . PHP_EOL;
                $s = file_get_contents("$path/$file");

                $str = $jp->minifyJson($s);
                $cfig = $jp->decodeJson($str, true);
                if ( ! $cfig) {
                    throw new \Exception("Error parsing component file " . $file);
                }
                $c = new Component();

                if (array_key_exists('name', $cfig)) {
                    $c->name = $cfig['name'];
                }
                else {
                    $c->name = $file;
                }
                if (array_key_exists('description', $cfig)) {
                    $c->description = $cfig['description'];
                }
                $c->owner = 1;
                $c->type = Component::SYSTEM;

                $c->setProperty('data', $cfig['data']);
                if (array_key_exists('props', $cfig)) $c->setProperty('props', $cfig['props']);
                if (array_key_exists('state', $cfig)) $c->setProperty('state', $cfig['state']);
                $c->save();
            }
        }
    }

}