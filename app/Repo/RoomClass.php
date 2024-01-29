<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Branch;
use App\Models\Room;
use App\Models\TopicArea;
use App\Models\TopicAreaDetail;
use App\Models\TopicAreaTranslation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RoomClass implements Interfaces\RoomInterface
{

    public function getAllRoomForDropdown()
    {
        try {
            $qry=Room::query();
            $qry=$qry->where('status',1);
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function getAllRooms()
    {
        try {
            $qry=Room::with('branch');
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }


    public function createRoom($request)
    {
        try {
            $id = $request->id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'title' => 'required|unique:rooms,title,' . $id,
                'branch_id' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

            $role = Room::updateOrCreate(
                [
                    'id' => $request->id,
                ],

                [
                    'title' =>$request->title,
                    'branch_id' =>$request->branch_id,
                    'status' =>$request->status,
                ]
            );

            DB::commit();
            $data = Room::with('branch')->find($role->id);
            return  Helper::successWithData($data,(($id)?"Room Updated Successfully":"Room Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }
    }



    public function deleteRoom($id)
    {
        try {
            $role = Room::find($id);
            $role->delete();
            return Helper::successWithData($role, $message="Room Deleted");
        }catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }


}
