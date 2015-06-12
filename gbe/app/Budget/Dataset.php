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

class Dataset extends EloquentPropertiedObject {
    protected  $table = 'datasets';

    const ANNUAL = 1;
    const MONTHLY = 2;
    const DAILY = 3;

    public function loadCSVData($filePath, $group)
    {

        // Blow away any previous load of this group
        DataItem::where('dataset','=',$this->id)
            ->where('group','=',$group)->delete();

        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings
        if ( !file_exists($filePath)) {
            \Log::info("Dataset.processCSVInput: The file " . $filePath . " does not exist");
        }
        $myFile = fopen($filePath,"r") or die ("Unable to open file");


        /*
         * Let's load the accounts and categories and setup maps by code
         */
        $accounts = Account::where('chart', '=', $this->chart)->get();
        $accountMap = array();
        foreach ($accounts as $account) {
            $accountMap[$account->code] = $account->id;
        }

        // Categories should be in the CSV file in this order
        $categories = array();
        $order = json_decode($this->category_order);
        foreach ($order as $catId) {
            $categories[] = AccountCategory::find($catId);
        }

        $categoryMaps = array();
        for ($i=0; $i<sizeof($categories); ++$i) {
            $cvs = AccountCategoryValue::where('category', '=', $categories[$i]->id)->get();
            $map = array();
            foreach ($cvs as $cv) {
                $map[$cv->code] = $cv->id;
            }
            $categoryMaps[] = $map;
        }

        $badLines = 0;
        $records = array();
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $line = fgetcsv($myFile); // Skip the header
        $lnum = 1;
        $errnum=0;
        while (! feof($myFile)) {
            $columns = fgetcsv($myFile);
            ++$lnum;

            if (sizeof($columns) > 1) { // Must at least have data item and account code
                $accountCode = strip_tags(trim($columns[0]));
                $amount = strip_tags(trim($columns[1]));

                // Look up the account
                if (array_key_exists($accountCode, $accountMap)) {
                    $account = $accountMap[$accountCode];
                }
                else {
                    echo "Unable to locate account $accountCode\n";
                    $account = $accountMap['-1'];
                }

                $ncat = sizeof($columns) - 2;
                if ($ncat > 0) {
                    $categories = array();
                    for ($i = 0; $i < $ncat; ++$i) {
                        $code = strip_tags(trim($columns[$i + 2]));
                        $map = $categoryMaps[$i];
                        if (array_key_exists($code, $map)) {
                            $categories[] = $map[$code];
                        }
                        else {
                            ++$errnum;
                            echo "Unable to locate category /$code/ at level $i : " . implode('/', $columns) . "\n";
                            $categories[] = null;
                        }
                    }
                }

                if ($errnum > 500) exit(1);
                $category1 = null;
                $category2 = null;
                $category3 = null;
                $categoryN = null;

                $size = sizeof($categories);
                if ($size>0) $category1 = $categories[0];
                if ($size>1) $category2 = $categories[1];
                if ($size>2) $category3 = $categories[2];
                if ($size>3) {
                    $spill = array();
                    for ($i=0; $i<$size-3; ++$i) {
                        $spill[] = $categories[3+$i];
                    }
                    $categoryN = json_encode($spill);
                }

                $records[] = [
                    'group'=>$group,
                    'account'=>$account,
                    'amount'=>$amount,
                    'dataset'=>$this->id,
                    'category1'=> $category1,
                    'category2'=> $category2,
                    'category3'=> $category3,
                    'categoryN'=> $categoryN,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ];
                if (sizeof($records) >= 999) {
                    \DB::table('data_items')->insert($records);
                    $records = array();
                }
            }
            else if (sizeof($columns) > 0) { // We ignore blank lines
                $val = trim($columns[0]);
                if (($val != null && strlen($val) > 0) || sizeof($columns) > 1) {
                    \Log::info("Invalid line $lnum: " . json_encode($columns));
                    ++$badLines;
                }
            }
        }
        if (sizeof($records)> 0) \DB::table('data_items')->insert($records);
        return "Processed data items file - total bad lines = " . $badLines . ' of ' . $lnum;
    }

