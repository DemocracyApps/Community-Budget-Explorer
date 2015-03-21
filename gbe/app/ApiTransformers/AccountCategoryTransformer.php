<?php namespace DemocracyApps\GB\ApiTransformers;
use DemocracyApps\GB\Accounts\AccountCategoryValue;

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

class AccountCategoryTransformer extends ApiTransformer {

    /**
     * @param $category
     * @param array $parameters
     * @return array
     */
    public function transform($category, array $parameters)
    {
        if (array_key_exists('includeData',$parameters) && $parameters['includeData'] == false) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'chart' => $category->chart,
                'description' => $category->description
            ];
        }
        else {
            $results = AccountCategoryValue::where('category','=',$category->id)->get();
            $items = array();
            foreach ($results as $result) {
                $items[] = [
                    'id'=>$result->id,
                    'name' => $result->name,
                    'code' => $result->code
                ];
            }
            return [
                'id' => $category->id,
                'name' => $category->name,
                'chart' => $category->chart,
                'description' => $category->description,
                'items' => $items
            ];

        }
    }
}