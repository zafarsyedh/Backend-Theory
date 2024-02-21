<?php

namespace App\Http\Controllers\Api;

use App\Events\CourseEvent;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Question;
use App\Models\QuestionSolved;
use App\Repo\Interfaces\CourseInterface;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\QuestionInterface;
use App\Repo\Interfaces\SystemInterface;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ExamController extends Controller
{
    public  $exam;
    public  $questions;
    public  $course;
    public  $system;
    public function __construct(ExamInterface $exam,QuestionInterface $questions,CourseInterface $course,SystemInterface $system)
    {
        $this->exam=$exam;
        $this->questions=$questions;
        $this->course=$course;
        $this->system=$system;
    }
    public function getQuestionsForExam(Request $request){

        try{
                 $request->all();
            $response=$this->questions->createAttemptAndSolveQuestion($request);
            if($response['status']){
            $res=$this->questions->getMovedQuestionForTheoryPractice($request,$response['data']);
            $this->exam->updateExamScheduleStatus($request->exam_id,2);

            return Helper::success($res,'Questions list');
            }else{
                return Helper::errorWithData($response['message'],[]);
            }
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
    //saveQuestionsForExam
    public function saveQuestionsForExam(Request $request){

        try{
            $response=$this->exam->saveExamQuestion($request);
            if($response['status']){
                return Helper::success($response,'Questions saved');
            }else{
                return Helper::errorWithData($response,'Questions not saved');
            }

        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
    //savePracticeQuestions
    public function savePracticeQuestions(Request $request){

        try{

            $response=$this->exam->savePracticeQuestion($request);
            if($response['status']){

               if(QuestionSolved::where('attempt_id',$response['data'])->where('is_answered',0)->count() == 0){
                   $this->exam->updateAttemptStatus($response['data']);
               }
                return Helper::success($response,'Questions saved');
            }else{
                return Helper::errorWithData($response,'Questions not saved');
            }

        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
    //getResults
    public function getResults(Request $request){

        try{
            $request->all();



            $response=$this->questions->getMovedQuestionForTheoryPractice($request,$request->attempt_id,2);
            if($response->count() > 0){

                $resData = collect([]);
                $correctOpt='';
                $choosedOpt='';
                foreach ($response as $row){

                    if($row->question->correct_opt=='a'){
                        $correctOpt=$row->question->questionTranslations[0]->opt_a;
                    }
                    if($row->question->correct_opt=='b'){
                        $correctOpt=$row->question->questionTranslations[0]->opt_b;
                    }
                    if($row->question->correct_opt=='c'){
                        $correctOpt=$row->question->questionTranslations[0]->opt_c;
                    }

                    if($row->choosed_option=='a'){
                        $choosedOpt=$row->question->questionTranslations[0]->opt_a;
                    }

                    if($row->choosed_option=='b'){
                        $choosedOpt=$row->question->questionTranslations[0]->opt_b;
                    }
                    if($row->choosed_option=='c'){
                        $choosedOpt=$row->question->questionTranslations[0]->opt_c;
                    }

                    if($row->choosed_option){
                        if($row->choosed_option==$row->question->correct_opt){
                        $qStatus='Correct';
                        } else{
                            $qStatus='Wrong';
                        }
                    }else{
                        $qStatus='-';
                    }

                    $array = array(
                        'id' =>  $row->id,
                        'question' =>  $row->question->questionTranslations[0]->q_title,
                        'choosed_ans' => $choosedOpt,
                        'correct_ans' => $correctOpt,
                        'q_status' => $qStatus,

                    );
                    $resData->push($array);
                }
            }
            return Helper::success($resData,'Result list');

        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }


    public function getAllResults(){

        try{
             $response=$this->exam->getAllResultsList();
            if($response['status']){
                return Helper::success($response['data'],'Results list');
            }else{
                return Helper::errorWithData('Record not exist',[]);
            }
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }


    //getScheduleExamList
    public function getScheduleExamList(Request $request){

        try{
            $response=$this->exam->getScheduleExamList($request);
            if($response['status']){
                return Helper::success($response['data'],'Questions list');
            }else{
                return Helper::errorWithData('Record not exist',[]);
            }
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
    //updateScheduleExam
    public function updateScheduleExam(Request $request){

        try{

            $response=$this->exam->updateExam($request);
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
    public function deleteExam($id){
        try {
            $response = $this->exam->deleteExam($id);
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
    //restartExam
    public function restartExam($id){
        try {

           $exam=ExamSchedule::with('student:id,std_name,traffic_id','course.courseConfig','qLanguage:id,lang,lang_short,direction','audioLanguage','system')->find($id);

           if($exam->exam_type==1){

               $examDuration=$exam->course->courseConfig->total_duration;;
           }else{
               $examDuration=$exam->course->courseConfig->practice_duration;
            }

             $this->system->updateSystemStatus($exam->system_id,3);

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
                'audioLangShortName' =>$exam->audioLanguage->lang_short,
                'audioLangFullName' =>$exam->audioLanguage->lang,
                'examType' =>$exam->exam_type,
                'direction' =>($exam->qLanguage->direction == 2)? 'ltr':'rtl',
                'systemIp' =>$exam->system->system_ip,
                'instructions' =>count($exam->course->courseTranslation->where('lang',$exam->qLanguage->lang_short))?$exam->course->courseTranslation->where('lang',$exam->qLanguage->lang_short)->pluck('instructions')->first():$exam->course->courseTranslation->where('lang','en')->pluck('instructions')->first(),
                'videoLink' =>count($exam->course->courseTranslation->where('lang',$exam->qLanguage->lang_short))?$exam->course->courseTranslation->where('lang',$exam->qLanguage->lang_short)->pluck('video_link')->first():$exam->course->courseTranslation->where('lang','en')->pluck('video_link')->first(),



            ];
            event(new CourseEvent($eventStdData));
            return   $response= Helper::success([],'Exam restart successfully');

        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }


    //getStudentResult
    public function getStudentResult(Request $request){

        try{


            $exam_id=$request->exam_id;
              $res=$this->exam->getExamWiseResult($exam_id);
            if($res->count() > 0){



                $questions= $this->exam->getSolvedQuestionAccordingAttempt($res->attempt->id);

                $resData = collect([]);

                $totalCorrectAns=0;
                $totalWrongAns=0;
                foreach ($questions as $row){

                    $totalCorrectAns= $totalCorrectAns + $row->solvedQuestion->where('is_correct_ans',1)->count();
                    $totalWrongAns=$totalWrongAns +  $row->solvedQuestion->where('is_correct_ans',0)->count();

                    $topicArray = array(
                        'topic'=> $row->topicAreaTranslation[0]->full_name,
                        'wrong_ans' =>$row->solvedQuestion->where('is_correct_ans',0)->count(),
                    );
                    $resData->push($topicArray);
                }

                $array = array(
                    'test_date' =>date('d M Y',strtotime($res->created_at)),
                    'std_name' =>$res->attempt->student->std_name,
                    'traffic_id' =>$res->attempt->student->traffic_id,
                    'course' =>$res->attempt->student->activeCourse->course->short_name,
                    'test_time' =>date('H:i:s',strtotime($res->created_at)),
                    'total_duration' => $res->total_duration,
                    'test_duration' => $res->test_duration,
                    'status' => $res->status,
                    'total_question' =>$res->total_question,
                    'required_ans' => $res->correct_ans_required,
                    'correct_ans' => $res->correct_ans,
                    'topics'=>$resData
                );
            }
            return Helper::success($array,'Result list');

        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
    //checkPracticeType
    public function checkPracticeType(Request $request){
        try {

            $response=$this->exam->checkPracticeType($request);
            return Helper::success($response,'Practice information found');

        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }

    //exitExam
    public function exitExam($id){
        try {

           $examUpdate= $this->exam->updateExamScheduleStatus($id,4);
            $this->system->updateSystemStatus($examUpdate->system_id,1);
            return   $response= Helper::success([],'Exam exit successfully');

        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }
}
