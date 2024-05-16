<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\ExamSchedule;
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
            if($response['systems']['status']){
                $response['rooms']=$this->room->getAllRoomForDropdown();
                $response= Helper::success($response,$response['systems']['message']);
            }else{
                $response= Helper::error($response['systems']['message'],$response['systems']['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }
    public function saveSystem(Request $request){
        try{
            $response=$this->system->createSystem($request);
            if($response['status']){
                $response= Helper::success($response['data'],$response['message']);
            }else{
                $response= Helper::error($response['message'],$response['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }

    public function deleteSystem( $id){
        try {
            if($exam=ExamSchedule::where('system_id',$id)->count()==0) {
                $response = $this->system->deleteSystem($id);
                if ($response['status']) {
                    $response = Helper::success($response['data'], $response['message']);
                } else {
                    $response = Helper::error($response['message'], $response['data']);
                }
                return $response;
            }
            else{
                return Helper::error($exam.'exams associated with this room',[]);
            }
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }

    //getRoomWiseSystems
    public function getRoomWiseSystems($roomId){
        try {
            $response =$this->system->getRoomWiseSystems($roomId);
            if($response['status']){
                $response= Helper::success($response['data'],$response['message']);
            }else{
                $response= Helper::error($response['message'],$response['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }

    //checkSystemIp
    public function checkSystemIp($systemIp){
        try {

            $response =$this->system->checkSystemIp($systemIp);
            if($response){
                $response= Helper::success($response,'Ip found');
            }else{
                $response= Helper::error('Ip not found',[]);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }
}
