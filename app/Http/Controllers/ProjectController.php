<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\FeaturesProject;
use App\Models\PlatformProject;
use App\Models\Project;
use App\Models\ToolProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'cover_image' => ['image', 'max:1000', 'mimes:jpg,jpeg,png,jfif'],
            'about' => ['required', 'string'],
            'start_date' => ['date'],
            'end_date' => ['date'],
            'features' => ['required', 'array', 'min:1'],
            'features.title' => ['required', 'string'],
            'features.description' => ['required', 'string'],
            'tools_ids' => ['required', 'array', 'min:1'],
            'tools_ids.*' => ['exists:tools,id'],
            'platforms_ids' => ['required', 'array', 'min:1'],
            'platforms.*' => ['exists:platforms,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            DB::beginTransaction();
            $project = Project::create([
                'title' => $request->title,
                'description' => $request->description,
                'about' => $request->about,
                'cover_image' => $request->cover_image,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            foreach ($request->tools_ids as $key => $value) {
                ToolProject::create([
                    'tool_id' => $value,
                    'project_id' => $project->id
                ]);
            }
            foreach ($request->platforms_ids as $key => $value) {
                PlatformProject::create([
                    'platform_id' => $value,
                    'project_id' => $project->id
                ]);
            }
            foreach ($request->features as $key => $value) {
                $features = Feature::create([
                    'title' => $value->title,
                    'description' => $value->description
                ]);
                FeaturesProject::create([
                    'feature_id' => $features->id,
                    'project_id' => $project->id
                ]);
            }

            DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Server failure : ' . $e, 500);
        }
    }
}
