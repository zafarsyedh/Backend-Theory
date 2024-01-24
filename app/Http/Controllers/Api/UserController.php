<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\LanguageRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repo\Interfaces\RoleInterface;
use App\Repo\Interfaces\UserInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public  $user;
    public $role;
    public function __construct(UserInterface $user, RoleInterface $role)
    {
        $this->user=$user;
        $this->role=$role;
    }

    public function index(){
        try{
            $response['users']=$this->user->getAllUser();
            if($response['users']['status']){
                $response['roles']=$this->role->getAllRoles();
                $response= Helper::success($response,$response['users']['message']);
            }else{
                $response= Helper::error($response['users']['message'],$response['users']['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }

    }

    public function saveUser(Request $request)
    {
        try{
            $response=$this->user->createUser($request);
            if($response['status']){
                $response= Helper::success($response['data'],$response['message']);
            }else{
                $response= Helper::error($response['message'],$response['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }

    }
    public function deleteUser($id){
        try {
            $response = $this->user->deleteUser($id);
            if($response['status']){
                $response= Helper::success($response['data'],$response['message']);
            }else{
                $response= Helper::error($response['message'],$response['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }
}
