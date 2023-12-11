<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    use ImageTrait;
    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>['required','string'],
            'email'=>['required','string','email','unique:members,email'],
            'phone'=>['required','string','unique:members,phone'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Member::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
        ]);
        return $this->success();
    }
    public function edit(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:members,id'],
            'name'=>['required','string'],
            'email'=>['required','string','email','unique:members,email,'.$request->id],
            'phone'=>['required','string','unique:members,phone,'.$request->id],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $member = Member::find($request->id);
            $member->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'phone'=>$request->phone,
            ]);
        $member->save();
        return $this->success();
    }
    public function delete(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:members,id'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $member = Member::find($request->id);
        if ($member->number_project > 0)
            return $this->error('you can not delete this member');
        $member->delete();
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
        $all_members = Member::query();
        if ($request->has('name')) {
            $members = $all_members->where('name', 'like', '%' . $request->name . '%');
        }else{
            $members = $all_members->get();
        }
        return $this->success($members);
    }
}
