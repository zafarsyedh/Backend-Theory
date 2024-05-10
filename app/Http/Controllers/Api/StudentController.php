<?php

namespace App\Http\Controllers\Api;

use App\Events\CourseEvent;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Services\ApiService;
use App\Models\ExamSchedule;
use App\Models\Student;
use App\Models\User;
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
            if($request->exam_template==2){
                $response = $this->apiService->getStudentInfo($trafficId);
            }else{
                $response = $this->apiService->getSharjahStudent($trafficId);
            }
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

            if($request->exam_type==1){
                if(!Student::where('traffic_id',$request->stdData['regnnumb'])->where('is_eligible',1)->latest('id')->first()){
                  return  $response= Helper::error('This is student not eligible for exam',[]);
                }
            }

             $isContinue= $this->exam->checkExamStatus($request->stdData,$request->exam_type);
           if($isContinue ==0){

               $examId=$this->exam->getExamIdOnTheBaseOfTrafficIdNumber($request->stdData['regnnumb']);


               $examSchedule=ExamSchedule::latest('id')->find($examId);
               $examSchedule->created_at=now();
               $examSchedule->exam_status=1;
               $examSchedule->q_lang=$request->q_lang;
               ($request->audio_lang)?$examSchedule->audio_lang=$request->audio_lang:'';

               $examSchedule->save();

               return  $res=$this->restartExam($examId);
//             return  $response= Helper::error('Exam of this student is already in progress',[]);
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

    //restartExam
    public function restartExam($id){
        try {

            $isContinue= $this->exam->checkExamStartOrNot($id);
            if($isContinue!==1){
                return  $response= Helper::error('student has been started exams',[]);
            }
            $examSchedule=ExamSchedule::find($id);
            $qry=ExamSchedule::with('student:id,std_name,traffic_id','course.courseConfig','qLanguage:id,lang,lang_short,direction');
            ($examSchedule)?$qry=$qry->with('audioLanguage'):'';
            $exam=$qry->with('system')->find($id);

            if($exam->exam_type==1){
                $examDuration=$exam->course->courseConfig->total_duration;;
            }else{
                $examDuration=$exam->course->courseConfig->practice_duration;
            }

            $this->system->updateSystemStatus($exam->system_id,3);
            $userInfo= User::with('branch')->find($exam->invg_id);

            $eventStdData = [

                'examId' =>$id,
                'stdId' =>$exam->std_id,
                'stdName' =>$exam->student->std_name,
                'trafficId' =>$exam->student->traffic_id,
                'courseId' =>$exam->course->id,
                'examDuration' =>$examDuration,

                'courseName' =>$exam->course->short_name,
                'qLangShortName' =>$exam->qLanguage->lang_short,
                'qLangFullName' =>$exam->qLanguage->lang,
                'audioLangShortName' =>($exam AND $exam->audioLanguage)?$exam->audioLanguage->lang_short:'-',
                'audioLangFullName' =>($exam AND $exam->audioLanguage)?$exam->audioLanguage->lang:'-',
                'examType' =>$exam->exam_type,
                'direction' =>($exam->qLanguage->direction == 2)? 'ltr':'rtl',
                'systemIp' =>$exam->system->system_ip,
                'instructions' =>count($exam->course->courseTranslation->where('lang',$exam->qLanguage->lang_short))?$exam->course->courseTranslation->where('lang',$exam->qLanguage->lang_short)->pluck('instructions')->first():$exam->course->courseTranslation->where('lang','en')->pluck('instructions')->first(),
                'videoLink' =>count($exam->course->courseTranslation->where('lang',$exam->qLanguage->lang_short))?$exam->course->courseTranslation->where('lang',$exam->qLanguage->lang_short)->pluck('video_link')->first():$exam->course->courseTranslation->where('lang','en')->pluck('video_link')->first(),
                'examTemplate' =>$userInfo?$userInfo->branch->exam_template:1,

            ];
            event(new CourseEvent($eventStdData));
            return   $response= Helper::success([],'Exam restart successfully');

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
