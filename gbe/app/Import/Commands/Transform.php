<?php

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
        foreach ($input as $line) {
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