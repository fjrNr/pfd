<?php
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SubmissionAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $start_date = strtotime('2018-05-15');
        $end_date = strtotime('2019-02-15');

        for($i = $start_date; $i <= $end_date; $i+=86400){
            $year = date('y', $i);
            $date = date('Y-m-d', $i);
            $submissions = DB::table('submissions')
                        ->where('receivedDate',$date)
                        ->pluck('id');

            $totalSubms = $submissions->count();
            $submissions->toArray();
            $submEachBox = ceil($totalSubms/6); 

            for($j = 0; $j < 6; $j++){
                $box = DB::table('non_afl_boxes')
                        ->where('classOfDate', $date)
                        ->where('boxNbr', $j)
                        ->first();

                if(isset($box)){
                    $copyData = [];
                    for($k = 0; $k < $submEachBox; $k++){
                        $index = ($j * $submEachBox) + $k;
                        
                        if($index < $totalSubms){
                            $copyData[] = [
                                'submissionId' => $submissions[$index],
                                'nonAflBoxId' => $box->id,
                                'created_at' => Carbon::parse($box->created_at),
                                'updated_at' => Carbon::now(),
                            ];
                        }
                    }

                    if(isset($copyData)){
                        DB::table('non_afl_assignments')->insert($copyData);
                    }
                }
            }
        }
    }
}
