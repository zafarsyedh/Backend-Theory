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
            $response['rooms']=$this->room->getAllRooms();
            if($response['rooms']['status']){
                $response['branches']=$this->branch->getAllBranchForDropdown();
                $response= Helper::success($response,$response['rooms']['message']);
            }else{
                $response= Helper::error($response['rooms']['message'],$response['rooms']['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }
    public function roomsList(){

        try{
            $response['rooms']=$this->room->getAllRoomForDropdown();
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
    public function saveRoom(Request $request){

        try{
            $response=$this->room->createRoom($request);
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


    public function deleteRoom( $id){
        try {
            $response = $this->room->deleteRoom($id);
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

    //getBranchRooms
    public function getBranchRooms($branchId){

        try{

        $response=$this->room->getBranchWiseRooms($branchId);
            if($response->count() > 0){
                $response= Helper::success($response,'Branch wise rooms list');
            }else{
                $response= Helper::error('Branch wise rooms not exist',[]);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }

    }



}
