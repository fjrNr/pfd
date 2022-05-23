<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NonAflBoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $num = 0;
        $start_date = strtotime('2018-05-15');
        $end_date = strtotime('2019-02-15');
        $curr_year = date('y', $start_date); 

        for($i = $start_date; $i <= $end_date; $i+=86400){
            $copyData = [];
            $year = date('y', $i);
            $date = date('Y-m-d', $i);
            
            if($year > $curr_year) {
                $curr_year = $year; 
                $num = 0;
            }
            
            for($j = 1; $j <= 6; $j++){
                $num++;
                $packNbr = 'PDF/'.$year.'/'.str_pad($num, 4, "0", STR_PAD_LEFT);
                $box_exist = DB::table('non_afl_boxes')->where('packNbr',$packNbr)->first();

                if (!$box_exist) {
                    $copyData[] = [
                        'classofdate' => $date,
                        'packNbr' => $packNbr,
                        'location' => config('enums.location')[0],
                        'boxNbr' => $j,
                        'endRetentionDate' => Carbon::parse($date)->addMonths(3),
                        'created_at' => Carbon::parse($date)->addHours($j+8),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }

            DB::table('non_afl_boxes')->insert($copyData);
        }
    }
}
