<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try{
            $data = $request->all();
            $user = User::where('email', $data['email'])->first();
            if (!$user || !Hash::check($data['password'], $user->password)) {
                $response= Helper::error('errors','Email or password is incorrect!');
                return $response;
            }else{
                $token = $user->createToken('auth_token')->plainTextToken;
                $cookie = cookie('token', $token, 60 * 24); // 1 day
                $response= Helper::success(new UserResource($user),'Login Successfully')->withCookie($cookie);
                return $response;
            }
        } catch (\Exception $e) {
            return Helper::error($e,$e->getMessage());
        }
    }
    public function apiVerifyToken(Request $request)
    {
         $request->all();
        $request->validate([
            'api_token' => 'required'
        ]);
             $user = User::where('api_token',$request->api_token)->first();
        if(!$user){
            $user = Helper::createAPIResponce($is_error = false, $code = 200, $token = 'Invalid token', $user);
        }
        return response($user);
    }
}
