<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TypeController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->name,[
            'name'=>['required','string'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Type::create(['name'=>$request->name]);
        return $this->success();
    }
}
