<?php

use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AFLSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $crews = DB::table('crews')->where('empRank', '=', 'PIC')->pluck('empNbr')->toArray();
        $acRegs = config('enums.acRegShort');

        //i = 200000
    	for($j = 0; $j < 200; $j++){
    		$copyData = [];
	    	for($k = 0; $k < 1000; $k++){
	    		$depstn = $faker->regexify('[A-Z]{3}');
		    	$arrstn = $faker->unique($depstn)->regexify('[A-Z]{3}');

				$date = date('Y-m-d', mt_rand(strtotime('2018-05-15'), strtotime('2019-02-15')));
				$depdate = Carbon::parse($date);
				$arrdate = Carbon::parse($date)->addDay(mt_rand(0,1));

				$copyData[] = [
		    		'aflnbr' => date('y', strtotime($depdate)).$acRegs[array_rand($acRegs)].$faker->unique()->numerify('####'),
		            'depstn' => $depstn,
		            'arrstn' => $arrstn,
		            'depdate' => $depdate,
		            'arrdate' => $arrdate,
		            'fltnbr' => mt_rand(100,2000),
		            'picnew' => $crews[array_rand($crews)],
		            'created_at' => Carbon::create(
		            	date('Y', strtotime($arrdate)), 
		            	date('m', strtotime($arrdate)), 
		            	date('d', strtotime($arrdate)), 
		            	mt_rand(0, 23), 
		            	mt_rand(0, 59), 
		            	mt_rand(0, 59)
		            )->addHours(6),
		            'updated_at' => Carbon::now()
		        ];	
	    	}
	    	
			DB::table('afl_copies')->insert($copyData);
    	}
    }
}
