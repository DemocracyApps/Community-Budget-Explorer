<?php namespace DemocracyApps\GB\Import\Commands;
/**
 *
 * This file is part of the Government Budget Explorer (GBE).
 *
 *  The GBE is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GBE is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the GBE.  If not, see <http://www.gnu.org/licenses/>.
 */

class Merge extends DPCommand {
    private $specification = null;
    private $trigger = null;
    private $before = null, $after = null;
    private $select = null;
    private $separator = "";

    public function __construct($specification)
    {
        $this->specification = $specification;
        $this->trigger = $specification['trigger'];
        $this->before = $specification['before'];
        $this->after = $specification['after'];
        if (array_key_exists('select', $specification)) {
            $this->select = $specification['select'];
        }
        if (array_key_exists('separator', $specification)) {
            $this->separator = $specification['separator'];
        }
    }

    /**
     * @param \string[] $input
     * @return \string[] $output
     */
    public function process($input)
    {
        $output = array();
        for ($i=0; $i<sizeof($input); ++$i) {
            $line = $input[$i];
            if (preg_match($this->trigger, $line)) {
                $matched = array();
                if ($this->before != null && $this->before > 0) {
                    for ($j=0; $j<$this->before; ++$j) {
                        $matched[] = $input[$i-$this->before + $j];
                    }
                }
                $matched[] = $line;
                if ($this->after != null && $this->after > 0) {
                    for ($j=0; $j<$this->after; ++$j) {
                        $matched[] = $input[$i+1+$j];
                    }
                }
                /*
                 * Now output
                 */
                $outline = "";
                $glue = "";
                if ($this->select == null) { // Just concatenate
                    foreach ($matched as $match) {
                        $outline .= $glue . $match;
                        $glue = $this->separator;
                    }
                }
                else {
                    foreach ($this->select as $which) {
                        $outline .= $glue . $matched[$which];
                        $glue = $this->separator;
                    }
                }
                $output[] = $outline;
            }

        }

        return $output;
    }

}