<?php namespace DemocracyApps\GB\Accounts;
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
use DemocracyApps\GB\Utility\EloquentPropertiedObject;

class DataItem extends EloquentPropertiedObject
{
    protected $table = 'data_items';

    public function addCategories ($categories) {
        $size = sizeof($categories);
        if ($size>0) $this->category1 = $categories[0];
        if ($size>1) $this->category2 = $categories[1];
        if ($size>2) $this->category3 = $categories[2];
        if ($size>3) {
            $spill = array();
            for ($i=0; $i<$size-3; ++$i) {
                $spill[] = $categories[3+$i];
            }
            $this->categoryN = json_encode($spill);
        }
    }



}