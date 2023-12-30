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
            'title' => ['required', 'string', 'unique:projects,title'],
            'description' => ['required', 'string'],
            'cover' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:10000'],
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
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'functionality' => ['required', 'string'],
            'about' => ['required', 'string'],
            'advantages' => ['required', 'array'],
            'advantages.*' => ['required', 'string'],
            'links' => ['required', 'array', 'min:1'],
            'links.*.link' => ['required', 'string'],
            'links.*.platform' => ['required', 'string'],
            'technology_ids' => ['required', 'array', 'min:1'],
            'technology_ids.*' => ['required', 'exists:technologies,id'],
            'tool_ids' => ['required', 'array', 'min:1'],
            'tool_ids.*' => ['required', 'exists:tools,id'],
            'work_type_ids' => ['required', 'array', 'min:1'],
            'work_type_ids.*' => ['required', 'exists:work_types,id'],
            'platform_ids' => ['required', 'array', 'min:1'],
            'platform_ids.*' => ['required', 'exists:platforms,id'],
            'member_ids' => ['required', 'array', 'min:1'],
            'member_ids.*' => ['required', 'exists:members,id'],
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
            'project_id' => ['required', 'exists:projects,id'],
            'cover' => ['image', 'mimes:jpeg,jpg,png', 'max:10000'],
            'logo' => ['image', 'mimes:jpeg,jpg,png', 'max:10000'],
            'images' => ['array'],
            'images.*' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:10000']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $project = Project::find($request->project_id);
        if ($request->cover)
            $project->update([
                'cover' => $this->setImage($request, 'cover', 'covers'),
            ]);
        if ($request->logo)
            $project->update([
                'logo' => $this->setImage($request, 'logo', 'logos'),
            ]);
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
            'project_id' => ['required', 'exists:projects,id'],
            'features' => ['required', 'array', 'min:1'],
            'features.*.title' => ['required', 'string'],
            'features.*.description' => ['required', 'string'],
            'features.*.images' => ['required', 'array', 'min:1'],
            'features.*.images.*' => ['required', 'image', 'mimes:jpeg,jpg,png'],
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
            'id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'unique:projects,title,' . $request->id],
            'description' => ['required', 'string'],
            'functionality' => ['required', 'string'],
            'about' => ['required', 'string'],
            'advantages' => ['required', 'string'],
            'advantages.*' => ['required', 'string'],
            'links' => ['required', 'array', 'min:1'],
            'links.*.link' => ['required', 'string'],
            'links.*.platform' => ['required', 'string'],
            'technology_ids' => ['required', 'array', 'min:1'],
            'technology_ids.*' => ['required', 'exists:technologies,id'],
            'tool_ids' => ['required', 'array', 'min:1'],
            'tool_ids.*' => ['required', 'exists:tools,id'],
            'work_type_ids' => ['required', 'array', 'min:1'],
            'work_type_ids.*' => ['required', 'exists:work_types,id'],
            'platform_ids' => ['required', 'array', 'min:1'],
            'platform_ids.*' => ['required', 'exists:platforms,id'],
            'member_ids' => ['required', 'array', 'min:1'],
            'member_ids.*' => ['required', 'exists:members,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $project = Project::find($request->id);
            $project->update([
                'title' => $request->title,
                'description' => $request->description,
                'functionality' => $request->functionality,
                'about' => $request->about,
                'advantages' => $request->advantages,
                'links' => $request->links
            ]);

            $tools = Tool::query();
            $tool_project = ToolProject::where('project_id', $request->id)->get();
            foreach ($tool_project as $item) {
                $tool = $tools->find($item->tool_id);
                $tool->number_project = 0;
                $tool->save();
                $item->delete();
            }
            foreach ($request->tool_ids as $value) {
                ToolProject::create([
                    'tool_id' => $request->tool_ids[0],
                    'project_id' => $project->id
                ]);
                $tool = $tools->find($request->tool_ids[0]);
                $tool->number_project += 1;
                $tool->save();
            }

            $work_types = WorkType::query();
            $work_type_projects = WorkTypesProject::where('project_id', $request->id)->get();
            foreach ($work_type_projects as $item) {
                $work_type = $work_types->find($item->work_type_id);
                $work_type->number_project = 0;
                $work_type->save();
                $item->delete();
            }
            foreach ($request->work_type_ids as $value) {
                WorkTypesProject::create([
                    'work_type_id' => $request->work_type_ids[0],
                    'project_id' => $project->id
                ]);
                $work_type = $work_types->find($request->work_type_ids[0]);
                $work_type->number_project += 1;
                $work_type->save();
            }

            $members = Member::query();
            $member_project = MemberProject::where('project_id', $request->id)->get();
            foreach ($member_project as $item) {
                $member = $members->find($item->member_id);
                $member->number_project = 0;
                $member->save();
                $item->delete();
            }
            foreach ($request->member_ids as $value) {
                MemberProject::create([
                    'member_id' => $request->member_ids [0],
                    'project_id' => $project->id
                ]);
                $member = $members->find($request->member_ids [0]);
                $member->number_project += 1;
                $member->save();
            }

            $technologies = Technology::query();
            $technology_project = TechnologiesProject::where('project_id', $request->id)->get();
            foreach ($technology_project as $item) {
                $technology = $technologies->find($item->technology_id);
                $technology->number_project = 0;
                $technology->save();
                $item->delete();
            }
            foreach ($request->technology_ids as $value) {
                TechnologiesProject::create([
                    'technology_id' => $request->technology_ids[0],
                    'project_id' => $project->id
                ]);
                $technology = $technologies->find($request->technology_ids[0]);
                $technology->number_project += 1;
                $technology->save();
            }

            $platforms = Platform::query();
            $platform_project = PlatformProject::where('project_id', $request->id)->get();
            foreach ($platform_project as $item) {
                $platform = $platforms->find($item->platform_id);
                $platform->number_project = 0;
                $platform->save();
                $item->delete();
            }
            foreach ($request->platform_ids as $value) {
                PlatformProject::create([
                    'platform_id' => $request->platform_ids[0],
                    'project_id' => $project->id
                ]);
                $platform = $platforms->find($request->platform_ids[0]);
                $platform->number_project += 1;
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
            'project_id' => ['required', 'exists:projects,id'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10000'],
            'images' => ['array'],
            'images.*.id' => ['required', 'exists:images,id'],
            'images.*.image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:10000']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $project = Project::with('images')->find($request->project_id);
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
            'features' => ['required', 'array', 'min:1'],
            'features.*.id' => ['required', 'exists:features,id'],
            'features.*.title' => ['required', 'string'],
            'features.*.description' => ['required', 'string'],
            'features.*.images' => ['required', 'array', 'min:1'],
            'features.*.images.*.id' => ['required', 'exists:images,id'],
            'features.*.images.*.image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:10000'],
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
            'id' => ['required', 'exists:projects,id'],
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
            'id' => ['required', 'exists:projects,id'],
            'active' => ['required','boolean'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $project = Project::find($request->id);
        $project->active = $request->active;
        $project->save();
        return $this->success();
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

}
