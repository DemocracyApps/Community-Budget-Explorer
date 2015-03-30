<?php namespace DemocracyApps\GB\Budget;

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

class AccountCategory extends EloquentPropertiedObject
{
    protected  $table = 'account_categories';

    static public function allOrganizationCategories ($orgId) {
        $items = \DB::table('account_categories')
            ->join('account_charts', 'account_categories.chart', '=', 'account_charts.id')
            ->where('account_charts.organization','=',$orgId)
            ->select('account_categories.id', 'account_categories.name',
                    'account_categories.chart', 'account_categories.description')
            ->get();
        return $items;
    }


    static public function processCSVInput($filePath, $category)
    {
        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings
        if ( !file_exists($filePath)) {
            \Log::info("AccountCategory.processCSVInput: The file " . $filePath . " does not exist");
        }
        $myFile = fopen($filePath,"r") or die ("Unable to open file");
        $badLines = 0;
        $records = array();
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $line = fgetcsv($myFile);
        $lnum = 1;
        while (! feof($myFile)) {
            $columns = fgetcsv($myFile);
            ++$lnum;
            if (sizeof($columns) == 2) {
                $code = strip_tags(trim($columns[0]));
                // See if there's already an account
                $categoryValue = AccountCategoryValue::where('category', '=', $category)
                    ->where('code', '=',$code)->first();
                if ($categoryValue == null) {
                    $records[] = [
                        'code'=>$code,
                        'name'=>strip_tags(trim($columns[1])),
                        'category'=>$category,
                        'created_at' => $created_at,
                        'updated_at' => $updated_at
                    ];
                    if (sizeof($records) >= 999) {
                        \DB::table('account_category_values')->insert($records);
                        $records = array();
                    }

                    $categoryValue = new AccountCategoryValue();

                    $categoryValue->code = $code;
                    $categoryValue->name = strip_tags(trim($columns[1]));
                    $categoryValue->category = $category;
                    $categoryValue->save();
                }
            }
            else {
                $val = trim($columns[0]);
                if (($val != null && strlen($val) > 0) || sizeof($columns) > 1) {
                    \Log::info("Invalid line $lnum: " . json_decode($columns));
                    ++$badLines;
                }
            }
        }
        if (sizeof($records)> 0) \DB::table('account_category_values')->insert($records);
        return $badLines>0?"Processed account categories file with $lnum lines - total bad lines = " . $badLines:null;
    }

}
