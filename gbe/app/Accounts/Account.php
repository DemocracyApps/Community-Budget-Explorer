<?php namespace DemocracyApps\GB\Accounts;


use DemocracyApps\GB\Utility\EloquentPropertiedObject;

class Account extends EloquentPropertiedObject
{
    const UNKNOWN = 0;
    const REVENUE = 1;
    const EXPENSE = 2;
    const ASSET = 3;
    const LIABILITY = 4;
    const EQUITY = 5;
    const CONTRA = 6;

    protected static $typeNames = ['Unknown', 'Revenue', 'Expense', 'Asset', 'Liability', 'Equity', 'Contra'];

    static public function typeName($typeId)
    {
        $name = "Unknown";
        if ($typeId > 0 && $typeId < 7) $name = self::$typeNames[$typeId];
        return $name;
    }

    static public function typeCode($typeName)
    {
        $nm = strtolower(trim($typeName));
        $id = self::UNKNOWN;
        switch ($nm) {
            case "revenue":
                $id = self::REVENUE;
                break;
            case "expense":
                $id = self::EXPENSE;
                break;
            case "asset":
                $id = self::ASSET;
                break;
            case "liability":
                $id = self::LIABILITY;
                break;
            case "equity":
                $id = self::EQUITY;
                break;
            case "contra":
                $id = self::CONTRA;
                break;
            default:
                $id = self::UNKNOWN;
                break;
        }
        return $id;
    }

    static public function processCSVInput($filePath, $chart)
    {
        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings
        if ( !file_exists($filePath)) {
            \Log::info("Account.processCSVInput: The file " . $filePath . " does not exist");
        }
        $myFile = fopen($filePath,"r") or die ("Unable to open file");
        $badLines = 0;
        $line = fgetcsv($myFile);
        while (! feof($myFile)) {
            $columns = fgetcsv($myFile);
            if (sizeof($columns) == 3) {
                $code = strip_tags(trim($columns[0]));
                // See if there's already an account
                $account = Account::where('chart', '=', $chart)
                    ->where('code', '=',$code)->first();
                if ($account == null) {
                    $account = new Account();
                }
                $account->code = $code;
                $account->name = strip_tags(trim($columns[1]));
                $account->type = Account::typeCode(trim($columns[2]));
                $account->chart = $chart;
                $account->save();
            }
            else {
                \Log::info("Invalid line: " . json_decode($columns));
                ++$badLines;
            }
        }
        return "Processed accounts file - total bad lines = " . $badLines;
    }
}