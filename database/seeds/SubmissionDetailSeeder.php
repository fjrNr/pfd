<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SubmissionDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $submissions = DB::table('submissions')->select('receivedDate', 'empNbr')->distinct()->where('empRank', 'PIC')->get();

        foreach($submissions as $subm){
            $afl_numbers = DB::table('afl_copies')
                        ->where('arrdate', $subm->receivedDate)
                        ->where('picnew', $subm->empNbr)
                        ->groupBy('aflnbr')
                        ->pluck('aflnbr')
                        ->toArray();

            $submission = DB::table('submissions')->select('id', 'created_at')
                        ->where('receivedDate', $subm->receivedDate)
                        ->where('empNbr', $subm->empNbr)
                        ->first();

            if(isset($afl_numbers)){
                $copyData = [];
                foreach($afl_numbers as $afl_num){
                    $query = DB::table('afl_copies')->where('aflnbr', $afl_num);
                    $query->where('arrdate', '!=', $subm->receivedDate)->delete();
                    $query->where('picnew', '!=', $subm->empNbr)->delete();

                    $copyData[] = [
                        'submissionId' => $submission->id,
                        'aflNbr' => $afl_num,
                        'flightPlan' => mt_rand(0,1),
                        'dispatchRelease' => mt_rand(0,1),
                        'weatherForecast' => mt_rand(0,1),
                        'notam' => mt_rand(0,1),
                        'toLdgDataCard' => mt_rand(0,1),
                        'loadSheet' => mt_rand(0,1),
                        'fuelReceipt' => mt_rand(0,1),
                        'paxManifest' => mt_rand(0,1),
                        'notoc' => mt_rand(0,1),
                        'created_at' => Carbon::parse($submission->created_at),
                        'updated_at' => Carbon::now(),
                    ];
                }

                DB::table('submission_details')->insert($copyData);
            }
        }
    }
}
