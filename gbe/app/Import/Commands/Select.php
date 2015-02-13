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
        // TODO: Could implement this as preg_grep, no?
        $output = array();
        foreach ($input as $line) {
            $matches = array();
            if (preg_match($this->specification, $line, $matches)) {
               $output[] = $line;
               // $output[] = $matches[0];
            }
        }

        return $output;
    }
}