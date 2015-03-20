<?php

use DemocracyApps\GB\Accounts\AccountChart;
use DemocracyApps\GB\Accounts\Dataset;
use DemocracyApps\GB\Organization;
use DemocracyApps\GB\Users\User;
use Illuminate\Database\Seeder;

class AshevilleSeeder extends Seeder {


    public function run()
    {
        // Create the organization and first user
        $user = User::where('email','=','eric@deepweave.com')->first();
        $org = new Organization();
        $org->name = "Asheville City Government";
        $org->slug = "asheville";
        $org->save();
        $org->addMember($user, 9);


        // Create the chart of accounts
        $chart = new AccountChart();
        $chart->name = "Default";
        $chart->organization = $org->id;
        $chart->save();

        \Log::info("Process accounts");
        $filePath = "../sample_data/AVL2014/canonical/accounts.csv";
        if (file_exists($filePath)) {
            \Bus::dispatch(new \DemocracyApps\GB\Commands\DataImport\LoadAccounts($filePath, $chart->id, false));
        }
        else dd("No such file: " . $filePath);

        // Now create the categories
        $order = array();
        \Log::info("Process funds");
        // Fund
        $cat = $this->createCategory("Fund", $chart->id, "../sample_data/AVL2014/canonical/cat_fund.csv");
        $order[] = $cat->id;
        \Log::info("Process departments");
        // Department
        $cat = $this->createCategory("Department", $chart->id, "../sample_data/AVL2014/canonical/cat_department.csv");
        $order[] = $cat->id;
        \Log::info("Process divisions");
        // Division
        $cat = $this->createCategory("Division", $chart->id, "../sample_data/AVL2014/canonical/cat_division.csv");
        $order[] = $cat->id;
        \Log::info("Process functions");
        // Function
        $cat = $this->createCategory("Function", $chart->id, "../sample_data/AVL2014/canonical/cat_function.csv");
        $order[] = $cat->id;
        \Log::info("Process cost centers");
        // Cost Center
        $cat = $this->createCategory("Cost Center", $chart->id, "../sample_data/AVL2014/canonical/cat_costcenter.csv");
        $order[] = $cat->id;

        //
        // And finally, let's load up the datasets

        \Log::info("Process 2014 Budget");
        $ds = $this->createDataset("2014 Budget", "budget", Dataset::ANNUAL, 2014, null, null,
            $org->id, $chart->id, $order);
        $ds->loadCSVData("../sample_data/AVL2014/canonical/2014AdoptedBudget_processed.csv", "all");

        \Log::info("Process 2013 Actuals");
        $ds = $this->createDataset("2013 Actuals", "actual", Dataset::ANNUAL, 2013, null, null,
                                   $org->id, $chart->id, $order);
        $ds->loadCSVData("../sample_data/AVL2014/canonical/general_fund_2013_processed.csv", "general");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/civiccenter_fund_2013_processed.csv", "civiccenter");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/masstransit_fund_2013_processed.csv", "masstransit");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/parkingservices_fund_2013_processed.csv", "parking");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/stormwater_fund_2013_processed.csv", "stormwater");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/streetcut_fund_2013_processed.csv", "streetcut");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/water_fund_2013_processed.csv", "water");

        \Log::info("Process 2012 Actuals");
        $ds = $this->createDataset("2012 Actuals", "actual", Dataset::ANNUAL, 2012, null, null,
            $org->id, $chart->id, $order);
        $ds->loadCSVData("../sample_data/AVL2014/canonical/general_fund_2012_processed.csv", "general");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/civiccenter_fund_2012_processed.csv", "civiccenter");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/masstransit_fund_2012_processed.csv", "masstransit");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/parkingservices_fund_2012_processed.csv", "parking");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/stormwater_fund_2012_processed.csv", "stormwater");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/streetcut_fund_2012_processed.csv", "streetcut");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/water_fund_2012_processed.csv", "water");

        \Log::info("Process 2011 Actuals");
        $ds = $this->createDataset("2011 Actuals", "actual", Dataset::ANNUAL, 2011, null, null,
            $org->id, $chart->id, $order);
        $ds->loadCSVData("../sample_data/AVL2014/canonical/general_fund_2011_processed.csv", "general");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/civiccenter_fund_2011_processed.csv", "civiccenter");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/masstransit_fund_2011_processed.csv", "masstransit");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/parkingservices_fund_2011_processed.csv", "parking");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/stormwater_fund_2011_processed.csv", "stormwater");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/streetcut_fund_2011_processed.csv", "streetcut");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/water_fund_2011_processed.csv", "water");

        \Log::info("Process 2010 Actuals");
        $ds = $this->createDataset("2010 Actuals", "actual", Dataset::ANNUAL, 2010, null, null,
            $org->id, $chart->id, $order);
        $ds->loadCSVData("../sample_data/AVL2014/canonical/general_fund_2010_processed.csv", "general");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/civiccenter_fund_2010_processed.csv", "civiccenter");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/masstransit_fund_2010_processed.csv", "masstransit");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/parkingservices_fund_2010_processed.csv", "parking");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/stormwater_fund_2010_processed.csv", "stormwater");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/streetcut_fund_2010_processed.csv", "streetcut");
        $ds->loadCSVData("../sample_data/AVL2014/canonical/water_fund_2010_processed.csv", "water");


    }

    private function createDataset($name, $type, $granularity, $year, $month, $day, $orgId, $chartId, $order)
    {
        $d = new Dataset();
        $d->name = $name;
        $d->type = $type;
        $d->granularity = $granularity;
        $d->year = $year;
        $d->month = $month;
        $d->day = $day;
        $d->organization = $orgId;
        $d->chart = $chartId;
        $d->category_order = json_encode($order);
        $d->save();
        return $d;
    }
    private function createCategory($name, $chartId, $filePath) {
        $category = new \DemocracyApps\GB\Accounts\AccountCategory();
        $category->name = $name;
        $category->chart = $chartId;
        $category->save();
        if (file_exists($filePath)) {
            \Bus::dispatch(new \DemocracyApps\GB\Commands\DataImport\LoadCategory($filePath, $category->id, false));
        }
        else throw new \Exception ("AshevilleSeeder createCategory $name - no such file: " . $filePath);

        return $category;
    }

}