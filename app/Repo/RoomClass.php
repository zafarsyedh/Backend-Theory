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
            return $response = ([
                "status" => "success",
                "data" => $qry,
                "messege" => "Rooms Lists"
            ]);
        } catch (ValidationException $validationException) {
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        }
    }
    public function getAllRooms()
    {
        try {
            $qry=Room::with('branch');
            $qry=$qry->get();
            return $response = ([
                "status" => "success",
                "data" => $qry,
                "messege" => "Rooms Lists"
            ]);
        } catch (ValidationException $validationException) {
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        }
    }


    public function createRoom($request)
    {
        try {
            $id = $request->id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'branch_id' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails())
                return $response=[
                    "status"=>"false",
                    "messege"=>$validator->errors()
                ];

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
            return $response = ([
                "status" => "success",
                "data" => $data,
                "messege" => (($id)?"Room Updated Successfully":"Room Added Successfully")
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



    public function deleteRoom($id)
    {
        try {
            $role = Room::find($id);
            $role->delete();
            return Helper::success($role, $message="Room Deleted");
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        }
    }


}
