<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Repo\Interfaces\BranchInterface;
use App\Repo\Interfaces\RoomInterface;
use App\Repo\Interfaces\SystemInterface;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public  $system;
    public  $room;
    public function __construct(SystemInterface $system,RoomInterface $room)
    {
        $this->system=$system;
        $this->room=$room;
    }
    public function systemList(){

        try{
            $response['systems']=$this->system->getAllSystems();
            $response['rooms']=$this->room->getAllRoomForDropdown();
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
    public function saveSystem(Request $request){

        try{
            $res=$this->system->createSystem($request);
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
}
