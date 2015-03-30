<?php

use DemocracyApps\GB\Organizations\MediaOrganization;
use DemocracyApps\GB\Users\User;
use Illuminate\Database\Seeder;

class CitizenTimesSeeder extends Seeder
{


    public function run()
    {
        // Create the organization and first user
        $user = User::where('email','=','eric@deepweave.com')->first();
        $org = new MediaOrganization();
        $org->name = "Asheville Citizen-Times";
        $org->save();
        $org->addMember($user, 9);

    }

}