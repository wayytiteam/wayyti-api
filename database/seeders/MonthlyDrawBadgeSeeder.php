<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Badge;

class MonthlyDrawBadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $monthly_draw_badges = [
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 50, 'requirement_value' => 1],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 100, 'requirement_value' => 2],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 150, 'requirement_value' => 3],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 200, 'requirement_value' => 4],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 250, 'requirement_value' => 5],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 300, 'requirement_value' => 6],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 350, 'requirement_value' => 7],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 400, 'requirement_value' => 8],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 450, 'requirement_value' => 9],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 500, 'requirement_value' => 10],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 550, 'requirement_value' => 11],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 600, 'requirement_value' => 12],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 650, 'requirement_value' => 13],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 700, 'requirement_value' => 14],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 750, 'requirement_value' => 15],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 800, 'requirement_value' => 16],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 850, 'requirement_value' => 17],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 900, 'requirement_value' => 18],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 950, 'requirement_value' => 19],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 1000, 'requirement_value' => 20],
        ];
        foreach($monthly_draw_badges as $badge) {
            Badge::updateOrCreate([
                'name' => 'Point Starter',
                'requirement_value' => $badge['requirement_value']
            ], $badge);
        }
    }
}
