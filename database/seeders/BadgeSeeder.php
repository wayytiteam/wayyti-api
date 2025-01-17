<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Badge;

use function Ramsey\Uuid\v1;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            ['name' => 'Newcomer', 'type' => 'login', 'points_equivalent' => 1, 'requirement_value' => 1],
            ['name' => 'Weekender','type' => 'login', 'points_equivalent' => 5, 'requirement_value' => 3],
            ['name' => 'Devotee','type' => 'login', 'points_equivalent' => 15, 'requirement_value' => 7],
            ['name' => 'Enthusiast','type' => 'login', 'points_equivalent' => 30, 'requirement_value' => 14],
            ['name' => 'Stalwart','type' => 'login', 'points_equivalent' => 50, 'requirement_value' => 21],
            ['name' => 'Champion','type' => 'login', 'points_equivalent' => 75, 'requirement_value' => 30],
            ['name' => 'Hero','type' => 'login', 'points_equivalent' => 120, 'requirement_value' => 60],
            ['name' => 'Legend','type' => 'login', 'points_equivalent' => 180, 'requirement_value' => 90],
            ['name' => 'Mythic','type' => 'login', 'points_equivalent' => 360, 'requirement_value' => 180],
            ['name' => 'Immortal','type' => 'login', 'points_equivalent' => 730, 'requirement_value' => 365],
            ['name' => 'First Finder','type' => 'tracker', 'points_equivalent' => 5, 'requirement_value' => 1],
            ['name' => 'Initiate Tracker','type' => 'tracker', 'points_equivalent' => 15, 'requirement_value' => 3],
            ['name' => 'Budding Tracker','type' => 'tracker', 'points_equivalent' => 25, 'requirement_value' => 5],
            ['name' => 'Keen Tracker','type' => 'tracker', 'points_equivalent' => 50, 'requirement_value' => 10],
            ['name' => 'Active Tracker','type' => 'tracker', 'points_equivalent' => 75, 'requirement_value' => 15],
            ['name' => 'Dedicated Tracker','type' => 'tracker', 'points_equivalent' => 100, 'requirement_value' => 20],
            ['name' => 'Seasoned Tracker','type' => 'tracker', 'points_equivalent' => 150, 'requirement_value' => 30],
            ['name' => 'Expert Tracker','type' => 'tracker', 'points_equivalent' => 250, 'requirement_value' => 50],
            ['name' => 'Master Tracker','type' => 'tracker', 'points_equivalent' => 375, 'requirement_value' => 75],
            ['name' => 'Tracking Virtuoso','type' => 'tracker', 'points_equivalent' => 500, 'requirement_value' => 100],
            ['name' => 'Penny Pincher','type' => 'savings', 'points_equivalent' => 1, 'requirement_value' => 1],
            ['name' => 'Savvy Saver','type' => 'savings', 'points_equivalent' => 5, 'requirement_value' => 5],
            ['name' => 'Budget Boss','type' => 'savings', 'points_equivalent' => 10, 'requirement_value' => 10],
            ['name' => 'Frugal Fanatic','type' => 'savings', 'points_equivalent' => 25, 'requirement_value' => 25],
            ['name' => 'Cost Cutter','type' => 'savings', 'points_equivalent' => 50, 'requirement_value' => 50],
            ['name' => 'Discount Devotee','type' => 'savings', 'points_equivalent' => 75, 'requirement_value' => 75],
            ['name' => 'Bargain Baron','type' => 'savings', 'points_equivalent' => 100, 'requirement_value' => 100],
            ['name' => 'Deal Detective','type' => 'savings', 'points_equivalent' => 150, 'requirement_value' => 150],
            ['name' => 'Savings Specialist','type' => 'savings', 'points_equivalent' => 200, 'requirement_value' => 200],
            ['name' => 'Savings Master','type' => 'savings', 'points_equivalent' => 500, 'requirement_value' => 500],
            ['name' => 'Savings Guru','type' => 'savings', 'points_equivalent' => 1000, 'requirement_value' => 1000],
            ['name' => 'Friend Inviter','type' => 'referral', 'points_equivalent' => 20, 'requirement_value' => 1],
            ['name' => 'Social Sharer','type' => 'referral', 'points_equivalent' => 100, 'requirement_value' => 5],
            ['name' => 'Community Builder','type' => 'referral', 'points_equivalent' => 200, 'requirement_value' => 10],
            ['name' => 'Deal Sharer','type' => 'share', 'points_equivalent' => 15, 'requirement_value' => 1],
            ['name' => 'Social Saver','type' => 'share', 'points_equivalent' => 150, 'requirement_value' => 10],
            ['name' => 'Influential Saver','type' => 'share', 'points_equivalent' => 375, 'requirement_value' => 25],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 50, 'requirement_value' => 1],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 100, 'requirement_value' => 2],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 250, 'requirement_value' => 3],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 500, 'requirement_value' => 4],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 750, 'requirement_value' => 5],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 1000, 'requirement_value' => 6],
            ['name' => 'Point Starter','type' => 'monthly-draw', 'points_equivalent' => 1500, 'requirement_value' => 7],
            ['name' => 'Starter','type' => 'rank', 'points_equivalent' => 50, 'requirement_value' => 50],
            ['name' => 'Enthusiast','type' => 'rank', 'points_equivalent' => 100, 'requirement_value' => 100],
            ['name' => 'Collector','type' => 'rank', 'points_equivalent' => 250, 'requirement_value' => 250],
            ['name' => 'Achiever','type' => 'rank', 'points_equivalent' => 500, 'requirement_value' => 500],
            ['name' => 'Master','type' => 'rank', 'points_equivalent' => 750, 'requirement_value' => 750],
            ['name' => 'Champoin','type' => 'rank', 'points_equivalent' => 1000, 'requirement_value' => 1000],
            ['name' => 'Legend','type' => 'rank', 'points_equivalent' => 1500, 'requirement_value' => 1500],

        ];
        foreach($badges as $badge) {
            // $check_badge = Badge::where('name', $badge['name'])->first();
            // if(!$check_badge) {
            //     Badge::updateOrCreate($badge,$badge);
            // }
            Badge::updateOrCreate($badge,$badge);
        }
    }
}
