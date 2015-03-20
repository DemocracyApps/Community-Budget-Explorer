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

        return $output;
    }
}