    public function loadAllInOneCSVData($filePath)
    {

        \Log::info("Starting load all in one with dataset $this->id");
        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings
        if ( !file_exists($filePath)) {
            \Log::info("Dataset.loadAllInOneCSVData: The file " . $filePath . " does not exist");
        }
        $myFile = fopen($filePath,"r") or die ("Unable to open file");


        $records = array();
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        /*
         * So we expect a file with the format:
         *  Type, Value, Category1, Category2, ...
         */
        $header = fgetcsv($myFile); // Get the header to grab category names
        \Log::info("Here's the header: " . json_encode($header));
        if (sizeof($header) < 3) throw new \Exception("Dataset.loadAllInOneCSVData: Header must have at least 3 columns");
        $categoryNames = array_slice($header, 2);
        $nCategories = sizeof($categoryNames);
        $categories = array();
        $order = [];

        for ($i=0; $i<$nCategories; ++$i) {
            $categories[$i] = new \stdClass();
            $categories[$i]->name = $categoryNames[$i];
            \Log::info("Read a category " . $categoryNames[$i]);
            $categories[$i]->values = array();
            if ($i < $nCategories-1) { // Don't do this part for account level
                \Log::info("Creating the category");
                $accountCategory = AccountCategory::where('chart', '=', $this->chart)->where('name', '=', $categoryNames[$i])->first();
                if ($accountCategory == null) {
                    $accountCategory = new AccountCategory();
                    $accountCategory->chart = $this->chart;
                    $accountCategory->name = $categories[$i]->name;
                    $accountCategory->save();
                }
                $categories[$i]->id = $accountCategory->id;
                $order[] = $accountCategory->id;
            }
        }
        \Log::info("Ok, let's save out with order = " . json_encode($order));
        $this->category_order = json_encode($order);
        $this->save();

        $lnum = 1;
        $errnum=0;
        while (! feof($myFile)) {
            $isBad = false;
            $columns = fgetcsv($myFile);
            ++$lnum;

            // Must at least have type, value and an account. Can have multiple categories preceding the account
            if (sizeof($columns) > 3) {
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

                // Amount
                $amount = strip_tags(trim($columns[1]));

                // Now category and account information
                for ($i = 0; $i < $nCategories; ++$i) {
                    $catName = strip_tags(trim($columns[$i + 2]));

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
                                            ->where('chart', '=', $this->chart)->first();
                            if ($account == null) {
                                $account = new Account();
                                $account->chart = $this->chart;
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
                if ($nCategories>1) $category2 = $categoryList[1];
                if ($nCategories>2) $category3 = $categoryList[2];
                if ($nCategories>3) {
                    $spill = array_slice($categoryList, 3);
                    $categoryN = json_encode($spill);
                }

                $records[] = [
                    'group'=>$this->id, // Not sure group is necessary
                    'account'=>$accountId,
                    'amount'=>$amount,
                    'dataset'=>$this->id,
                    'category1'=> $category1,
                    'category2'=> $category2,
                    'category3'=> $category3,
                    'categoryN'=> $categoryN,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ];
                if (sizeof($records) >= 999) {
                    \DB::table('data_items')->insert($records);
                    $records = array();
                }
            }
        }
        if (sizeof($records)> 0) \DB::table('data_items')->insert($records);
        return "Processed data items file - total errors = " . $errnum . ' of ' . $lnum;
    }

    /*
     * This routine is possibly unused??
     */
    static public function processCSVInput($filePath, $dataset)
    {
        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings
        if ( !file_exists($filePath)) {
            \Log::info("Dataset.processCSVInput: The file " . $filePath . " does not exist");
        }
        $myFile = fopen($filePath,"r") or die ("Unable to open file");
        $ds = self::find($dataset);

        /*
         * Let's load the accounts and categories
         */
        $accounts = Account::where('chart', '=', $ds->chart)->get();
        $accountMap = array();
        foreach ($accounts as $account) {
            $accountMap[$account->code] = $account->id;
        }

        $categories = AccountCategory::where('chart', '=', $ds->chart)->orderBy('order')->get();
        $categoryMaps = array();
        for ($i=0; $i<sizeof($categories); ++$i) {
            $cvs = AccountCategoryValue::where('category', '=', $categories[$i]->id)->get();
            $map = array();
            foreach ($cvs as $cv) {
                $map[$cv->code] = $cv->id;
            }
            $categoryMaps[] = $map;
        }

        $badLines = 0;
        $line = fgetcsv($myFile);
        while (! feof($myFile)) {
            $columns = fgetcsv($myFile);
            if (sizeof($columns) == 7) {
                $accountCode = strip_tags(trim($columns[0]));
                $amount = strip_tags(trim($columns[1]));


                // Look up the account
                $account = $accountMap[$accountCode];

                $categories = array();
                for ($i=0; $i<5; ++$i) {
                    $code = trim($columns[$i+2]);
                    $map = $categoryMaps[$i];
                    $categories[] = $map[$code];
                }

                $ditem = new DataItem();
                $ditem->account = $account;
                $ditem->amount = $amount;
                $ditem->dataset = $dataset;
                $ditem->addCategories($categories);

                $ditem->save();
            }
            else {
                if (!feof($myFile)) {
                    \Log::info("Invalid line: " . json_encode($columns));
                    ++$badLines;
                }
            }
        }
        return "Processed accounts file - total bad lines = " . $badLines;
    }

    static public function validateCSVInput($filePath, $chart)
    {
        $messages = array();
        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings
        if ( !file_exists($filePath)) {
            \Log::info("Dataset.processCSVInput: The file " . $filePath . " does not exist");
        }
        $myFile = fopen($filePath,"r") or die ("Unable to open file");

        /*
         * Let's load the accounts and categories
         */
        $accounts = Account::where('chart', '=', $chart)->get();
        $accountMap = array();
        foreach ($accounts as $account) {
            $accountMap[$account->code] = $account->id;
        }

        $categories = AccountCategory::where('chart', '=', $chart)->orderBy('order')->get();
        $categoryMaps = array();
        for ($i=0; $i<sizeof($categories); ++$i) {
            $cvs = AccountCategoryValue::where('category', '=', $categories[$i]->id)->get();
            $map = array();
            foreach ($cvs as $cv) {
                $map[$cv->code] = $cv->id;
            }
            $categoryMaps[] = $map;
        }

        $badLines = 0;
        $line = fgetcsv($myFile);
        $count = 1;
        while (! feof($myFile)) {
            $columns = fgetcsv($myFile);
            ++$count;
            if (sizeof($columns) == 7) {
                $accountCode = strip_tags(trim($columns[0]));
                $amount = strip_tags(trim($columns[1]));

                if (! isset($amount)) $messages[] = "Amount not set: " . json_encode($columns);

                // Look up the account
                if (! array_key_exists($accountCode, $accountMap) && abs($amount) > 0.01) {
                    $messages[] = "Line " . $count . " - Account not set: " . json_encode($columns);
                }
                if (abs($amount) > 0.01) {
                    for ($i = 0; $i < 5; ++$i) {
                        $code = trim($columns[$i + 2]);
                        $map = $categoryMaps[$i];
                        if (!array_key_exists($code, $map)) {
                            $messages[] = "Line " . $count . " - Category " . $code . " not set: " . json_encode($columns);
                        }
                    }
                }
            }
            else {
                if (!feof($myFile)) {
                    \Log::info("Invalid line " . $count . ": " . json_encode($columns));
                    ++$badLines;
                }
            }
        }
        return $messages;
    }

}