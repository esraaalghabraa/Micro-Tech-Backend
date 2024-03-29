<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\ToolProject;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ToolController extends Controller
{
    use ImageTrait;
    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>['required','string', 'unique:tools,name'],
            'icon'=>['required','image','mimes:jpeg,jpg,png,svg'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Tool::create([
            'name'=>$request->name,
            'icon' => $this->setImage($request, 'icon', 'tools'),
            ]);
        return $this->success();
    }
    public function edit(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:tools,id', 'unique:tools,name,' .$request->id ],
            'name'=>['required','string'],
            'icon'=>['image','mimes:jpeg,jpg,png,svg'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $tool = Tool::find($request->id);
        $tool->update(['name'=>$request->name]);
        if ($request->has('icon')) {
            if ($tool->icon)
                $this->deleteImage('tools',$tool->icon);
            $tool->update(['icon'=>$this->setImage($request, 'icon', 'tools')]);
        }
        $tool->save();
        return $this->success();
    }
    public function delete(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:tools,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $tool = Tool::find($request->id);
        if ($tool->number_project > 0)
            return $this->error('you can not delete this tool because there are projects depended on it');
        if ($tool->icon)
            $this->deleteImage('tools',$tool->icon);
        $tool->delete();
        return $this->success();
    }
    public function index(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                'id' => ['nullable','exists:tools,id'],
                'name' => ['nullable','string'],
            ]
        );
        if ($validate->fails())
            return $this->error($validate->errors()->first());
        if ($request->id != null) {
            return $this->success(Tool::find($request->id));
        }
        $all_tools = Tool::query();
        if ($request->has('name')) {
            $all_tools->where('name', 'like', '%' . $request->name . '%');
        }
        $tools = $all_tools->get();
        return $this->success($tools);
    }
}
