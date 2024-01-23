<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Branch;
use App\Models\TopicArea;
use App\Models\TopicAreaDetail;
use App\Models\TopicAreaTranslation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BranchClass implements Interfaces\BranchInterface
{

    public function getAllBranchForDropdown()
    {
        try {
            $qry=Branch::query();
            $qry=$qry->where('status',1);
            $qry=$qry->get();
       return  Helper::successWithData($qry,'Record found');
        } catch (ValidationException $validationException) {
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        }
    }
    public function getAllBranches()
    {
        try {
            $qry=Branch::query();
            $qry=$qry->get();
            return $response = ([
                "status" => "success",
                "data" => $qry,
                "messege" => "Branch Lists"
            ]);
        } catch (ValidationException $validationException) {
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        }
    }


    public function createBranch($request)
    {
        try {
            $id = $request->id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails())
                return $response=[
                    "status"=>"false",
                    "messege"=>$validator->errors()
                ];

            $role = Branch::updateOrCreate(
                [
                    'id' => $request->id,
                ],

                [
                    'title' =>$request->title,
                    'status' =>$request->status,

                ]
            );

            DB::commit();
            $data = Branch::find($role->id);
            return $response = ([
                "status" => "success",
                "data" => $data,
                "messege" => (($id)?"Branch Updated Successfully":"Branch Added Successfully")
            ]);
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return $response=[
                "status"=>"false",
                "messege"=> $validationException->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return $response=[
                "status"=>"false",
                "messege"=> $e->getMessage()
            ];
        }
    }



    public function deleteBranch($id)
    {
        try {
            $role = Branch::find($id);
            $role->delete();
            return Helper::success($role, $message="Branch Deleted");
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        }
    }


}
