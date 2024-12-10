<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Persona;
use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $personas = [
            ['name' => 'The Bargain Hunter',
             'description' => 'Always on a look out for the best deals and discounts',
             'image_path' => 'https://smartsale-bucket.s3.us-east-2.amazonaws.com/bargain-hunter.png'
            ],
            ['name' => 'Trendsetter',
             'description' => 'The first to have the latest products and trends',
             'image_path' => 'https://smartsale-bucket.s3.us-east-2.amazonaws.com/trendsetter.png'
            ],
            ['name' => 'Conscious Shopper',
             'description' => 'Prefers eco-friendly and sustainable products',
             'image_path' => 'https://smartsale-bucket.s3.us-east-2.amazonaws.com/conscious+Shopper.png'
            ],
            ['name' => 'Tech Enthusiast',
             'description' => 'Geared towards the latest gadgets and innovations',
             'image_path' => 'https://smartsale-bucket.s3.us-east-2.amazonaws.com/tech-enthusiast.png'
            ],
            ['name' => 'Luxury Lover',
             'description' => 'Seeks out premium brands and luxury experiences',
             'image_path' => 'https://smartsale-bucket.s3.us-east-2.amazonaws.com/luxury-lover.png'
            ],
        ];
        $persona_list = Persona::get();
        if(empty($persona_list)) {
            foreach ($personas as $persona) {
                Persona::create($persona);
            }
        }
    }
}
