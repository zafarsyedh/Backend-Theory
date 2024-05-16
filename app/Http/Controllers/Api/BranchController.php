<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\Room;
use App\Repo\Interfaces\BranchInterface;
use App\Repo\Interfaces\LanguageInterface;
use App\Repo\Interfaces\TopicAreaInterface;
use Illuminate\Http\Request;

class BranchController extends Controller
{

    public  $branch;
    public function __construct(BranchInterface $branch)
    {
        $this->branch=$branch;
    }

    public function index(){
        try{
            $response=$this->branch->getAllBranches();
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
    public function branchesList(){
        try{
            $response=$this->branch->getAllBranchForDropdown();
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
    public function saveBranch(Request $request){

        try{
            $response=$this->branch->createBranch($request);
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

    public function deleteBranch( $id){
        try {
            if($room=Room::where('branch_id',$id)->count()==0) {
                $response = $this->branch->deleteBranch($id);
                if ($response['status']) {
                    $response = Helper::success($response['data'], $response['message']);
                } else {
                    $response = Helper::error($response['message'], $response['data']);
                }
                return $response;
            }
            else{
                    return Helper::error($room.'rooms associated with this branch',[]);
                }
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }

}
