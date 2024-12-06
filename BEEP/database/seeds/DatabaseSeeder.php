<?php

use Illuminate\Database\Seeder;
use Database\Seeders\RolesAndUsersSeeder; // Correct namespace

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DeviceCorrectionSeeder::class);
        $this->call(RolesAndUsersSeeder::class); // Call the seeder
        // $this->call(UserSeeder::class);
        // $this->call(MeasurementSeeder::class);
    }
}
