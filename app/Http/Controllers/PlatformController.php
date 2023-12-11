<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlatformController extends Controller
{
    use ImageTrait;
    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>['required','string'],
            'icon'=>['required','image','mimes:jpeg,jpg,png'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Platform::create([
            'name'=>$request->name,
            'icon' => $this->setImage($request, 'icon', 'platforms'),
        ]);
        return $this->success();
    }
    public function edit(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:platforms,id'],
            'name'=>['required','string'],
            'icon'=>['image','mimes:jpeg,jpg,png'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $platform = Platform::find($request->id);
            $platform->update(['name'=>$request->name]);
        if ($request->has('icon')) {
            $path = public_path('assets\images\platforms\\' . $platform->icon);
            unlink($path);
            $platform->update(['icon'=>$this->setImage($request, 'icon', 'platforms')]);
        }
        $platform->save();
        return $this->success();
    }
    public function delete(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:platforms,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $platform = Platform::find($request->id);
        if ($platform->number_project > 0)
            return $this->error('you can not delete this platform');
        $path = public_path('assets\images\platforms\\' . $platform->icon);
        unlink($path);
        $platform->delete();
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
        $all_platforms = Platform::query();

        if ($request->name != null) {
            $all_platforms->where('name', 'like', '%' . $request->name . '%');
        }
            $platforms = $all_platforms->get();
        return $this->success($platforms);
    }

}
