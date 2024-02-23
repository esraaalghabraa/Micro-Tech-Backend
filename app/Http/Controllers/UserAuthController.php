<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:user,access'])->except(['register','login','refreshToken']);
        $this->middleware(['auth:sanctum','ability:refresh'])->only('refreshToken');
    }

    public function register(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password'=>['required','string','min:6','max:50']
            ]
        );
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $user = User::create([
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        $user->token = $user->createToken('accessToken', ['user','access'], now()->addDay())->plainTextToken;
        $user->refresh_token = $user->createToken('refreshToken', ['user','refresh'], now()->addDays(6))->plainTextToken;
        return $this->success($user);
    }

    public function login(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
                'password'=>['required','string','min:6','max:50']
            ]
        );
        if ($validator->fails() || !Auth::guard('user')->validate($request->only('email','password')))
            return $this->error('البريد الالكتروني أو كلمة المرور غير صالحة');

        $user = User::where('email', $request->email)->first();
        $user->token = $user->createToken('accessToken', ['user','access'], now()->addDay())->plainTextToken;
        $user->refresh_token = $user->createToken('refreshToken', ['user','refresh'], now()->addDays(6))->plainTextToken;
        return $this->success($user);
    }

    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        return $this->success();
    }

    public function refreshToken(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('accessToken', ['user','access'], now()->addDay())->plainTextToken;
            $r_token = $request->user()->createToken('refreshToken', ['user','refresh'], now()->addDays(6))->plainTextToken;
            DB::commit();
            return $this->success(['token' => $token, 'refresh_token' => $r_token]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Server failure : ' . $e, 500);
        }
    }
}
