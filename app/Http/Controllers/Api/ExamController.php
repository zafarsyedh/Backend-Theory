<?php

namespace App\Http\Controllers\Api;

use App\Events\CourseEvent;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Repo\Interfaces\CourseInterface;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\QuestionInterface;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ExamController extends Controller
{
    public  $exam;
    public  $questions;
    public  $course;
    public function __construct(ExamInterface $exam,QuestionInterface $questions,CourseInterface $course)
    {
        $this->exam=$exam;
        $this->questions=$questions;
        $this->course=$course;
    }

    public function getQuestionsForExam(Request $request){

        try{
            $response=$this->questions->questionMoveInSolvedQuestionTable($request);
            if($response['status']){
            $res=$this->questions->getMovedQuestionForTheoryPractice($request,$response['data']);
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

           $exam=ExamSchedule::with('student:id,std_name,traffic_id','course:id,short_name','qLanguage:id,lang,lang_short,direction','audioLanguage','system')->find($id);

            $eventStdData = [

                'examId' =>$id,
                'stdId' =>$exam->std_id,
                'stdName' =>$exam->student->std_name,
                'trafficId' =>$exam->student->traffic_id,
                'courseId' =>$exam->course->id,
                'courseName' =>$exam->course->short_name,
                'qLangShortName' =>$exam->qLanguage->lang_short,
                'qLangFullName' =>$exam->qLanguage->lang,
                'audioLangShortName' =>$exam->audioLanguage->lang_short,
                'audioLangFullName' =>$exam->audioLanguage->lang,
                'examType' =>$exam->exam_type,
                'direction' =>($exam->qLanguage->direction == 2)? 'ltr':'rtl',
                'systemIp' =>$exam->system->system_ip,

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
            $attemptId=1;//$request->attempt_id;
             $res=$this->exam->getAttemptInfo($attemptId);
            if($res->count() > 0){

                 $courseInfo=Helper::fetchOnlyData($this->course->getCourseConfig($res->student->activeCourse->course_id));

                $totalRequireQuestion=0;
                $courseConfiguration=$courseInfo->courseConfig;


                if($courseConfiguration[0]->require_type==1){
                    $totalRequireQuestion=$courseConfiguration[0]->specific_require +  $courseConfiguration[0]->common_require + $courseConfiguration[0]->video_require;
                }else{
                    $totalRequireQuestion=$courseConfiguration[0]->total_require;
                }

                $questions= $this->exam->getSolvedQuestionAccordingAttempt($attemptId);


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
                    'test_date' =>$res->created_at,
                    'std_name' =>$res->student->std_name,
                    'traffic_id' =>$res->student->traffic_id,
                    'course' =>$res->student->activeCourse->course->short_name,
                    'test_time' => $res->created_at,
                    'total_duration' => $courseConfiguration[0]->total_duration,
                    'test_duration' => 10,
                    'status' => ($totalCorrectAns >= $totalRequireQuestion)?1:0,
                    'total_question' =>$courseConfiguration[0]->specific_question +  $courseConfiguration[0]->common_question + $courseConfiguration[0]->video_question,
                    'required_ans' => $totalRequireQuestion,
                    'correct_ans' => $totalCorrectAns,
                    'topics'=>$resData


                );

                return $array;

                $resData = collect([]);
                $correctOpt='';
                $choosedOpt='';
                foreach ($response as $row){



                    $array = array(
                        'test_date' =>  $row->id,
                        'traffic_id' =>  $row->question->questionTranslations[0]->q_title,
                        'course' => $choosedOpt,
                        'test_time' => $correctOpt,
                        'total_duration' => $qStatus,
                        'test_duration' => $qStatus,
                        'status' => $qStatus,
                        'total_question' => $qStatus,
                        'required_ans' => $qStatus,
                        'correct_ans' => $qStatus,

                    );
                    $resData->push($array);
                }
            }
            return Helper::success($resData,'Result list');

        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
}
