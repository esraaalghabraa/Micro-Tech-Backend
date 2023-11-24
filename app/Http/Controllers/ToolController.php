<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ToolController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->name,[
            'name'=>['required','string'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Tool::create(['name'=>$request->name]);
        return $this->success();
    }
}
