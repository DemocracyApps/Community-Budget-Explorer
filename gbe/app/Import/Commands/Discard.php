<?php
/**
 * Created by PhpStorm.
 * User: ericjackson
 * Date: 2/12/15
 * Time: 5:20 PM
 */

namespace DemocracyApps\GB\Import\Commands;


class Discard extends DPCommand {

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
            if (! preg_match($this->specification, $line)) {
                $output[] = $line;
            }
        }

        return $output;
    }

}