<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Traits\HandleFiles;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserClass implements Interfaces\UserInterface
{
use HandleFiles;

protected $path='user-images/';


    public function getAllUser()
    {
        try {
            $qry=User::with('role','branch');
            $qry=$qry->with('room');
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }

    }
    public function createUser($request)
    {

        try {

            $id = $request->id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users')->whereNull('deleted_at') .$id,
                ],
                'phone' => 'required',
                'password' =>'nullable|string|min:8',
                'role_id' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

            $user = User::updateOrCreate(
                [
                    'id' => $request->id,
                ],
                [
                    'name' =>$request->name,
                    'email' =>$request->email,
                    'password' =>Hash::make($request->password),
                    'phone' =>$request->phone,
                    'role_id' =>$request->role_id,
                    'room_id' =>$request->room_id,
                    'branch_id' =>$request->branch_id,
                    'status' =>$request->status,
                ]
            );
            if($id)
            {
                $user->roles()->sync($user->role_id);
            }else
            {
                $user->roles()->attach($user->role_id);
            }

            DB::commit();
            $data = User::with("role",'branch','room')->find($user->id);
            return  Helper::successWithData($data,(($id)?"User Updated Successfully":"User Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }



    }
    public function deleteUser($id)
    {
        try {
            $role = User::find($id);
            $role->delete();
            return Helper::successWithData($role, $message="User Deleted");
        }catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }

}
