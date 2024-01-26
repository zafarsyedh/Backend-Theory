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
use mysql_xdevapi\Exception;

class BranchClass implements Interfaces\BranchInterface
{
    public function getAllBranchForDropdown()
    {
        try {
            $qry=Branch::query();
            $qry=$qry->where('status',1);
            $qry=$qry->get();
             return  Helper::successWithData($qry,'Record found');
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function getAllBranches()
    {
        try {
            $qry=Branch::query();
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
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
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

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
            return  Helper::successWithData($data,(($id)?"Branch Updated Successfully":"Branch Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }
    }
    public function deleteBranch($id)
    {
        try {
            $role = Branch::find($id);
            $role->delete();
            return Helper::successWithData($role, $message="Branch Deleted");
        }catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }

}
