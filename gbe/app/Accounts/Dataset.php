<?php namespace DemocracyApps\GB\Accounts;

use DemocracyApps\GB\Utility\EloquentPropertiedObject;

class Dataset extends EloquentPropertiedObject {
    protected  $table = 'datasets';

    public function addCategories ($categories) {
        $size = sizeof($categories);
        if (size>0) $this->category1 = $categories[0];
        if (size>1) $this->category2 = $categories[1];
        if (size>2) $this->category3 = $categories[2];
        if (size>3) {
            $spill = array();
            for ($i=0; $i<size-3; ++$i) {
                $spill[] = $categories[3+$i];
            }
            $this->categoryN = json_encode($spill);
        }
    }

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
                    $code = columns[$i+2];
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
                \Log::info("Invalid line: " . json_decode($columns));
                ++$badLines;
            }
        }
        return "Processed accounts file - total bad lines = " . $badLines;
    }


}