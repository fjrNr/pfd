<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->call(CrewSeeder::class);
        $this->call(AFLSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SubmissionSeeder::class);
        $this->call(SubmissionDetailSeeder::class);
        $this->call(NonAflBoxSeeder::class);
        $this->call(SubmissionAssignmentSeeder::class);
    }
}
