<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Branch;
use App\Models\Category;
use App\Models\PermissionModule;
use App\Models\RoleHasPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleClass implements Interfaces\RoleInterface
{

    public function getAllRoles()
    {
        try {
            $qry=Role::with('users');
            $qry=$qry->orderBy('id','DESC');
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }

    }

    public function getAllPermissions($id)
    {
        try {
            $data["permissions"]=PermissionModule::with('permissions')->get();
            $data['roleName'] = Role::select(['id','name'])->find($id);
            $data['rolepermissions'] = $data['roleName']->permissions;
            return  Helper::successWithData($data,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }

    }

    public function saveRole($request)
    {
        try {
            $id = $request->id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:roles,name,' . $id,
                'status' => 'required',
            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

            $role = Role::updateOrCreate(
                [
                    'id' => $request->id
                ],
                [
                    'name'=>$request->name,
                    'guard_name'=>'web',
                    'status'=>$request->status
                ]);
            DB::commit();
            $data=Role::with('users')->find($role->id);
            return  Helper::successWithData($data,(($id)?"Role Updated Successfully":"Role Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }

    }

    public function deleteRole($id)
    {
        try {
            $role = Role::find($id);
            $role->delete();
            return Helper::successWithData($role, $message="Role Deleted");
        }catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }




    public function saveRolePermissions($request)
    {
        try {
            DB::beginTransaction();
            $role= Role::find($request->roleId);
            $role->syncPermissions($request->permissions);
            DB::commit();
            return  Helper::successWithData($role,'Role Permissions Updated Successfully');
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }
    }
}
