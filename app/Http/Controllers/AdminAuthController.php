<?php

namespace App\Http\Controllers;

use App\Mail\AuthMail;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;

class AdminAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'abilities:admin,access'])->except(['send_code','verifyCode']);
        $this->middleware(['auth:sanctum','ability:refresh'])->only('refreshToken');
    }

    public function send_code(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'usernameOrEmail'=>['required','string'],
            'password'=>['required','string','min:6','max:50']
        ]);
        if ($validation->fails())
            return $this->error('اسم المستخدم أو كلمة المرور غير صالحة');
        try {
            DB::beginTransaction();
            $admin = Admin::where('user_name', $request->usernameOrEmail)
                            ->orWhere('email', $request->usernameOrEmail)
                            ->first();
            $verify_code = Random::generate(6, '0-9');
            $details=['verify_code'=>$verify_code,'user_name'=>$admin->user_name];
            Mail::to($admin->email)->send(new AuthMail($details));
            $admin->update(['verification_code' => $verify_code]);
            $admin->save();
            DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Server failure '.$e, 500);
        }
    }


    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name'=>['required','string','exists:admins,user_name'],
            'code' => ['required', 'numeric','exists:admins,verification_code']
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            DB::beginTransaction();
            $admin = Admin::where('user_name', $request->user_name)->first();
            if($admin->updated_at < now()->subMinutes(6)){
                $admin->verification_code=null;
                return $this->error('انتهت مهلة إدخال رمز التحقق');
            }
            if (!$admin->markEmailAsVerified()) {
                $admin->markEmailAsVerified();
                $admin->save();
            }
            $admin->verification_code=null;
            $admin->save();
            $admin->token = $admin->createToken('accessToken', ['admin','access'], now()->addDay())->plainTextToken;
            $admin->refresh_token = $admin->createToken('refreshToken', ['admin','refresh'], now()->addDays(6))->plainTextToken;
            DB::commit();
            return $this->success($admin);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Server failure', 500);
        }
    }

}
