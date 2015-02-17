<?php namespace DemocracyApps\GB\Accounts;


use DemocracyApps\GB\Utility\EloquentPropertiedObject;

class AccountCategory extends EloquentPropertiedObject
{
    protected  $table = 'account_categories';


    static public function processCSVInput($filePath, $category)
    {
        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings
        if ( !file_exists($filePath)) {
            \Log::info("AccountCategory.processCSVInput: The file " . $filePath . " does not exist");
        }
        $myFile = fopen($filePath,"r") or die ("Unable to open file");
        $badLines = 0;
        $line = fgetcsv($myFile);
        while (! feof($myFile)) {
            $columns = fgetcsv($myFile);
            if (sizeof($columns) == 2) {
                $code = strip_tags(trim($columns[0]));
                // See if there's already an account
                $categoryValue = AccountCategoryValue::where('category', '=', $category)
                    ->where('code', '=',$code)->first();
                if ($categoryValue == null) {
                    $categoryValue = new AccountCategoryValue();
                }
                $categoryValue->code = $code;
                $categoryValue->name = strip_tags(trim($columns[1]));
                $categoryValue->category = $category;
                $categoryValue->save();
            }
            else {
                \Log::info("Invalid line: " . json_decode($columns));
                ++$badLines;
            }
        }
        return "Processed account categories file - total bad lines = " . $badLines;
    }

}
