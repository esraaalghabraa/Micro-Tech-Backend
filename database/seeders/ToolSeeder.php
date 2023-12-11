<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\MemberProject;
use App\Models\Platform;
use App\Models\PlatformProject;
use App\Models\Project;
use App\Models\TechnologiesProject;
use App\Models\Technology;
use App\Models\Tool;
use App\Models\ToolProject;
use App\Models\WorkType;
use App\Models\WorkTypesProject;
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
        Member::create([
            'name'=>'flutter',
            'email'=>'ahmed@gmail.com',
            'phone'=>'987654321'
            ]);
        Member::create([
            'name'=>'flutter',
            'email'=>'ahmed1@gmail.com',
            'phone'=>'123456789'
        ]);

        for ($i=0;$i<20;$i++) {
            $project = Project::create([
                'title' => '$request->title',
                'description' => '$request->description',
                'functionality' => '$request->functionality',
                'about' => ' $request->about',
                'advantages' => ['eee', 'eee', 'eee',],
                'links' => ['eee', 'eee', 'eee',]
            ]);
            ToolProject::create([
                'tool_id' => 1,
                'project_id' => $project->id
            ]);
            WorkTypesProject::create([
                'work_type_id' => 1,
                'project_id' => $project->id
            ]);
            MemberProject::create([
                'member_id' => 1,
                'project_id' => $project->id
            ]);
            TechnologiesProject::create([
                'technology_id' => 1,
                'project_id' => $project->id
            ]);
            PlatformProject::create([
                'platform_id' => 1,
                'project_id' => $project->id
            ]);
        }
    }
}
