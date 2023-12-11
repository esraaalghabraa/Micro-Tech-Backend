<?php

namespace Database\Seeders;

use App\Models\Advantage;
use App\Models\Platform;
use App\Models\Project;
use App\Models\Technology;
use App\Models\Tool;
use App\Models\WorkType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tool::create([
            'name'=>'flutter',
            'icon'=>'6570ada8a1124.png'
            ]);
        Tool::create([
            'name'=>'flutter',
            'icon'=>'6570ada8a1124.png'
            ]);

        Technology::create([
            'name'=>'flutter',
            'icon'=>'6570ada8a1124.png'
            ]);
        Technology::create([
            'name'=>'flutter',
            'icon'=>'6570ada8a1124.png'
            ]);

        WorkType::create([
            'name'=>'flutter',
            'icon'=>'6570ada8a1124.png'
            ]);
        WorkType::create([
            'name'=>'flutter',
            'icon'=>'6570ada8a1124.png'
            ]);

        Platform::create([
            'name'=>'flutter',
            'icon'=>'6570ada8a1124.png'
            ]);
        Platform::create([
            'name'=>'flutter',
            'icon'=>'6570ada8a1124.png'
            ]);

    }
}
