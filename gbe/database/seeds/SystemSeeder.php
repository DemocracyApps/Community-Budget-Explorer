<?php

use DemocracyApps\GB\Services\JsonProcessor;
use DemocracyApps\GB\Sites\Component;
use DemocracyApps\GB\Sites\Layout;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{


    public function run()
    {

        // Read the layouts directory
        $path = base_path()."/../layouts";
        $lFiles = scandir($path);
        $jp = new JsonProcessor();

        foreach ($lFiles as $file) {
            \Log::info("The file is " . $file);
            if (ends_with ($file, '.json')) {
                $s = file_get_contents("$path/$file");

                $str = $jp->minifyJson($s);
                $cfig = $jp->decodeJson($str, true);
                if ( ! $cfig) {
                    throw new \Exception("Error reading layout file " . $file);
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
        // Create the base components
        $c = new Component();
        $c->name = "SimpleCard";
        $c->owner = 1;
        $c->description="Just a simple display of a single card";
        $c->type = Component::SYSTEM;
        $c->save();

        $c = new Component();
        $c->name = "SlideShow";
        $c->owner = 1;
        $c->description="Carousel slideshow of pictures with title, text and link";
        $c->type = Component::SYSTEM;
        $c->save();
    }

}