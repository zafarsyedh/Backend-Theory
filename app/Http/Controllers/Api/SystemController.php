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
            $response =  $this->system->deleteSystem($id);
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
}
