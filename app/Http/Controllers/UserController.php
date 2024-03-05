<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:user,access']);
    }

    public function changeLike(Request $request){
        $validator = Validator::make($request->all(),[
            'project_id'=>['exists:projects,id']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $like = Like::where(['user_id'=>Auth::user()->id,'project_id'=>$request->project_id])->first();
        $project = Project::find($request->project_id);
        if (!$like) {
            $like = Like::create([
                'user_id' => Auth::user()->id,
                'project_id' => $request->project_id,
            ]);
            $project->update([
                'likes'=>$project->likes++
            ]);
            $project->save();
        }
        else{
            $project->update([
                'likes'=>$project->likes--
            ]);
            $project->save();
            $like->delete();
        }
        return $this->success();
    }

    public function addComment(Request $request){
        $validator = Validator::make($request->all(),[
            'project_id'=>['required','exists:projects,id'],
            'comment'=>['required','string']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Comment::create([
            'user_id'=>Auth::user()->id,
            'project_id'=>$request->project_id,
            'comment'=>$request->comment
        ]);
        return $this->success();
    }

    public function editComment(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:comments,id'],
            'comment'=>['required','string']
        ])  ;
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $comment = Comment::find($request->id);
        if (Auth::user()->id != $comment->user_id)
            return $this->error();
        $comment->update([
            'comment'=>$request->comment
        ]);
        $comment->save();
        return $this->success();
    }

    public function deleteComment(Request $request){
        $validator = Validator::make($request->all(),[
            'id'=>['required','exists:comments,id']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $comment = Comment::find($request->id);
        if (Auth::user()->id != $comment->user_id)
            return $this->error();
        $comment->delete();
        return $this->success();
    }

}
