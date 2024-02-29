<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('register','login');
    }

    public function register(RegisterRequest $request)
    {
        $data=$request->validated();
        $data['password']=Hash::make($data['password']);
        $data['username']=strstr($data['email'],needle:'@',before_needle: true);
        $user=User::create($data);
        $token = $user->createToken(User::USER_TOKEN);
        return $this->success([
            'user'=>$user,
            'token'=>$token->plainTextToken,
        ],'User has been register successfully');
    }
    public function login(LoginRequest $request){
        $isValid = $this->isValidCredential($request);
        if(!$isValid['success'])
            return $this->error($isValid['message'],Response::HTTP_UNPROCESSABLE_ENTITY);

        $user = $isValid['user'];
        $token = $user->createToken(User::USER_TOKEN);

        return $this->success([
            'user'=>$user,
            'token'=>$token->plainTextToken,
        ],'Login successfully');
    }

    public function isValidCredential(LoginRequest $request)
    {
        $data=$request->validated();

        $user=User::where('email',$data['email'])->first();
        if ($user === null){
            return[
                'success'=>false,
                'message'=>'Invalid Credential',
            ];
        }
        if (Hash::check($data['password'],$user->password)){
            return [
                'success'=>true,
                'user'=>$user,
            ];
        }
        return[
            'success'=>false,
            'message'=>'password is not matched',
        ];
    }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return $this->success(null,'Logout successfully');
    }
}
