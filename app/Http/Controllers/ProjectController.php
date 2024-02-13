<?php

namespace App\Http\Controllers;

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
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    use ImageTrait;

    public function createFast(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => [ 'string', 'unique:projects,title'],
            'description' => [ 'string'],
            'cover' => [ 'image', 'mimes:jpeg,jpg,png,svg', 'max:1000'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'cover' => $request->cover ? $this->setImage($request, 'cover', 'covers') : $request->cover,
        ]);
        return $this->success();
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => [ 'string', 'unique:projects,title'],
            'description' => [ 'string'],
            'functionality' => [ 'string'],
            'about' => [ 'string'],
            'cover' => ['image', 'mimes:jpeg,jpg,png,svg', 'max:1000'],
            'logo' => ['image', 'mimes:jpeg,jpg,png,svg', 'max:1000'],
            'advantages' => [ 'array'],
            'advantages.*' => [ 'string'],
            'links' => [ 'array', 'min:1'],
            'links.*.link' => [ 'string'],
            'links.*.platform' => [ 'string'],
            'technology_ids' => [ 'array', 'min:1'],
            'technology_ids.*' => [ 'exists:technologies,id'],
            'tool_ids' => [ 'array', 'min:1'],
            'tool_ids.*' => [ 'exists:tools,id'],
            'work_type_ids' => [ 'array', 'min:1'],
            'work_type_ids.*' => [ 'exists:work_types,id'],
            'platform_ids' => [ 'array', 'min:1'],
            'platform_ids.*' => [ 'exists:platforms,id'],
            'member_ids' => [ 'array', 'min:1'],
            'member_ids.*' => [ 'exists:members,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $project = Project::create([
                'title' => $request->title,
                'description' => $request->description,
                'functionality' => $request->functionality,
                'about' => $request->about,
                'advantages' => $request->advantages,
                'links' => $request->links
            ]);
            if ($request->cover != null) {
                    $project->update([
                        'cover' => $this->setImage($request, 'cover', 'covers')                ]);
                    $project->save();
            }
            if ($request->logo != null) {
                    $project->update([
                        'logo' => $this->setImage($request, 'logo', 'logos'),
                    ]);
                    $project->save();
            }
            foreach ($request->tool_ids as $value) {
                ToolProject::create([
                    'tool_id' => $value,
                    'project_id' => $project->id
                ]);
                $tool = Tool::find($value);
                $tool->number_project += 1;
                $tool->save();
            }
            foreach ($request->work_type_ids as $value) {
                WorkTypesProject::create([
                    'work_type_id' => $value,
                    'project_id' => $project->id
                ]);
                $work_type = WorkType::find($value);
                $work_type->number_project += 1;
                $work_type->save();
            }
            foreach ($request->member_ids as $value) {
                MemberProject::create([
                    'member_id' => $value,
                    'project_id' => $project->id
                ]);
                $member = Member::find($value);
                $member->number_project += 1;
                $member->save();
            }
            foreach ($request->technology_ids as $value) {
                TechnologiesProject::create([
                    'technology_id' => $value,
                    'project_id' => $project->id
                ]);
                $technology = Technology::find($value);
                $technology->number_project += 1;
                $technology->save();
            }
            foreach ($request->platform_ids as $value) {
                PlatformProject::create([
                    'platform_id' => $value,
                    'project_id' => $project->id
                ]);
                $platform = Platform::find($value);
                $platform->number_project += 1;
                $platform->save();
            }
            return $this->success(['project_id' => $project->id]);
        } catch (\Exception $e) {
            return $this->error('Server failure : ' . $e, 500);
        }
    }

    public function addImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => [ 'exists:projects,id'],
            'images' => ['array'],
            'images.*' => [ 'image', 'mimes:jpeg,jpg,png,svg', 'max:1000']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $project = Project::find($request->project_id);
        if ($request->images)
            foreach ($request->images as $item) {
                Image::create([
                    'image' => $this->setItemImage($item, 'images'),
                    'project_id' => $request->project_id
                ]);
            }
        $project->save();
        return $this->success();
    }

    public function addFeatures(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => [ 'exists:projects,id'],
            'features' => [ 'array', 'min:1'],
            'features.*.title' => [ 'string'],
            'features.*.description' => [ 'string'],
            'features.*.images' => [ 'array', 'min:1'],
            'features.*.images.*' => [ 'image', 'mimes:jpeg,jpg,png,svg'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->features as $item) {
            $feature = Feature::create([
                'title' => $item['title'],
                'description' => $item['description'],
                'project_id' => $request->project_id,
            ]);
            foreach ($item['images'] as $image) {
                Image::create([
                    'image' => $this->setItemImage($image, 'images'),
                    'project_id' => $request->project_id,
                    'feature_id' => $feature->id
                ]);
            }
        }
        return $this->success();
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => [ 'exists:projects,id'],
            'title' => [ 'string', 'unique:projects,title,' . $request->id],
            'description' => [ 'string'],
            'functionality' => [ 'string'],
            'about' => [ 'string'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,jpg,png,svg', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,svg', 'max:1000'],
            'advantages' => [ 'array'],
            'advantages.*' => [ 'string'],
            'links' => [ 'array', 'min:1'],
            'links.*.link' => [ 'string'],
            'links.*.platform' => [ 'string'],
            'technology_ids' => [ 'array', 'min:1'],
            'technology_ids.*' => [ 'exists:technologies,id'],
            'tool_ids' => [ 'array', 'min:1'],
            'tool_ids.*' => [ 'exists:tools,id'],
            'work_type_ids' => [ 'array', 'min:1'],
            'work_type_ids.*' => [ 'exists:work_types,id'],
            'platform_ids' => [ 'array', 'min:1'],
            'platform_ids.*' => [ 'exists:platforms,id'],
            'member_ids' => [ 'array', 'min:1'],
            'member_ids.*' => [ 'exists:members,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $project = Project::find($request->id);
            if ($request->cover) {
                if ($project->cover != null) {
                    $this->deleteImage('covers', $project->cover);
                    $project->cover = null;
                    $project->save();
                }
                $project->update([
                    'cover' => $this->setImage($request, 'cover', 'covers'),
                ]);
                $project->save();
            }
            if ($request->logo) {
                if ($project->logo != null) {
                    $this->deleteImage('logos', $project->logo);
                    $project->logo = null;
                    $project->save();
                }
                $project->update([
                    'logo' => $this->setImage($request, 'logo', 'logos'),
                ]);
                $project->save();
            }
            $project->update([
                'title' => $request->title,
                'description' => $request->description,
                'functionality' => $request->functionality,
                'about' => $request->about,
                'advantages' => $request->advantages,
                'links' => $request->links
            ]);

            $tool_project = ToolProject::where('project_id', $request->id)->get();
            foreach ($tool_project as $item) {
                $tool = Tool::find($item->tool_id);
                $tool->update([
                    'number_project' => $tool->number_project-1
                ]);
                $tool->save();
                $item->delete();
            }
            foreach ($request->tool_ids as $value) {
               ToolProject::create([
                    'tool_id' => (int)$value,
                    'project_id' => $project->id
                ]);
                $tool = Tool::find((int)$value);
                $tool->update([
                    'number_project' => $tool->number_project+1
                ]);
                $tool->save();
            }
            $work_type_projects = WorkTypesProject::where('project_id', $request->id)->get();
            foreach ($work_type_projects as $item) {
                $work_type = WorkType::find($item->work_type_id);
                $work_type->update([
                    'number_project' => $work_type->number_project-1
                ]);
                $work_type->save();
                $item->delete();
            }
            foreach ($request->work_type_ids as $value) {
                WorkTypesProject::create([
                    'work_type_id' => (int)$value,
                    'project_id' => $project->id
                ]);
                $work_type = WorkType::find((int)$value);
                $work_type->update([
                    'number_project' => $work_type->number_project+1
                ]);
                $work_type->save();
            }

            $member_project = MemberProject::where('project_id', $request->id)->get();
            foreach ($member_project as $item) {
                $member = Member::find($item->member_id);
                $member->update([
                    'number_project' => $member->number_project-1
                ]);
                $member->save();
                $item->delete();
            }
            foreach ($request->member_ids as $value) {
                MemberProject::create([
                    'member_id' => (int)$value,
                    'project_id' => $project->id
                ]);
                $member = Member::find((int)$value);
                $member->update([
                    'number_project' => $member->number_project+1
                ]);
                $member->save();
            }

            $technology_project = TechnologiesProject::where('project_id', $request->id)->get();
            foreach ($technology_project as $item) {
                $technology = Technology::find($item->technology_id);
                $technology->update([
                    'number_project' => $technology->number_project-1
                ]);
                $technology->save();
                $item->delete();
            }
            foreach ($request->technology_ids as $value) {
                TechnologiesProject::create([
                    'technology_id' => (int)$value,
                    'project_id' => $project->id
                ]);
                $technology = Technology::find((int)$value);
                $technology->update([
                    'number_project' => $technology->number_project+1
                ]);
                $technology->save();
            }

            $platform_project = PlatformProject::where('project_id', $request->id)->get();
            foreach ($platform_project as $item) {
                $platform = Platform::find($item->platform_id);
                $platform->update([
                    'number_project' => $platform->number_project-1
                ]);
                $platform->save();
                $item->delete();
            }
            foreach ($request->platform_ids as $value) {
                PlatformProject::create([
                    'platform_id' => (int)$value,
                    'project_id' => $project->id
                ]);
                $platform = Platform::find((int)$value);
                $platform->update([
                    'number_project' => $platform->number_project+1
                ]);
                $platform->save();
            }
            return $this->success(['project_id' => $project->id]);
        } catch (\Exception $e) {
            return $this->error('Server failure : ' . $e, 500);
        }
    }

    public function editImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => [ 'exists:projects,id'],
            'images' => ['array'],
            'images.*.id' => [ 'exists:images,id'],
            'images.*.image' => [ 'image', 'mimes:jpeg,jpg,png,svg', 'max:1000']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $project = Project::with('images')->find($request->project_id);
        if ($request->images) {
            $images_project = Image::where('project_id', $request->project_id)->get();

            foreach ($request->images as $item) {
                $image = $images_project->find($item['id']);
                if ($image) {
                    $this->deleteImage('images', $image['image']);
                }
                $image->image = $this->setItemImage($item['image'], 'images');
                $image->save();
            }
        }
        $project->save();
        return $this->success();
    }

    public function editFeatures(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'features' => [ 'array', 'min:1'],
            'features.*.id' => [ 'exists:features,id'],
            'features.*.title' => [ 'string'],
            'features.*.description' => [ 'string'],
            'features.*.images' => [ 'array', 'min:1'],
            'features.*.images.*.id' => [ 'exists:images,id'],
            'features.*.images.*.image' => [ 'image', 'mimes:jpeg,jpg,png,svg', 'max:1000'],
            ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->features as $item) {
            $feature = Feature::find($item['id']);
            $feature->update([
                'title' => $item['title'],
                'description' => $item['description'],
            ]);
            foreach ($item['images'] as $image_item) {
                $image = Image::find($image_item['id']);
                if ($image->image != null) {
                    $this->deleteImage('images', $image->image);
                    $image->image = null;
                    $image->save();
                }
                $image->image = $this->setItemImage($image_item['image'], 'images');
                $image->save();
            }
            $feature->save();
        }
        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => [ 'exists:projects,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $project = Project::find($request->id);
        $platform_project = PlatformProject::where('project_id', $request->id)->get();
        foreach ($platform_project as $item) {
            $item->delete();
        }
        $work_type_projects = WorkTypesProject::where('project_id', $request->id)->get();
        foreach ($work_type_projects as $item) {
            $item->delete();
        }
        $tool_project = ToolProject::where('project_id', $request->id)->get();
        foreach ($tool_project as $item) {
            $item->delete();
        }
        $member_project = MemberProject::where('project_id', $request->id)->get();
        foreach ($member_project as $item) {
            $item->delete();
        }
        $technology_project = TechnologiesProject::where('project_id', $request->id)->get();
        foreach ($technology_project as $item) {
            $item->delete();
        }
        if ($project->cover != null)
            $this->deleteImage('covers', $project->cover);
        if ($project->logo != null)
            $this->deleteImage('logos', $project->logo);
        if ($project->images)
            foreach ($project->images as $image) {
                $this->deleteImage('images', $image->image);
            }
        foreach ($project->features as $feature) {
            $feature->delete();
        }
        $project->delete();
        return $this->success();
    }

    public function activate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['exists:projects,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $project = Project::find($request->id);
        $project->active = !$project->active;
        $project->save();
        return $this->success();
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['nullable','exists:platforms,id'],
            'title' => ['string'],
            'active' => ['boolean'],
            'technology_id' => ['exists:technologies,id'],
            'work_type_id' => ['exists:work_types,id'],
            'platform_id' => ['exists:platforms,id'],
            'member_id' => ['exists:members,id']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors());
        $all_projects = Project::query();
        if ($request->id != null) {
            $all_projects->where('id',$request->id);
        }
        if ($request->has('title'))
            $all_projects->where('title', 'like', '%' . $request->title . '%');
        if ($request->has('active'))
            $all_projects->where('active',$request->active);
        if ($request->has('technology_id')) {
            $all_projects->whereHas('technologies', function ($q) use ($request) {
                $q->where('technologies.id', $request->technology_id);
            });
        }
        if ($request->has('work_type_id'))
            $all_projects->whereHas('workTypes', function ($q) use ($request) {
                $q->where('work_type_id', $request->work_type_id);
            });
        if ($request->has('platform_id'))
            $all_projects->whereHas('platforms', function ($q) use ($request) {
                $q->where('platform_id', $request->platform_id);
            });
        if ($request->has('member_id'))
            $all_projects->whereHas('members', function ($q) use ($request) {
                $q->where('member_id', $request->member_id);
            });
        $records = $request->records_number ? $request->records_number : 10;
        $projects = $all_projects
            ->with(['features'=>function($q){
                return $q->with('images');
            }])
            ->with(['images'=>function($q){
                return $q->where('images.feature_id',null);
            }])
            ->with('technologies')
            ->with('tools')
            ->with('workTypes')
            ->with('platforms')
            ->with('members')
            ->paginate($records);
        return $this->success($projects);
    }

    function getGroups(){
        return $this->success([
            "technologies"=>Technology::select('id','name')->get()->all(),
            "toolsKit"=>Tool::select('id','name')->get()->all(),
            "wokeTypes"=>WorkType::select('id','name')->get()->all(),
            "platforms"=>Platform::select('id','name')->get()->all()
        ]);
    }


}
