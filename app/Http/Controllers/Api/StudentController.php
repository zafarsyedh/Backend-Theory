<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Services\ApiService;
use App\Repo\CourseClass;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\StudentInterface;
use App\Repo\Interfaces\SystemInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public $apiService;
    public $student;
    public $exam;
    public $system;

    public function __construct(ApiService $apiService,StudentInterface $student,ExamInterface $exam,SystemInterface $system)
    {
        $this->apiService = $apiService;
        $this->student = $student;
        $this->exam = $exam;
        $this->system = $system;
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

           $isContinue= $this->exam->checkExamStatus($request->stdData);
           if($isContinue==0){
             return  $response= Helper::error('Exam of this student is already in progress',[]);
           }

            $response=$this->student->saveStudent($request);
            if($response['status']){
                //updateSystemStatus
                $this->system->updateSystemStatus($request->system_id,3);
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

        $course=new CourseClass();
        $courseInfo=$course->getCourseInfoByShortName('LMV');
       return $courseInfo->courseTranslation->where('lang','en')->pluck('instructions')->first();
    }
}
