<?php
/**
 * Created by PhpStorm.
 * User: ericjackson
 * Date: 2/13/15
 * Time: 10:39 AM
 */

namespace DemocracyApps\GB\Import\Commands;


class Transform extends DPCommand{

    private $specification = null;
    private $pattern = null;
    private $replacement = null;
    private $limit = null;

    public function __construct($specification)
    {
        $this->specification = $specification;
        $this->pattern = $specification['pattern'];
        $this->replacement = $specification['replacement'];
        if (array_key_exists('limit', $specification)) {
            $this->limit = $specification['limit'];
        }
    }

    /**
     * @param \string[] $input
     * @return \string[] $output
     */
    public function process($input)
    {
        $output = array();
        $i = 0;
        foreach ($input as $line) {
           // $output[] = preg_replace($this->pattern, $this->replacement, $line, $this->limit);
            //$output[] = preg_replace ("/^(,,,,)/", "ABC $1 ", $line);
            if ($this->limit != null) {
                $output[] = preg_replace ($this->pattern, $this->replacement, $line, $this->limit);
            }
            else {
                $output[] = preg_replace ($this->pattern, $this->replacement, $line);
            }
        }

        return $output;
    }

}