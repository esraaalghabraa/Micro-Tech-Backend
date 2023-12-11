<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Platform::create(['name'=>'android']);
        Platform::create(['name'=>'ios']);
        Platform::create(['name'=>'web']);
        Platform::create(['name'=>'desktop']);
    }
}
