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

class Account extends EloquentPropertiedObject
{
    protected  $table = 'accounts';
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

    static public function allOrganizationAccounts ($orgId) {
        $items = \DB::table('accounts')
            ->join('account_charts', 'accounts.chart', '=', 'account_charts.id')
            ->where('account_charts.organization','=',$orgId)
            ->select('accounts.id', 'accounts.code', 'accounts.name', 'accounts.chart', 'accounts.type')
            ->get();
        return $items;
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
        $lnum = 1;
        $records = array();
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $records[] = [
            'code'=>'-1',
            'name'=>"Unknown Account",
            'type'=>Account::UNKNOWN,
            'chart'=>$chart,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ];
        while (! feof($myFile)) {
            $columns = fgetcsv($myFile);
            ++$lnum;
            if (sizeof($columns) == 3) {
                $code = strip_tags(trim($columns[0]));
                // See if there's already an account
                $account = Account::where('chart', '=', $chart)
                    ->where('code', '=',$code)->first();
                if ($account == null) {
                    if (true) {
                        $records[] = [
                            'code'=>$code,
                            'name'=>strip_tags(trim($columns[1])),
                            'type'=>Account::typeCode(trim($columns[2])),
                            'chart'=>$chart,
                            'created_at' => $created_at,
                            'updated_at' => $updated_at
                        ];
                        if (sizeof($records) >= 999) {
                            \DB::table('accounts')->insert($records);
                            $records = array();
                        }
                    }
                    else {
                        $account = new Account();
                        $account->code = $code;
                        $account->name = strip_tags(trim($columns[1]));
                        $account->type = Account::typeCode(trim($columns[2]));
                        $account->chart = $chart;
                        $account->save();
                    }
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
        if (sizeof($records)> 0) \DB::table('accounts')->insert($records);
        return $badLines>0?"Processed accounts file with $lnum lines - total bad lines = " . $badLines:null;
    }
}