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
        $data['users']=$this->user->getAllUser();
        $data['roles']=$this->role->getAllRoles();
        if($data){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $data);

        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 206, $message = 'content not available', $data);

        }
        return response()->json($response);
    }


    public function saveUser(Request $request)
    {

        try {
            $res=$this->user->createUser($request);
            if( $res['status'] == 'success'){
                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
            }else{
                $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
            }
    public function deleteUser($id){

        $res=$this->user->deleteUser($id);
        if($res==1){
            return response()->json(['success' => 'Record deleted successfully']);
            $response=$this->createAPIResponce($is_error=false,$code=200,$message='Record save',$res);
            return response()->json($response, $status = 200);
        }else{
            return response()->json(['error' =>$res]);
            $response=$this->createAPIResponce($is_error=true,$code=401,$message=$res,$res);

            return response()->json($response, $status = 401);
        }
    }


    public function editUser(Request $request){
        $id=$request->user_id;
        $res=$this->user->editUser($id);
        if($res){

            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'Record found', $res);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message = 'data not found', $res);

        }
        return response()->json($response);
    }
    public function updateUser(Request $request){

           $res=$this->user->updateUser($request);
        if( $res['status'] == 'success')
        {
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
        }
        return response()->json($response);
    }

    //getInvigilator

    public function getInvigilator()
    {
        $response=$this->user->getInvigilator();
        if($response->count() > 0)
        {
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message = 'data not found', $response);
        }
        return response()->json($response);
    }

}
