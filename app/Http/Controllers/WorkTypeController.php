<?php

namespace App\Http\Controllers;

use App\Models\WorkType;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkTypeController extends Controller
{
    use ImageTrait;
    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>['required','string', 'unique:work_types,name'],
            'icon'=>['required','image','mimes:jpeg,jpg,png,svg'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        WorkType::create([
            'name'=>$request->name,
            'icon' => $this->setImage($request, 'icon', 'work_types'),
        ]);
        return $this->success();
    }
    public function edit(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:work_types,id'],
            'name'=>['required','string', 'unique:work_types,name,' . $request->id],
            'icon'=>['image','mimes:jpeg,jpg,png,svg'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $work_type = WorkType::find($request->id);
            $work_type->update(['name'=>$request->name]);
        if ($request->has('icon')) {
            if ($work_type->icon)
                $this->deleteImage('work_types',$work_type->icon);
            $work_type->update(['icon'=>$this->setImage($request, 'icon', 'work_types')]);
        }
        $work_type->save();
        return $this->success();
    }
    public function delete(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:work_types,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $work_type = WorkType::find($request->id);
        if ($work_type->number_project > 0)
            return $this->error('you can not delete this work_type because there are projects depended on it');
        if ($work_type->icon)
            $this->deleteImage('work_types',$work_type->icon);
        $work_type->delete();
        return $this->success();
    }
    public function index(Request $request){
        $validate = Validator::make(
            $request->only('name'),
            [
                'name' => 'nullable|string',
            ]
        );
        if ($validate->fails())
            return $this->error($validate->errors()->first());
        if ($request->id != null) {
            return $this->success(WorkType::find($request->id));
        }
        $all_work_types = WorkType::query();
        if ($request->has('name')) {
            $all_work_types->where('name', 'like', '%' . $request->name . '%');
        }
        $work_types = $all_work_types->get();
        return $this->success($work_types);
    }

}
