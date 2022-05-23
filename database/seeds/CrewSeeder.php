<?php

use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CrewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
    	$crewData = [];
	    $faker = Faker::create('id_ID');
	    
	    for($i = 0; $i < 1500; $i++) {
		    $gender = $faker->randomElement(['male', 'female']);
		    $rank = $faker->randomElement(['CP', 'SO', 'FO', 'PIC', 'FA']);

	        $crewData[] = [
	            'empNbr' => $faker->unique()->numberBetween(500000, 599999),
	            'firstName' => $faker->firstName($gender),
	            'middleName' => $faker->optional()->firstName($gender),
	            'lastName' => $faker->lastNameMale(),
	            'empRank' => $rank,
	            'created_at' => Carbon::now(),
	            'updated_at' => Carbon::now()
	        ];
	    }

		$chunks = array_chunk($crewData, 100);
		foreach($chunks as $ck){
			DB::table('crews')->insert($ck);
		}
    }
}
