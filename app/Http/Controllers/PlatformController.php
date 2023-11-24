<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlatformController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->name,[
            'name'=>['required','string'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Platform::create(['name'=>$request->name]);
        return $this->success();
    }
}
