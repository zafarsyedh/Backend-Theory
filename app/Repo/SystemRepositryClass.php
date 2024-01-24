<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Branch;
use App\Models\Room;
use App\Models\System;
use App\Models\TopicArea;
use App\Models\TopicAreaDetail;
use App\Models\TopicAreaTranslation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SystemRepositryClass implements Interfaces\SystemInterface
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
    public function getAllSystems()
    {
        try {
            $qry=System::with('room');
            $qry=$qry->get();
            return  Helper::successWithData($qry,'Record found');
        } catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }


    public function createSystem($request)
    {
        try {
            $id = $request->id;
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'room_id' => 'required',
                'system_ip' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

            $role = System::updateOrCreate(
                [
                    'id' => $request->id,
                ],

                [
                    'title' =>$request->title,
                    'room_id' =>$request->room_id,
                    'system_ip' =>$request->system_ip,
                    'status' =>$request->status,
                ]
            );

            DB::commit();
            $data = System::with('room')->find($role->id);
            return  Helper::successWithData($data,(($id)?"System Updated Successfully":"System Added Successfully"));
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return Helper::errorWithData($validationException->errors()->first(), $validationException->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), $e);
        }
    }



    public function deleteSystem($id)
    {

        try {
            $role = System::find($id);
            $role->delete();
            return Helper::successWithData($role, $message="System Deleted");
        }catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }


}
