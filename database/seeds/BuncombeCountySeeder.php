<?php

use DemocracyApps\GB\Budget\AccountChart;
use DemocracyApps\GB\Budget\Dataset;
use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Users\User;
use Illuminate\Database\Seeder;

class BuncombeCountySeeder extends Seeder {


    public function run()
    {
        // Create the organization and first users
        $user = User::where('email','=','eric@deepweave.com')->first();
        $org = new GovernmentOrganization();
        $org->name = "Buncombe County, NC";
        $org->save();
        $org->addMember($user, 9);

        $user = User::where('email','=','admin@democracyapps.us')->first();
        $org->addMember($user, 9);

        // Create the chart of accounts
        $chart = new AccountChart();
        $chart->name = "Default";
        $chart->government_organization = $org->id;
        $chart->save();

        // Now create the categories
        $order = array();
        \Log::info("Set up categories");
        // Function
        $cat = $this->createCategory("Function", $chart->id, null);
        $order[] = $cat->id;
        //
        // And finally, let's load up the datasets

        \Log::info("Process 2016 Budget");
        echo "Process 2016 Budget\n";
        $ds = $this->createDataset("2016 Budget", "budget", Dataset::ANNUAL, 2016, null, null,
            $org->id, $chart->id, $order);
        $ds->loadAllInOneCSVData("./resources/sample_data/BuncombeCounty/Budget2016.csv");

        \Log::info("Process 2015 Budget");
        echo "Process 2015 Budget\n";
        $ds = $this->createDataset("2015 Budget", "budget", Dataset::ANNUAL, 2015, null, null,
            $org->id, $chart->id, $order);
        $ds->loadAllInOneCSVData("./resources/sample_data/BuncombeCounty/Budget2015.csv");

        \Log::info("Process 2014 Budget");
        echo "Process 2014 Budget\n";
        $ds = $this->createDataset("2014 Budget", "budget", Dataset::ANNUAL, 2014, null, null,
            $org->id, $chart->id, $order);
        $ds->loadAllInOneCSVData("./resources/sample_data/BuncombeCounty/Budget2015.csv");

        // Now create the school categories
        $order = array();
        // Function
        $cat = $this->createCategory("School System", $chart->id, null);
        $order[] = $cat->id;
        $cat = $this->createCategory("Fund", $chart->id, null);
        $order[] = $cat->id;
        $cat = $this->createCategory("Group", $chart->id, null);
        $order[] = $cat->id;

        echo "Process 2014 School Actuals\n";
        $ds = $this->createDataset("2014 School Actuals", "actual", Dataset::ANNUAL, 2014, null, null,
            $org->id, $chart->id, $order);
        $ds->loadAllInOneCSVData("./resources/sample_data/BuncombeCounty/Schools2014.csv");

        echo "Process 2015 School Budget\n";
        $ds = $this->createDataset("2015 School Budget", "actual", Dataset::ANNUAL, 2015, null, null,
            $org->id, $chart->id, $order);
        $ds->loadAllInOneCSVData("./resources/sample_data/BuncombeCounty/Schools2015.csv");

        echo "Process 2016 School Budget\n";
        $ds = $this->createDataset("2016 School Budget", "actual", Dataset::ANNUAL, 2016, null, null,
            $org->id, $chart->id, $order);
        $ds->loadAllInOneCSVData("./resources/sample_data/BuncombeCounty/Schools2016.csv");
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
        $d->government_organization = $orgId;
        $d->chart = $chartId;
        if ($order != null) $d->category_order = json_encode($order);
        $d->save();
        return $d;
    }
    private function createCategory($name, $chartId, $filePath) {
        $category = new \DemocracyApps\GB\Budget\AccountCategory();
        $category->name = $name;
        $category->chart = $chartId;
        $category->save();
        if ($filePath != null) {
            if (file_exists($filePath)) {
                \Bus::dispatch(new \DemocracyApps\GB\Commands\DataImport\LoadCategory($filePath, $category->id, false));
            } else throw new \Exception ("BuncombeCountySeeder createCategory $name - no such file: " . $filePath);
        }
        return $category;
    }

}