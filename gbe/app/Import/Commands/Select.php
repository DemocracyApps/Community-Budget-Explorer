<?php

namespace DemocracyApps\GB\Import\Commands;


class Select extends DPCommand {

    private $specification = null;

    public function __construct($specification)
    {
        $this->specification = $specification;
    }

    /**
     * @param string[] $input
     * @return \string[] $output
     */
    public function process($input)
    {
        $counts = array();
        // TODO: Could implement this as preg_grep, no?
        $output = array();
        foreach ($input as $line) {
            $matches = array();
            if (preg_match($this->specification, $line, $matches)) {
                if (true)
                    $output[] = $line;
                else
                 $output[] = $matches[10];
                $size = sizeof($matches);
                if (! array_key_exists($size,$counts)) $counts[$size] = 0;
                ++ $counts[$size];
            }
        }
        foreach ($counts as $count=>$instances) {
            echo "Count " . $count . ": " . $instances . PHP_EOL;
        }

        return $output;
    }
}