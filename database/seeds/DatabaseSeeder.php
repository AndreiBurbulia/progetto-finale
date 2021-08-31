<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            OrderSeeder::class,
            RestaurantSeeder::class,
            CategorySeeder::class,
            PlateSeeder::class,
        ]);
    }
}