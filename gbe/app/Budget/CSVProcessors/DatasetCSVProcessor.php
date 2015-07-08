<?php namespace DemocracyApps\GB\Budget\CSVProcessors;
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
use DemocracyApps\GB\Budget\Account;
use DemocracyApps\GB\Budget\AccountCategory;
use DemocracyApps\GB\Budget\AccountCategoryValue;
use DemocracyApps\GB\Budget\Dataset;
use DemocracyApps\GB\Utility\Notification;

class DatasetCSVProcessor
{

    private function loadMultipleDatasets($filePath, $data) {

        \Log::info("Starting multi-dataset load");
        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings
        if ( !file_exists($filePath)) {
            \Log::info("DatasetCSVProcessor.loadMultipleDatasets: The file " . $filePath . " does not exist");
            return "The file $filePath does not exist.";
        }
        $myFile = fopen($filePath,"r") or die ("Unable to open file");
        $header = fgetcsv($myFile); // Get the header to grab category names
        if (sizeof($header) < 3) throw new \Exception("Dataset.loadMultipleDatasets: Header must have at least 3 columns");

        $nYears = $data['year_count']; // Number of datasets
        $yearColumn = $data['year_column'] - 1;
        \Log::info("Getting year data from column  $yearColumn through " . ($yearColumn + $nYears - 1));
        $nCategories = $data['categories'];
        $categoryColumn = $data['categories_column']-1;
        \Log::info("Getting category data from column " . $categoryColumn . " through " . ($categoryColumn + $nCategories - 1));
        $year0 = $data['start_year'] + 0;
        \Log::info("Start year is " . $year0);
        $yearHeaders = array_slice($header, $yearColumn, $nYears);

        $chart = $data['chart'];

        $records = array();
        $datasets = array();
        for ($i=0; $i<$nYears; ++$i) {
            $records[] = array();
            $ds = new Dataset();
            $ds->name = $data['name'] . $yearHeaders[$i];
            $ds->government_organization = $data['governmentId'];
            $ds->chart = $data['chart'];
            $ds->year = $year0+$i;
            $ds->type = 'actual'; // This doesn't actually mean anything. Need to replace with annotation system.
            $ds->granularity = Dataset::ANNUAL;
            if (array_key_exists('description',$data)) $ds->description = $data['description'];
            $ds->save();
            $datasets[] = $ds;
        }
        \Log::info("Created the datasets");
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        /*
         * So we expect a file with the format:
         *  Type, Value, Category1, Category2, ...
         */
        $categoryNames = array_slice($header, $categoryColumn, $nCategories);
        $categories = array();
        $order = [];

        for ($i=0; $i<$nCategories; ++$i) {
            $categories[$i] = new \stdClass();
            $categories[$i]->name = $categoryNames[$i];
            $categories[$i]->values = array();
            if ($i < $nCategories-1) { // Don't do this part for account level
                $accountCategory = AccountCategory::where('chart', '=', $chart)->where('name', '=', $categoryNames[$i])->first();
                if ($accountCategory == null) {
                    $accountCategory = new AccountCategory();
                    $accountCategory->chart = $chart;
                    $accountCategory->name = $categories[$i]->name;
                    $accountCategory->save();
                }
                $categories[$i]->id = $accountCategory->id;
                $order[] = $accountCategory->id;
            }
        }
        for ($iYear = 0; $iYear < $nYears; ++$iYear) {
            $ds = $datasets[$iYear];
            $ds->category_order = json_encode($order);
            $ds->save();
        }

        \Log::info("Created categories");

        $lnum = 1;
        $errnum=0;
        while (! feof($myFile)) {
            $isBad = false;
            $columns = fgetcsv($myFile);
            ++$lnum;

            // Initializations
            $type = Account::UNKNOWN;
            $accountId = null;
            $categoryList = array();

            // Get the account type
            $typeName = strtolower(strip_tags(trim($columns[0])));
            if (starts_with($typeName, 'exp'))
                $type = Account::EXPENSE;
            elseif (starts_with($typeName, 'rev'))
                $type = Account::REVENUE;
            else
                $isBad = true;

            if ($isBad) {
                ++$errnum;
                continue;
            }
            \Log::info("Type $typeName on line $lnum");

            // Get the amounts
            $amounts = array();
            for ($iYear = 0; $iYear < $nYears; ++$iYear) {
                $amounts[] = strip_tags(trim($columns[$yearColumn + $iYear]));
            }
            \Log::info("The amounts " . json_encode($amounts));
            // Now category and account information
            for ($i = 0; $i < $nCategories; ++$i) {
                $catName = strip_tags(trim($columns[$i + $categoryColumn]));

                if (array_key_exists($catName, $categories[$i]->values)) {
                    if ($i < $nCategories-1) {
                        $categoryList[] = $categories[$i]->values[$catName];
                    }
                    else {
                        $accountId = $categories[$i]->values[$catName];
                    }
                }
                else {
                    if ($i == $nCategories - 1) { // Account
                        $account = Account::where('name', '=', $catName)
                            ->where('chart', '=', $chart)->first();
                        if ($account == null) {
                            $account = new Account();
                            $account->chart = $chart;
                            $account->name = $catName;
                            $account->code = $catName;
                            $account->type = $type;
                            $account->created_at = $created_at;
                            $account->updated_at = $updated_at;
                            $account->save();
                            $accountId = $account->id;
                        }
                        else {
                            $accountId = $account->id;
                        }
                        $categories[$i]->values[$catName] = $accountId;
                    } else {
                        $catValue = AccountCategoryValue::where('name', '=', $catName)
                            ->where('category', '=', $categories[$i]->id)->first();
                        if ($catValue == null) { // Need to create it
                            $c = new AccountCategoryValue();
                            $c->name = $catName;
                            $c->code = $catName;
                            $c->category = $categories[$i]->id;
                            $c->save();
                            $categoryList[] = $c->id;
                            $categories[$i]->values[$catName] = $c->id;
                        } else {
                            $categories[$i]->values[$catName] = $catValue->id;
                            $categoryList[] = $catValue->id;
                        }
                    }
                }
            }

            $category1 = null;
            $category2 = null;
            $category3 = null;
            $categoryN = null;

            if ($nCategories>0) $category1 = $categoryList[0];
            if ($nCategories>1 && array_key_exists(1, $categoryList)) $category2 = $categoryList[1];
            if ($nCategories>2 && array_key_exists(2, $categoryList)) $category3 = $categoryList[2];
            if ($nCategories>3 && array_key_exists(3, $categoryList)) {
                $spill = array_slice($categoryList, 3);
                $categoryN = json_encode($spill);
            }
            \Log::info("Figured out the categories: $category1, $accountId");
            for ($iYear = 0; $iYear < $nYears; ++$iYear) {
                if ($amounts[$iYear] != null && $amounts[$iYear] != 0.0)
                $records[$iYear][] = [
                    'group' => $datasets[$iYear]->id, // Not sure group is necessary
                    'account' => $accountId,
                    'amount' => $amounts[$iYear],
                    'dataset' => $datasets[$iYear]->id,
                    'category1' => $category1,
                    'category2' => $category2,
                    'category3' => $category3,
                    'categoryN' => $categoryN,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ];
                if (sizeof($records[$iYear]) >= 999) {
                    \Log::info("Saving out dataset ".$datasets[$iYear]->id);
                    \DB::table('data_items')->insert($records[$iYear]);
                    $records[$iYear] = array();
                }
            }
            \Log::info(" End of loop " . $lnum);
        }
        for ($iYear = 0; $iYear < $nYears; ++$iYear) {
            if (sizeof($records[$iYear])> 0) {
                \DB::table('data_items')->insert($records[$iYear]);
            }
        }
        return "Processed data items file - total errors = " . $errnum . ' of ' . $lnum;

    }

    public function fire($queueJob, $data)
    {
        $userId = $data['userId'];
        $user = \DemocracyApps\GB\Users\User::findOrFail($userId);
        \Auth::login($user);
        $filePath = $data['filePath'];

        \Log::info("DatasetCSVProcessor with multi = " . $data['multi']);
        if ($data['multi']) {
            $this->loadMultipleDatasets($filePath, $data);
        }
        else {
            $datasetId = $data['dataset'];
            $dataset = Dataset::find($datasetId);

            \Log::info("Starting processing of " . $filePath);
            $notification = Notification::find($data['notificationId']);
            $notification->messages = $dataset->loadAllInOneCSVData($filePath);
            $notification->status = 'Completed';
            $notification->completed_at = date('Y-m-d H:i:s');
            $notification->save();
            \Log::info("Completed processing of job " . $notification->id . " for " . $filePath);
        }
        unlink($filePath);
        $queueJob->delete();
    }

}