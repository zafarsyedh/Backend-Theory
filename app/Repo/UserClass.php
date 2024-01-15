<?php

namespace App\Repo;
use App\Models\Role;
use App\Models\User;
use App\Traits\HandleFiles;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserClass implements Interfaces\UserInterface
{
use HandleFiles;

protected $path='user-images/';


    public function getAllUser()
    {
        $qry=User::with('role');
        $qry=$qry->get();
        return $qry;
    }
    public function getInvigilator()
    {
        $qry=User::query();
        $qry=$qry->where('is_take_test',1);
        $qry=$qry->where('status',1);
        $qry=$qry->where('is_deleted',0)
            ->orderBy('id','DESC');
        $qry=$qry->get();
        return $qry;
    }

    public function createUser($request)
    {

            if (User::where('email', $request->email)->first()) {
                return $response=[
                    "status"=>"false",
                    "messege"=>"This record already exist"
                ];
            }
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->role_id = $request->role_id;
            $user->status = $request->status;

        if($user->save()){
            $user->roles()->attach($user->role_id);
            $data = User::with("role")->find($user->id);
            return $response=([
                "status"=>"success",
                "data"=>$user,
                "messege"=>"User Added Successfully"
            ]);
        }else{
            return $response=[
                "status"=>"false",
                "messege"=>"Record not save due to some technical error"
            ];

        }


    }

    public function deleteUser($id)
    {
        // TODO: Implement deleteAddon() method.
        $addon =User::find($id);
        $addon->delete();
        return 1;
    }

    public function editUser($id)
    {
        // TODO: Implement editAddon() method.
        return $category = User::find($id);
    }

    public function updateUser($request)
    {

        $user =  User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->role_id = $request->role_id;
        $user->status = $request->status;
        if($user->save()){
            $user->roles()->sync($user->role_id);
//            $user->roles()->attach($user->role_id);
            $data = User::with("role")->find($user->id);
            return $response=([
                "status"=>"success",
                "data"=>$data,
                "messege"=>"User Updated Successfully"
            ]);
        }else{
            return $response=[
                "status"=>"false",
                "messege"=>"Record not save due to some technical error"
            ];

        }
    }
}
