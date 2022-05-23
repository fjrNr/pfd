<?php

use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
	    
	    for($i = 0; $i < 20; $i++) {
		    $gender = $faker->randomElement(['male', 'female']);

	        DB::table('auth_users')->insert([
	            'id' => $faker->unique()->regexify('[A-Z]{4,8}'),
	            'name' => $faker->name($gender),
	            'password' => bcrypt('tes123'),
	            'group' => $faker->randomElement(['ADMIN', 'SUBM OFC', 'BOX OFC', 'ANALYST']),
	            'department' => $faker->regexify('[A-Z]{3,5}'),
	            'homebase' => $faker->regexify('[A-Z]{3}'),
	            'updatedt' => Carbon::now()
	        ]);
	    }

	    // DB::table('auth_users')->insert([
     //        'id' => 'ADMIN',
     //        'name' => 'Administrator',
     //        'password' => bcrypt('admin123'),
     //        'group' => 'ADMIN',
     //        'department' => $faker->regexify('[A-Z]{3,5}'),
     //        'homebase' => $faker->regexify('[A-Z]{3}'),
     //        'updatedt' => Carbon::now()
     //    ]);
    }
}
