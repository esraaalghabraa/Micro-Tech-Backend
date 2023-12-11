<?php

namespace App\Http\Controllers;

use App\Models\Feature;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\FileExists;
use function PHPUnit\Framework\isEmpty;

class ProjectController extends Controller
{
    use ImageTrait;

    public function createShort(Request $request)
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
            'cover' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10000'],
            'logo' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:10000'],
            'hero_images' => ['required', 'array'],
            'hero_images.*' => ['required', 'image']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $project = Project::find($request->project_id);
        $images=[];
        foreach ($request->hero_images as $i=>$item) {
            $images[$i]=$this->setItemImage($item,'hero_images');
        }
        $project->update([
            'cover' => $this->setImage($request, 'cover', 'covers'),
            'logo' => $this->setImage($request, 'logo', 'logos'),
            'hero_images' =>  $images,
        ]);
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
        foreach ($request->features as $feature) {
            $feature_images=[];
            foreach ($feature['images'] as $i=>$item) {
                $feature_images[$i]=$this->setItemImage($item,'feature_images');
            }
            Feature::create([
                'title' => $feature['title'],
                'description' => $feature['description'],
                'images'=>$feature_images,
                'project_id' => $request->project_id,
            ]);
        }
        return $this->success();
    }

    public function editFeatures(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'features' => ['required', 'array', 'min:1'],
            'features.*.id' => ['required', 'exists:features,id'],
            'features.*.title' => ['required', 'string'],
            'features.*.description' => ['required', 'string'],
            'features.*.images' => ['required', 'array', 'min:1'],
            'features.*.images.*' => ['required'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

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

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:projects,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $project = Project::find($request->id);
        foreach ($project->features as $value)
            $value->delete();
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
        $project->delete();
        return $this->success();
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['string'],
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
        $records = $request->Records_Number ? $request->Records_Number : 10;
        $projects = $all_projects
            ->with('features')
            ->with('technologies')
            ->with('tools')
            ->with('workTypes')
            ->with('platforms')
            ->with('members')
            ->latest()->paginate($records);
        return $this->success($projects);
    }

    public function addMembers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'member_ids' => ['required', 'array', 'min:1'],
            'member_ids.*' => ['exists:members,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->member_ids as $value) {
            MemberProject::create([
                'member_id' => $value,
                'project_id' => $request->project_id
            ]);
            $member = Member::find($value);
            $member->number_project += 1;
            $member->save();
        }
        return $this->success();
    }

    public function addPlatforms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'platform_ids' => ['required', 'array', 'min:1'],
            'platform_ids.*' => ['exists:platforms,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->platform_ids as $value) {
            PlatformProject::create([
                'platform_id' => $value,
                'project_id' => $request->project_id
            ]);
            $platform = Platform::find($value);
            $platform->number_project += 1;
            $platform->save();
        }
        return $this->success();
    }

    public function addToolsKit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'tool_ids' => ['required', 'array', 'min:1'],
            'tool_ids.*' => ['exists:tools,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->tool_ids as $value) {
            ToolProject::create([
                'tool_id' => $value,
                'project_id' => $request->project_id
            ]);
            $tool = Tool::find($value);
            $tool->number_project += 1;
            $tool->save();
        }
        return $this->success();
    }

    public function addWorkTypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'work_type_ids' => ['required', 'array', 'min:1'],
            'work_type_ids.*' => ['exists:work_types,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->work_type_id as $value) {
            WorkType::create([
                'work_type_id' => $value,
                'project_id' => $request->project_id
            ]);
            $work_type = WorkType::find($value);
            $work_type->number_project += 1;
            $work_type->save();
        }
        return $this->success();
    }

    public function addTechnologies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'technology_ids' => ['required', 'array', 'min:1'],
            'technology_ids.*' => ['exists:technologies,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->technology_ids as $value) {
            TechnologiesProject::create([
                'technology_id' => $value,
                'project_id' => $request->project_id
            ]);
            $technology = Technology::find($value);
            $technology->number_project += 1;
            $technology->save();
        }
        return $this->success();
    }

    public function deleteMembers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'member_ids' => ['required', 'array', 'min:1'],
            'member_ids.*' => ['exists:members,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->member_ids as $value) {
            MemberProject::create([
                'member_id' => $value,
                'project_id' => $request->project_id
            ]);
            $member = Member::find($value);
            $member->number_project += 1;
            $member->save();
        }
        return $this->success();
    }

    public function deletePlatforms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'platform_ids' => ['required', 'array', 'min:1'],
            'platform_ids.*' => ['exists:platforms,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->platform_ids as $value) {
            PlatformProject::create([
                'platform_id' => $value,
                'project_id' => $request->project_id
            ]);
            $platform = Platform::find($value);
            $platform->number_project += 1;
            $platform->save();
        }
        return $this->success();
    }

    public function deleteToolsKit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'tool_ids' => ['required', 'array', 'min:1'],
            'tool_ids.*' => ['exists:tools,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->tool_ids as $value) {
            ToolProject::create([
                'tool_id' => $value,
                'project_id' => $request->project_id
            ]);
            $tool = Tool::find($value);
            $tool->number_project += 1;
            $tool->save();
        }
        return $this->success();
    }

    public function deleteWorkTypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'work_type_ids' => ['required', 'array', 'min:1'],
            'work_type_ids.*' => ['exists:work_types,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->work_type_id as $value) {
            WorkType::create([
                'work_type_id' => $value,
                'project_id' => $request->project_id
            ]);
            $work_type = WorkType::find($value);
            $work_type->number_project += 1;
            $work_type->save();
        }
        return $this->success();
    }

    public function deleteTechnologies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'technology_ids' => ['required', 'array', 'min:1'],
            'technology_ids.*' => ['exists:technologies,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        foreach ($request->technology_ids as $value) {
            TechnologiesProject::create([
                'technology_id' => $value,
                'project_id' => $request->project_id
            ]);
            $technology = Technology::find($value);
            $technology->number_project += 1;
            $technology->save();
        }
        return $this->success();
    }

    public function editImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'exists:projects,id'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10000'],
            'image_ids' => ['nullable', 'array'],
            'image_ids.*' => ['required', 'exists:project_images,id']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $project = Project::find($request->project_id);
        if ($request->has('cover')) {
            if ($project->cover) {
                $path = public_path('assets\images\covers\\' . $project->cover);
                unlink($path);
            }
            $project->update([
                'cover' => $this->setImage($request, 'cover', 'covers'),
            ]);
            $project->save();
        }
        if ($request->has('logo'))
            if ($project->logo) {
                $path = public_path('assets\images\logos\\' . $project->logo);
                unlink($path);
            }
        $project->update([
            'logo' => $this->setImage($request, 'logo', 'logos'),
        ]);
        $project->save();
        if ($request->has('image_ids'))
            foreach ($request->image_ids as $value) {
                $image = ProjectImage::find($value);
                $path = public_path('assets\images\screens\\' . $image->image);
                unlink($path);
                $image->update([
                    'image' => $this->setImage($request, 'image', 'screens'),
                ]);
            }
        return $this->success();
    }

    public function editAdvantage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'advantage_id' => ['required', 'exists:advantages,id'],
            'name' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Advantage::find($request->advantage_id)->update(['name' => $request->name]);
        return $this->success();
    }

    public function deleteAdvantage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'advantage_id' => ['required', 'exists:advantages,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Advantage::find($request->advantage_id)->delete();
        return $this->success();
    }

    public function editFeature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feature_id' => ['required', 'exists:features,id'],
            'title' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $feature = Feature::find($request->feature_id);
        if ($request->has('title'))
            $feature->update(['name' => $request->name]);
        if ($request->has('description'))
            $feature->update(['name' => $request->description]);
        return $this->success();
    }

    public function deleteFeature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feature_id' => ['required', 'exists:features,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Feature::find($request->feature_id)->delete();
        return $this->success();
    }


    /**
     * create project
     * get Projects with filtering by title|technology|work_type|platform|member
     * add Members for special project
     * add Platforms for special project
     * add ToolsKit for special project
     * add WorkTypes for special project
     * add Technologies for special project
     * add Advantages for special project
     * add Features for special project
     * add Images for special project such as cover|logo|screens
     * edit Advantage for special project
     * edit Feature for special project
     * edit Images for special project such as cover|logo|screens
     * delete Members for special project
     * delete Platforms for special project
     * delete ToolsKit for special project
     * delete WorkTypes for special project
     * delete Technologies for special project
     **/

    //TODO
    // add update function same create
    // add update function same addImages
}
