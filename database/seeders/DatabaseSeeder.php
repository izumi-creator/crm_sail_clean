<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 他のSeederをここに追加
        $this->call([
            UserSeeder::class,
            CourtSeeder::class,
            InsuranceCompanySeeder::class,
            RoomSeeder::class,
        ]);
    }
}