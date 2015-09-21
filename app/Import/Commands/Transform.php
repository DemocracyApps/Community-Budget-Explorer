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