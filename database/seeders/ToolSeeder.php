<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Image;
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
            'name' => 'flutter',
            'icon' => '6578576442cc4.png'
        ]);
        Tool::create([
            'name' => 'flutter',
            'icon' => '6578576442cc4.png'
        ]);

        Technology::create([
            'name' => 'flutter',
            'icon' => '6578576442cc4.png'
        ]);
        Technology::create([
            'name' => 'flutter',
            'icon' => '6578576442cc4.png'
        ]);

        WorkType::create([
            'name' => 'flutter',
            'icon' => '6578576442cc4.png'
        ]);
        WorkType::create([
            'name' => 'flutter',
            'icon' => '6578576442cc4.png'
        ]);

        Platform::create([
            'name' => 'flutter',
            'icon' => '6578576442cc4.png'
        ]);
        Platform::create([
            'name' => 'flutter',
            'icon' => '6578576442cc4.png'
        ]);
        Member::create([
            'name' => 'flutter',
            'email' => 'ahmed@gmail.com',
            'phone' => '987654321'
        ]);
        Member::create([
            'name' => 'flutter',
            'email' => 'ahmed1@gmail.com',
            'phone' => '123456789'
        ]);

        for ($i = 0; $i < 5; $i++) {
            $project = Project::create([
                'title' => 'title',
                'description' => 'description',
                'functionality' => 'functionality',
                'about' => ' about',
                'advantages' => ['eee', 'eee', 'eee',],
                'links' => ['eee', 'eee', 'eee',],
                'cover' => '6578576442cc4.png',
                'logo' => '6578576442cc4.png'
            ]);
            ToolProject::create([
                'tool_id' => 1,
                'project_id' => $project->id
            ]);
            $tool = Tool::find(1);
            $tool->update([
                'number_project' => $tool->number_project += 1
            ]);
            WorkTypesProject::create([
                'work_type_id' => 1,
                'project_id' => $project->id
            ]);
            $WorkType = WorkType::find(1);
            $WorkType->update([
                'number_project' => $WorkType->number_project += 1
            ]);
            MemberProject::create([
                'member_id' => 1,
                'project_id' => $project->id
            ]);
            $Member = Member::find(1);
            $Member->update([
                'number_project' => $Member->number_project += 1
            ]);
            TechnologiesProject::create([
                'technology_id' => 1,
                'project_id' => $project->id
            ]);
            $Technology = Technology::find(1);
            $Technology->update([
                'number_project' => $Technology->number_project += 1
            ]);
            PlatformProject::create([
                'platform_id' => 1,
                'project_id' => $project->id
            ]);
            $Platform = Platform::find(1);
            $Platform->update([
                'number_project' => $Platform->number_project += 1
            ]);
            Image::create([
                'image' => '6578576442cc4.png',
                'project_id' => $project->id
            ]);
            $feature = Feature::create([
                'title' => 'title',
                'description' => 'description',
                'project_id' => $project->id
            ]);
            Image::create([
                'image' => '6578576442cc4.png',
                'project_id' => $project->id,
                'feature_id' => $feature->id,
            ]);
        }
    }
}
