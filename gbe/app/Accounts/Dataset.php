<?php namespace DemocracyApps\GB\Accounts;

use DemocracyApps\GB\Utility\EloquentPropertiedObject;

class Dataset extends EloquentPropertiedObject {
    protected  $table = 'datasets';

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

                if (! isset($amount)) throw new \Exception ("Amount not set: " . json_encode($columns));

                // Look up the account
                if (! array_key_exists($accountCode, $accountMap)) throw new \Exception ("Account not set: " . json_encode($columns));
                $account = $accountMap[$accountCode];

                for ($i=0; $i<5; ++$i) {
                    $code = trim($columns[$i+2]);
                    $map = $categoryMaps[$i];
                    if (! array_key_exists($code, $map)) {
                        throw new \Exception ("Category " . $code . " not set: " . json_encode($columns));
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
        return "Processed accounts file - total bad lines = " . $badLines;
    }

}