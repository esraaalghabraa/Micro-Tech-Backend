<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeveloperController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->name,[
            'name'=>['required','string'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Developer::create(['name'=>$request->name]);
        return $this->success();
    }
}
