<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InsuranceCompany;

class InsuranceCompanySeeder extends Seeder
{
    public function run(): void
    {
        InsuranceCompany::factory()->count(10)->create();
    }
}
