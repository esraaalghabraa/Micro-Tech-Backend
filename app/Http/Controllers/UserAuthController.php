<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:user,access'])->only('logout');
        $this->middleware(['auth:sanctum', 'ability:refresh'])->only('refreshToken');
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'max:50']
            ]
        );
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $user->token = $user->createToken('accessToken', ['user', 'access'], now()->addDay())->plainTextToken;
        $user->refresh_token = $user->createToken('refreshToken', ['user', 'refresh'], now()->addDays(6))->plainTextToken;
        return $this->success($user);
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
                'password' => ['required', 'string', 'min:6', 'max:50']
            ]
        );
        if ($validator->fails() || !Auth::guard('user')->validate($request->only('email', 'password')))
            return $this->error('البريد الالكتروني أو كلمة المرور غير صالحة');

        $user = User::where('email', $request->email)->first();
        $user->token = $user->createToken('accessToken', ['user', 'access'], now()->addDay())->plainTextToken;
        $user->refresh_token = $user->createToken('refreshToken', ['user', 'refresh'], now()->addDays(6))->plainTextToken;
        return $this->success($user);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->success();
    }

    public function refreshToken(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('accessToken', ['user', 'access'], now()->addDay())->plainTextToken;
            $r_token = $request->user()->createToken('refreshToken', ['user', 'refresh'], now()->addDays(6))->plainTextToken;
            DB::commit();
            return $this->success(['token' => $token, 'refresh_token' => $r_token]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Server failure : ' . $e, 500);
        }
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:6', 'max:50'],
            'reaching_way' => ['required', 'string'],
            'inquiry_type' => ['required', 'string'],
            'message' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            DB::beginTransaction();
            $message = Message::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'reaching_way' => $request->reaching_way,
                'inquiry_type' => $request->inquiry_type,
                'message' => $request->message,
            ]);
            $details = [
                'message' => $request->message,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'inquiry_type' => $request->inquiry_type,
                'reaching_way' => $request->reaching_way,
            ];
            Mail::to('contact@microtechdev.com')->send(new ContactMail($details));
            DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function getHomeProjects()
    {
        $projects = Project::
            where('active', 1)
            ->where('special', 1)
            ->take(8)->select('id', 'title','description','category','likes')
            ->get();
        //TODO more details of project
        return $this->success($projects);
    }
    public function getProjects(Request $request)
    {
        $projects = Project::query();
        $projects->where('active', 1)
            ->where('special', 1)
            ->select('id', 'title','description','category','likes');
        if ($request->has('category')) {
            $projects->where('category', $request->category);
        }
        $projects = $projects->get();
        return $this->success($projects);
    }
}
