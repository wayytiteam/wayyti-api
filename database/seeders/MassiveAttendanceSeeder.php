<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Attendance;

class MassiveAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User ID you want to seed attendance for
        $userId = '9d59ddac-1848-4c4d-999f-efbf79a9f487'; // Change this to the desired user ID

        // Start date: November 7, 2023
        $startDate = Carbon::create(2023, 11, 7);

        // Loop to create 365 records for each day starting from $startDate
        for ($i = 0; $i < 365; $i++) {
            Attendance::create([
                'user_id' => $userId,
                'created_at' => $startDate->copy()->addDays($i), // Add $i days to the start date
                'updated_at' => $startDate->copy()->addDays($i), // Add $i days to the start date for consistency
            ]);
        }
    }
}
