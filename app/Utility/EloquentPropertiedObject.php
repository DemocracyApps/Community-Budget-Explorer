<?php namespace DemocracyApps\GB\Utility;
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

class EloquentPropertiedObject extends \Eloquent
{
    protected $properties = [];

    public function __construct()
    {
        parent::__construct();

        self::saving(function ($dbObject) {
            $this->attributes['properties'] = json_encode($this->properties);
        });

    }

    public function newFromBuilder($attributes = array(), $connection = NULL)
    {
        $instance = parent::newFromBuilder($attributes, $connection);
        if (array_key_exists('properties', $instance->attributes)) {
            $instance->properties = json_decode($instance->attributes['properties'], true);
        }
        return $instance;
    }

    public function setProperty ($propName, $propValue)
    {
        $this->properties[$propName] = $propValue;
    }

    public function hasProperty ($propName)
    {
        $hasProperty = false;
        if ($this->properties) {
            if (array_key_exists($propName, $this->properties)) {
                $hasProperty = true;
            }
        }
        return $hasProperty;
    }

    public function getProperty ($propName)
    {
        $propValue = null;
        if ($this->properties && array_key_exists($propName, $this->properties)) {
            $propValue = $this->properties[$propName];
        }
        return $propValue;
    }



}