<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Services\ApiService;
use App\Repo\Interfaces\StudentInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public $apiService;
    public $student;

    public function __construct(ApiService $apiService,StudentInterface $student)
    {
        $this->apiService = $apiService;
        $this->student = $student;
    }

    public function getBdcStd(Request $request)
    {

        try {
            $trafficId =$request->traffic_id;
             $response = $this->apiService->getStudentInfo($trafficId);
            if ($response->successful()) {
                $responseData = $response->json();
                $response= Helper::success($responseData['data'],$response['message']);
            }else{
                $response= Helper::error('Invalid traffic ID',['error']);
            }
            return $response;

             } catch (\Exception $e) {
                return Helper::errorWithData($e->getMessage(), []);
                }
}

    //saveScheduleExam
    public function saveScheduleExam(Request $request){

        try{
            $response=$this->student->saveStudent($request);
            if($response['status']){
                $response= Helper::success($response['data'],'Exam save successfully');
            }else{
                $response= Helper::error($response['message'],[]);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }



    public function testData(Request $request){

        return  $response= Helper::success($request->traffic_id,'return request');

    }



}
