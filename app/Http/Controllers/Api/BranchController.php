<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
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
            $response['branches']=$this->branch->getAllBranches();
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
    public function branchesList(){

        try{
            $response['branches']=$this->branch->getAllBranchForDropdown();
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
    public function saveBranch(Request $request){

        try{
            $res=$this->branch->createBranch($request);
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


    public function deleteBranch( $id){
        try {
            $res = $this->branch->deleteBranch($id);
            return Helper::ajaxSuccess($res->get('data'),$res->get('message'));
        } catch (\Exception $e) {
            return Helper::ajaxError($e->getMessage());
        }
    }


}
