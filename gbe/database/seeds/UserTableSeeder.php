<?php

use Illuminate\Database\Seeder;
use DemocracyApps\GB\Users\User;

class UserTableSeeder extends Seeder {


    public function run()
    {
        $faker = Faker\Factory::create();
        DB::table('users')->delete();

        $user = User::create(['email' => 'admin@democracyapps.us', 'name'=>'DemocracyApps Administrator',
            'password' => \Hash::make('democracy01'), 'superuser'=>true,
            'verified' => true]);
        $user = User::create(['email' => 'eric@deepweave.com', 'name'=>'Eric Jackson',
            'password' => \Hash::make('democracy01'), 'superuser'=>true,
            'verified' => true]);


//        $path = public_path().'/img/ej.png';
//        $path2 = public_path().'/img/ejtmp.png';
//        \Image::make($path)->resize(300,250, function ($constraint) {
//            $constraint->aspectRatio();
//            $constraint->upsize();
//        })->save($path2);
//        $picture = \File::get($path);
//        $name = uniqid('pic') . 'png';
//        $disk = \Storage::disk('s3');
//        $disk->put($name, $picture);
//        $user->photo = $name;
//        $user->save();
//        unlink($path2);
    }

    private function createDescription ($faker) {
        $desc = "";
        $glue = PHP_EOL . ' ' . PHP_EOL;
        $desc .= implode($glue, $faker->paragraphs(2));
        return $desc;
    }

}
