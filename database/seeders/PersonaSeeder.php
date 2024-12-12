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
             'image_path' => env('AWS_URL').'/bargain-hunter.png'
            ],
            ['name' => 'Trendsetter',
             'description' => 'The first to have the latest products and trends',
             'image_path' => env('AWS_URL').'/trendsetter.png'
            ],
            ['name' => 'Conscious Shopper',
             'description' => 'Prefers eco-friendly and sustainable products',
             'image_path' => env('AWS_URL').'/conscious+Shopper.png'
            ],
            ['name' => 'Tech Enthusiast',
             'description' => 'Geared towards the latest gadgets and innovations',
             'image_path' => env('AWS_URL').'/tech-enthusiast.png'
            ],
            ['name' => 'Luxury Lover',
             'description' => 'Seeks out premium brands and luxury experiences',
             'image_path' => env('AWS_URL').'/luxury-lover.png'
            ],
        ];
        $persona_list = Persona::get();
        foreach ($personas as $persona) {
            Persona::create($persona);
        }
    }
}
