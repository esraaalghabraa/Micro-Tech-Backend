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
            'icon'=>['required','image','mimes:jpeg,jpg,png'],
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
            'name'=>['required','string'],
            'icon'=>['image','mimes:jpeg,jpg,png'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $technology = Technology::find($request->id);
            $technology->update(['name'=>$request->name]);
        if ($request->has('icon')) {
            $path = public_path('assets\images\technologies\\' . $technology->icon);
            unlink($path);
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
            return $this->error('you can not delete this technology');
        $path = public_path('assets\images\technologies\\' . $technology->icon);
        unlink($path);
        $technology->delete();
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
        $all_technologies = Technology::query();
        if ($request->has('name')) {
           $all_technologies->where('name', 'like', '%' . $request->name . '%');
        }
            $technologies = $all_technologies->get();
        return $this->success($technologies);
    }

}
