<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Branch;
use App\Models\Category;
use App\Models\PermissionModule;
use App\Models\RoleHasPermission;
use App\Models\UserRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleClass implements Interfaces\RoleInterface
{

    public function getAllRoles()
    {
        try {
            $qry=UserRoles::with('user');
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
            $data['roleName'] = UserRoles::select(['id','name'])->find($id);
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
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('roles')->whereNull('deleted_at') . $id,
                ],
                'status' => 'required',
            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

            $role = UserRoles::updateOrCreate(
                [
                    'id' => $request->id
                ],
                [
                    'name'=>$request->name,
                    'guard_name'=>'web',
                    'status'=>$request->status
                ]);
            DB::commit();
            $data=UserRoles::with('user')->find($role->id);
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
            $role = UserRoles::find($id);
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
            $role= UserRoles::find($request->role_id);
            $permissions = array_map('intval', $request->permissions);
            $role->syncPermissions($permissions);
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
