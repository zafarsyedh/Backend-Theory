<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\RoleRequest;
use App\Repo\Interfaces\RoleInterface;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public  $role;
    public function __construct(RoleInterface $role)
    {
        $this->role=$role;
    }

    public function index(){
         $response=$this->role->getAllRoles();
        if($response->count() > 0){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message = 'data not found', $response);
        }
        return response()->json($response);
    }
    public function saveRole(Request $request){
          $res=$this->role->saveRole($request);
        if( $res['status'] == 'success'){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
        }
        return response()->json($response);
    }
    public function editRole(Request $request){
        $res=$this->role->editRole($request->id);
        if($res){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'Record found', $res);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message = 'data not found', $res);

        }
        return response()->json($response);
    }
    public function updateRole(Request $request){

        try {
            $res=$this->role->updateRole($request);
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
    public function saveRolePermissions(Request $request){

        try {
            $res=$this->role->saveRolePermissions($request);
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

    public function deleteRole( $request){

        $res=$this->role->deleteRole($request);
        if($res){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'Record found', $res);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message = 'data not found', $res);

        }
        return response()->json($response);
    }

    //getAllPermissions
    public function getAllPermissions($id){
        $response=$this->role->getAllPermissions($id);
        if($response){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message = 'data not found', $response);
        }
        return response()->json($response);
    }




}
