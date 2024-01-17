<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Repo\Interfaces\BranchInterface;
use App\Repo\Interfaces\RoomInterface;
use Illuminate\Http\Request;

class RoomController extends Controller
{

    public  $branch;
    public  $room;
    public function __construct(BranchInterface $branch,RoomInterface $room)
    {
        $this->branch=$branch;
        $this->room=$room;
    }

    public function index(){

        try{
            $response['branches']=$this->branch->getAllBranchForDropdown();
            $response['rooms']=$this->room->getAllRooms();
            if($response){
                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);
            }else{
                $response =Helper::createAPIResponce($is_error = true, $code = 206, $message = 'content not available', $response);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
    public function roomsList(){

        try{
            $response['rooms']=$this->room->getAllRoomForDropdown();
            if($response){
                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response['branches']['data']);
            }else{
                $response =Helper::createAPIResponce($is_error = true, $code = 206, $message = 'content not available', $response);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
    public function saveRoom(Request $request){

        try{
            $res=$this->room->createRoom($request);
            if( $res['status'] == 'success'){
                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
            }else{
                $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }


    public function deleteRoom( $id){
        try {
            $res = $this->room->deleteRoom($id);
            return Helper::ajaxSuccess($res->get('data'),$res->get('message'));
        } catch (\Exception $e) {
            return Helper::ajaxError($e->getMessage());
        }
    }

}
