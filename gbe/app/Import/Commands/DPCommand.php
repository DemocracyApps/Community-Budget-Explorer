<?php

namespace DemocracyApps\GB\Import\Commands;


abstract class DPCommand {


    abstract public function __construct($specification);

    /**
     * @param string[] $input
     * @param $specification
     * @return \string[] $output
     */
    abstract public function process ($input);
}