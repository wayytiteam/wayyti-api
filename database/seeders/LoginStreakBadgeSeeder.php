<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoginStreakBadge;

class LoginStreakBadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            ['name' => 'Newcomer', 'points_required' => 1],
            ['name' => 'Weekender', 'points_required' => 5],
            ['name' => 'Devotee', 'points_required' => 15],
            ['name' => 'Enthusiast', 'points_required' => 30],
            ['name' => 'Stalwart', 'points_required' => 50],
            ['name' => 'Champion', 'points_required' => 75],
            ['name' => 'Hero', 'points_required' => 120],
            ['name' => 'Legend', 'points_required' => 180],
            ['name' => 'Mythic', 'points_required' => 360],
            ['name' => 'Immortal', 'points_required' => 730],
        ];
        foreach($badges as $badge) {
            LoginStreakBadge::create($badge);
        }
    }
}
