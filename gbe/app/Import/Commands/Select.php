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
     * @param $specification
     * @return \string[] $output
     */
    public function process($input)
    {

        $output = array();
        foreach ($input as $line) {
            if (preg_match($this->specification, $line)) {
                $output[] = $line;
            }
        }

        return $output;
    }
}