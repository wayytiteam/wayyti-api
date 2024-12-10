<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PragmaRX\Countries\Package\Countries;
use App\Models\GoogleProduct;

class GoogleProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = new Countries();
        $country_list = $countries->all()->toArray();
        foreach($country_list as $country) {

        }
    }
}
