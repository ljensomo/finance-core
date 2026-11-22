<?php

namespace Database\Seeders;

use App\Models\WishlistCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WishlistCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Type 1: Purchase
            ['name' => 'Personal Care & Wardrobe', 'type' => 1, 'icon' => 'fa-solid fa-shirt'],
            ['name' => 'Tech & Gadgets',           'type' => 1, 'icon' => 'fa-solid fa-laptop'],
            ['name' => 'Home & Living',            'type' => 1, 'icon' => 'fa-solid fa-couch'],
            ['name' => 'Kitchen & Dining',         'type' => 1, 'icon' => 'fa-solid fa-utensils'],
            ['name' => 'Health & Fitness',         'type' => 1, 'icon' => 'fa-solid fa-dumbbell'],
            ['name' => 'Hobbies & Recreation',     'type' => 1, 'icon' => 'fa-solid fa-guitar'],
            ['name' => 'Gifts & Events',           'type' => 1, 'icon' => 'fa-solid fa-gift'],

            // Type 2: Travel
            ['name' => 'Destinations & Trips',     'type' => 2, 'icon' => 'fa-solid fa-map-location-dot'],
            ['name' => 'Flights & Transport',      'type' => 2, 'icon' => 'fa-solid fa-plane'],
            ['name' => 'Accommodations',           'type' => 2, 'icon' => 'fa-solid fa-hotel'],
            ['name' => 'Food & Dining',            'type' => 2, 'icon' => 'fa-solid fa-bowl-food'],
            ['name' => 'Tours & Activities',       'type' => 2, 'icon' => 'fa-solid fa-ticket'],
            ['name' => 'Travel Gear & Prep',       'type' => 2, 'icon' => 'fa-solid fa-suitcase-rolling'],
        ];

        foreach ($categories as $category) {
            WishlistCategory::firstOrCreate(
                ['name' => $category['name'], 'type' => $category['type']],
                ['icon' => $category['icon'], 'status' => 1]
            );
        }
    }
}
