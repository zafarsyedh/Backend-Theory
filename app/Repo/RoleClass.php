<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Category;
use App\Models\PermissionModule;
use App\Models\RoleHasPermission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleClass implements Interfaces\RoleInterface
{

    public function getAllRoles()
    {
        $qry=Role::with('users');
        $qry=$qry->orderBy('id','DESC');
        $qry=$qry->get();
        return $qry;

    }

    //getAllPermissions
    public function getAllPermissions($id)
    {
         $data["permissions"]=PermissionModule::with('permissions')->get();
        $data['roleName'] = Role::select(['id','name'])->find($id);
        $data['rolepermissions'] = $data['roleName']->permissions;
        return $data;

//        $qry=Permission::query();
////        $qry=$qry->with('permAllow');
//        $qry=$qry->get();
//        return $qry;

    }

    public function saveRole($request)
    {

            $role = Role::updateOrCreate(
                [
                    'name' => $request->name
                ],
                [
                    'name'=>$request->name,
                    'guard_name'=>'web',
                    'status'=>$request->status
                ]);
            if($role){
                $data=Role::with('users')->find($role->id);
                return $response=([
                    "status"=>"success",
                    "data"=>$data,
                    "messege"=>"Role Added Successfully"
                ]);
            }else{
                return $response=[
                    "status"=>"false",
                    "messege"=>"Record not save due to some technical error"
                ];

            }

//            $role->syncPermissions($request->permissions);



    }

    public function deleteRole($id)
    {

        // TODO: Implement deleteAddon() method.
         $role =Role::find($id);
        if($role){
            $role->delete();
            return  1;
        }else{
            return "Rec not exist";
        }
    }

    public function editRole($id)
    {

        $data['roleName'] = Role::select(['id','name'])->find($id);
         $data['permissions'] = $data['roleName']->permissions;
        return $data;
    }

    public function updateRole($request)
    {
        try {

            $role = Role::updateOrCreate(
                [
                    'id' => $request->id
                ],
                [
                    'name'=>$request->name,
                    'guard_name'=>'web',
                    'status'=>$request->status
                ]);
            if($role){
                return $response=([
                    "status"=>"success",
                    "data"=>$role,
                    "messege"=>"Role Updated Successfully"
                ]);
            }else{
                return $response=[
                    "status"=>"false",
                    "messege"=>"Record not save due to some technical error"
                ];

            }
        } catch (\Exception $e) {
            return $response=[
                "status"=>"false",
                "messege"=>$e->getMessage()
            ];
        }
    }
    public function saveRolePermissions($request)
    {
        try {
            $role= Role::find($request->roleId);
            $role->syncPermissions($request->permissions);
            if($role){
                return $response=([
                    "status"=>"success",
                    "data"=>$role,
                    "messege"=>"Role Permissions Updated Successfully"
                ]);
            }else{
                return $response=[
                    "status"=>"false",
                    "messege"=>"Record not save due to some technical error"
                ];

            }
        } catch (\Exception $e) {
            return $response=[
                "status"=>"false",
                "messege"=>$e->getMessage()
            ];
        }
    }
}
