<?php

namespace App\Http\Controllers;

use App\Models\Technology;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TechnologyController extends Controller
{
    use ImageTrait;
    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>['required','string'],
            'icon'=>['required','image','mimes:jpeg,jpg,png,svg'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Technology::create([
            'name'=>$request->name,
            'icon' => $this->setImage($request, 'icon', 'technologies'),
        ]);
        return $this->success();
    }
    public function edit(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:technologies,id'],
            'name'=>['required','string', 'unique:technologies,name,' . $request->id],
            'icon'=>['image','mimes:jpeg,jpg,png,svg'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $technology = Technology::find($request->id);
            $technology->update(['name'=>$request->name]);
        if ($request->has('icon')) {
            if ($technology->icon)
                $this->deleteImage('technologies',$technology->icon);
            $technology->update(['icon'=>$this->setImage($request, 'icon', 'technologies')]);
        }
        $technology->save();
        return $this->success();
    }
    public function delete(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:technologies,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $technology = Technology::find($request->id);
        if ($technology->number_project > 0)
            return $this->error('you can not delete this technology because there are projects depended on it');
        if ($technology->icon)
            $this->deleteImage('technologies',$technology->icon);
        $technology->delete();
        return $this->success();
    }
    public function index(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                'id' => ['nullable','exists:technologies,id'],
                'name' => ['nullable','string'],
            ]
        );
        if ($validate->fails())
            return $this->error($validate->errors()->first());
        if ($request->id != null) {
            return $this->success(Technology::find($request->id));
        }
        $all_technologies = Technology::query();
        if ($request->name != null) {
            $all_technologies->where('name', 'like', '%' . $request->name . '%');
        }
        $technologies = $all_technologies->get();
        return $this->success($technologies);
    }

}
