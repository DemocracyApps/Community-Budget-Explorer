<?php

namespace DemocracyApps\GB\Import\Commands;


class PluckColumns extends DPCommand {

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
        for ($i=0; $i<sizeof($this->specification); ++$i) {
            $this->specification[$i] -= 1;
        }

        $output = array();
        foreach ($input as $line) {
            $cols = str_getcsv($line);
            $outline = "";
            $separator = "";
            foreach ($this->specification as $spec) {
                $outline .= $separator . $cols[$spec];
                $separator = ",";
            }
            $output[] = $outline;
        }

        return $output;
    }
}