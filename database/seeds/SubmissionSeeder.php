<?php

use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $formNbr = DB::table('submissions')->count();
        $crews = DB::table('crews')->where('empRank', '=', 'PIC')->pluck('empNbr')->toArray();
        $auth_users = DB::table('auth_users')->whereIn('group', ['ADMIN', 'SUBM OFC'])->pluck('id')->toArray();
        $notes = config('enums.submission_notes');

        // i = 50000
        for($i = 0; $i < 50; $i++){
            $copyData = [];
            for($j = 0; $j < 1000; $j++){
                $crew = DB::table('crews')->where('empNbr', $crews[array_rand($crews)])->first();
                $receivedDate = date('Y-m-d', mt_rand(strtotime('2018-05-15'), strtotime('2019-02-15')));
                $formNbr++;

                $copyData[] = [
                    'empNbr' => $crew->empNbr,
                    'empRank' => $crew->empRank,
                    'inputBy' => $auth_users[array_rand($auth_users)],
                    'formNbr' => str_pad($formNbr, 5, "0", STR_PAD_LEFT),
                    'receivedDate' => $receivedDate,
                    'quantity' => mt_rand(1, 5),
                    'remark' => $notes[array_rand($notes)],
                    'signed' => $crew->firstName.' '.$crew->middleName.' '.$crew->lastName,
                    'created_at' => Carbon::create(
                        date('Y', strtotime($receivedDate)), 
                        date('m', strtotime($receivedDate)), 
                        date('d', strtotime($receivedDate)), 
                        mt_rand(0, 23),
                        mt_rand(0, 59), 
                        mt_rand(0, 59)
                    )->addHours(6),
                    'updated_at' => Carbon::now(),
                ];

            }

            DB::table('submissions')->insert($copyData);
        }
    }
}
