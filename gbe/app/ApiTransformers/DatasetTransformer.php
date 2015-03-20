<?php namespace DemocracyApps\GB\ApiTransformers;
use DemocracyApps\GB\Accounts\Account;
use DemocracyApps\GB\Accounts\AccountCategory;
use DemocracyApps\GB\Accounts\AccountCategoryValue;
use DemocracyApps\GB\Accounts\DataItem;
use DemocracyApps\GB\Accounts\Dataset;

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

class DatasetTransformer extends ApiTransformer {
    private $granularities;
    private $accountMap;
    private $categoriesMap;

    public function __construct ()
    {
        $this->granularities = array();
        $this->granularities[Dataset::ANNUAL] = "Year";
        $this->granularities[Dataset::DAILY] = "Day";
        $this->granularities[Dataset::MONTHLY] = "Month";
    }

    private function createMaps($dataset) {
        if ($this->accountMap == null) $this->accountMap = array();
        if ($this->categoriesMap == null) $this->categoriesMap = array();

        $accounts = Account::where('chart','=',$dataset->chart)->get();
        $map = array();

        foreach($accounts as $account) {
            $map[$account->id] = $account->name;
        }
        $this->accountMap[$dataset->chart] = $map;

        $categoryTypes = AccountCategory::where('chart', '=', $dataset->chart)->get();
        foreach ($categoryTypes as $type) {
            $map = array();
            $cats = AccountCategoryValue::where('category','=',$type->id)->get();
            foreach($cats as $cat) {
                $map[$cat->id] = $cat->name;
            }

            $this->categoriesMap[$type->id]['name'] = $type->name;
            $this->categoriesMap[$type->id]['map'] = $map;
        }
    }

    /**
     * @param $dataset
     * @param array $parameters
     * @return array
     */
    public function transform($dataset, array $parameters)
    {
        if (array_key_exists('includeData',$parameters) && $parameters['includeData'] == false) {
            return [
                'id' => $dataset->id,
                'name' => $dataset->name,
                'type' => $dataset->type,
                'granularity' => $this->granularities[$dataset->granularity],
                'year' => $dataset->year,
                'month' => $dataset->month,
                'day' => $dataset->day,
                'description' => $dataset->description
            ];
        }
        else {
            $mapping = true;
            if (array_key_exists('noMapping',$parameters)) {
                $mapping = !$parameters['noMapping'];
            }
            if ($mapping) {
                if ($this->accountMap == null) {
                    $this->createMaps($dataset);
                }
            }
            $categoryOrder = null;
            $allCategories = array();
            if ($dataset->category_order != null) {
                $categoryOrder = json_decode($dataset->category_order);
                foreach ($categoryOrder as $cat) {
                    if ($mapping) {
                        $allCategories[] = $this->categoriesMap[$cat]['name'];
                    }
                    else {
                        $allCategories[] = $cat;
                    }
                }
            }

            $dataItems = DataItem::where('dataset','=',$dataset->id)->get();

            $data = array();
            foreach ($dataItems as $item) {
                $account = $item->account;
                $categories = array();
                if ($mapping) {
                    $account = $this->accountMap[$dataset->chart][$item->account];

                    if ($item->category1 == null) {
                        $categories[] = null;
                    }
                    else {
                        $categories[] = $this->categoriesMap[$categoryOrder[0]]['map'][$item->category1];
                    }
                    if ($item->category2 == null) {
                        $categories[] = null;
                    }
                    else {
                        $categories[] = $this->categoriesMap[$categoryOrder[1]]['map'][$item->category2];
                    }
                    if ($item->category3 == null) {
                        $categories[] = null;
                    }
                    else {
                        $categories[] = $this->categoriesMap[$categoryOrder[2]]['map'][$item->category3];
                    }
                    if ($item->categoryN != null) {
                        $rem = json_decode($item->categoryN);
                        for ($i=0; $i<sizeof($rem); ++$i) {
                            if ($rem[$i] == null) {
                                $categories[] = null;
                            }
                            else {
                                $categories[] = $this->categoriesMap[$categoryOrder[3+$i]]['map'][$rem[$i]];
                            }
                        }
                    }
                }
                else {
                    $categories[] = $item->category1;
                    $categories[] = $item->category2;
                    $categories[] = $item->category3;
                    if ($item->categoryN != null) {
                        $rem = json_decode($item->categoryN);
                        foreach ($rem as $r) $categories[] = $r;
                    }
                }
                $val = [
                    'account'=>$account,
                    'categories'=>$categories,
                    'amount' => $item->amount
                ];
                $data[] = $val;
            }
            return [
                'id' => $dataset->id,
                'name' => $dataset->name,
                'type' => $dataset->type,
                'granularity' => $this->granularities[$dataset->granularity],
                'year' => $dataset->year,
                'month' => $dataset->month,
                'day' => $dataset->day,
                'description' => $dataset->description,
                'categoryIdentifiers' => $allCategories,
                'data' => $data
            ];
        }
    }
}