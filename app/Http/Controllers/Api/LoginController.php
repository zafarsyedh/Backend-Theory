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
                return response()->json([
                    'status'=>'errors',
                    'data' => 'Email or password is incorrect!'
                ], 200);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $cookie = cookie('token', $token, 60 * 24); // 1 day

            return response()->json([
                'status'=>'success',
                'data' => new UserResource($user),
                'token'=> $token
            ])->withCookie($cookie);

        } catch (\Exception $e) {
            return response()->json([
                'status'=>'errors',
                'data' => $e->getMessage()
            ], 422);
        }

//        try {
//            if(!Auth::attempt($request->only('email','password'))){
//                Helper::sendError('Credentials error!');
//            }
//            return new UserResource(auth()->user());
//        } catch (\Exception $e) {
//            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
//        }
    }


    public function apiVerifyToken(Request $request)
    {
         $request->all();


        $request->validate([
            'api_token' => 'required'
        ]);

   //        $user = User::with('stdInfo')->where('api_token', $request->api_token)->first();
             $user = User::where('api_token',$request->api_token)->first();

        if(!$user){

            $user = Helper::createAPIResponce($is_error = false, $code = 200, $token = 'Invalid token', $user);

        }
        return response($user);
    }
}
