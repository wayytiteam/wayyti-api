<?php

namespace Database\Seeders;

use App\Models\Point;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'nicole.amoguis@kodakollectiv.com')->first();
        for ($i=0; $i < 250; $i++) {
            Point::create([
                'user_id' => $user->id,
                'points' => 2
            ]);
        }
    }
}
