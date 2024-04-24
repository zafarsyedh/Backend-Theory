<?php

namespace App\Http\Controllers\Api;

use App\Events\CourseEvent;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\ExamRequest;
use App\Models\Configuration;
use App\Models\ExamSchedule;
use App\Models\QuestionSolved;
use App\Models\Result;
use App\Models\Student;
use App\Models\System;
use App\Models\User;
use App\Notifications\SendMailandSmsNotification;
use App\Repo\Interfaces\CourseInterface;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\QuestionInterface;
use App\Repo\Interfaces\SystemInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

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
                $exam=ExamSchedule::find($request->exam_id);
                $this->system->updateSystemStatus($exam->system_id ,1);
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
                $choosedImage=null;
                foreach ($response as $row){

                    if($row->question->correct_opt=='a'){
                        $correctOpt=$row->question->questionTranslations[0]->opt_a;
                        ($row->question->opt_a_image!=1)?$correctImage=$row->question->opt_a_image:$correctImage=null;
                    }
                    if($row->question->correct_opt=='b'){
                        $correctOpt=$row->question->questionTranslations[0]->opt_b;
                        ($row->question->opt_b_image!=1)?$correctImage=$row->question->opt_b_image:$correctImage=null;
                    }
                    if($row->question->correct_opt=='c'){
                        $correctOpt=$row->question->questionTranslations[0]->opt_c;
                        ($row->question->opt_c_image!=1)?$correctImage=$row->question->opt_c_image:$correctImage=null;
                    }


                    //Choosed Options
                    if($row->choosed_option=='a'){
                        $choosedOpt=$row->question->questionTranslations[0]->opt_a;
                        ($row->question->opt_a_image!=1)?$choosedImage=$row->question->opt_a_image:$choosedImage=null;
                    }

                    if($row->choosed_option=='b'){
                        $choosedOpt=$row->question->questionTranslations[0]->opt_b;
                        ($row->question->opt_b_image!=1)?$choosedImage=$row->question->opt_b_image:$choosedImage=null;
                    }
                    if($row->choosed_option=='c'){
                        $choosedOpt=$row->question->questionTranslations[0]->opt_c;
                        ($row->question->opt_c_image!=1)?$choosedImage=$row->question->opt_c_image:$choosedImage=null;
                    }

                    if($row->is_answered==1){
                        if($row->is_correct_ans==1){
                            $qStatus='Correct';
                        }else{
                            $qStatus='Wrong';
                        }

                    }else{
                        $qStatus='-';
                    }



                    $array = array(
                        'id' =>  $row->id,
                        'question' =>  $row->question->questionTranslations[0]->q_title,
                        'choosed_ans' => $choosedOpt,
                        'choosedImage' => $choosedImage,
                        'correct_ans' => $correctOpt,
                        'correctImage' => $correctImage,
                        'q_status' => $qStatus,

                    );
                    $resData->push($array);
                    $choosedOpt='';
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

    //getPracticeResult
    public function getPracticeResult(){

        try{

            $response=$this->exam->getPracticeResult();
            if($response['status']) {
                $resData = collect([]);
                foreach ($response['data'] as $row) {

                    $row->solvedQuestion->where('is_correct_ans',1)->count();

                    $totalAnsweredQ=$row->solvedQuestion->where('is_answered',1)->count();
                    $correctQ=$row->solvedQuestion->where('is_correct_ans',1)->count();
                    $wrongQ=$totalAnsweredQ - $correctQ;
                    $array = array(
                        'id' => $row->id,
                        'attempt_id' => $row->id,
                        'test_date' => date('d M Y', strtotime($row->created_at)),
                        'std_name' => $row->student->std_name,
                        'traffic_id' => $row->student->traffic_id,
                        'course' => $row->student->activeCourse->course->short_name,
                        'totalQ' =>$row->solvedQuestion->count(),
                        'correctAns' =>$row->solvedQuestion->where('is_correct_ans',1)->count(),
                        'wrongAns' =>$wrongQ,
                        'skipAns' => $row->solvedQuestion->where('is_answered',0)->count(),
                    );
                    $resData->push($array);
                }
                return Helper::success($resData, 'Result list');
            }
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }

    //getScheduleExamList
    public function getScheduleExamList(Request $request){

        try{
            $request->all();
            $response=$this->exam->getScheduleExamList($request);
            if($response['status']){
                return Helper::success($response['data'],'Schedule exam list');
            }else{
                return Helper::errorWithData('Record not exist',[]);
            }
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }

    //getRunningExam
    public function getRunningExam(Request $request){

        try{
            $request->all();
            $response=$this->exam->getScheduleExamList($request);
            if($response['status']){
                $resData = collect([]);

                foreach ($response['data'] as $row){
                    $examStartFrom='-';
                    $courseInfo=Helper::fetchOnlyData($this->course->getCourseConfig($row->course_id));
                    $row->exam_type ==1 ? $examDuration=$courseInfo->courseConfig->total_duration:$examDuration=$courseInfo->courseConfig->practice_duration;
                    if($row->exam_status==2){
                        $examStartFrom = Carbon::parse($row->updated_at)->addMinutes($examDuration);
                        $examStartFrom=date('d-M-Y H:i:s',strtotime($examStartFrom));
                    }


                    $topicArray = array(
                        'id'=> $row->id,
                        'traffic_id'=> $row->student->traffic_id,
                        'std_name' =>$row->student->std_name,
                        'course' =>$row->course->short_name,
                        'invigilator' =>$row->invigilator->name,
                        'exam_type' =>$row->exam_type==1?'Exam':'Practice',
                        'system' =>$row->system->title,
                        'exam_start_from' =>$examStartFrom,
                        'created_at' =>$row->created_at,
                        'exam_status' =>$row->exam_status,
                    );
                    $resData->push($topicArray);
                }
                return Helper::success($resData,'Schedule exam list');
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

            $isContinue= $this->exam->checkExamStartOrNot($id);
            if($isContinue!==1){
                return  $response= Helper::error('student has been started exam',[]);
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


    //getStudentResult
    public function getStudentResult(Request $request){

        try{


            $exam_id=$request->exam_id;
            $res=$this->exam->getExamWiseResult($exam_id);

            if($res){

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
                    'topics'=>$resData,
                    'examLang'=>$res->exam->qLanguage->lang,
                    'langShort'=> $res->exam->qLanguage->lang_short,
                );

                return Helper::success($array,'Result list');
            }else{
                return Helper::error('Exam not found',[]);
            }

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

    //examSystemStatusUpdate
    public function examSystemStatusUpdate(Request $request){
        try {
            if($request->exam_id){
                $examUpdate= $this->exam->updateExamScheduleStatus($request->exam_id,3);
            }
            if($request->system_ip){
                $system=  System::where('system_ip',$request->system_ip)->first();
                $this->system->updateSystemStatus($system->id,1);
            }


            return   $response= Helper::success([],'Exam and system updated successfully');

        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }

    //sendResultEmailAndSms
    public function sendResultEmailAndSms(Request $request){
//        try {


            $user = User::find(3);
            $trafficId=16109070;



            $config= Configuration::latest('id')->first();
            $emailTemplate=$config->email_template;
            $smsTemplate=$config->sms_template;

            $student= Student::where('traffic_id',16109070)->first();
            $resultId= Result::where('exam_id',1)->first();

            $type=1;

            if($result=Result::find($resultId->id)){
                ($type==1)?$result->is_send_email=1:$result->is_send_sms=1;
                $result->save();
            }

            $data = [
                'std_name' => $student->std_name,
                'exam_status' =>($exam AND $exam->status==1)?'Pass':'Fail'
            ];


            foreach ($data as $key => $value) {
                $placeholder = '{{' . $key . '}}';
                $emailTemplate = str_replace($placeholder, $value, $emailTemplate);
                $smsTemplate = str_replace($placeholder, $value, $smsTemplate);
            }

            $mailData = [
                'email' =>$emailTemplate,
            ];

            $user->notify(new SendMailandSmsNotification($mailData,$trafficId));




            dd('Done');

//        } catch (\Exception $e) {
//            return Helper::error($e->getMessage(),$e);
//        }
    }


    //storeResultPdf
    public function storeResultPdf(Request $request)
    {
        try {

            $path = 'results/';
            $trafficId = $request->trafficId;
            $examId = $request->examid;


            if ($request->hasFile('pdf')) {
                $pdf = $request->file('pdf');
                $fileName = $path . $trafficId . '-result' . '.pdf'; // Append .pdf extension
                $pdf->move(public_path('storage/uploads/' . $path), $fileName);

                if ($result = Result::where('exam_id', $request->examid)->latest('id')->first()) {
                    $result->pdf_file = $fileName;
                    $result->save();

                $this->sendSmsAndEmail($trafficId,$examId);

                    return response()->json(['message' => 'PDF uploaded successfully']);
                }
                return response()->json(['message' => 'No PDF file uploaded'], 400);
            }

        } catch (\Exception $e) {
            return Helper::error($e->getMessage(), $e);
        }
    }

    public function sendSmsAndEmail($trafficId,$examId)
    {
        try {

            $result = Result::where('exam_id',$examId)->first();
            $student = Student::where('traffic_id', $trafficId)->first();

            $this->exam->sendEmail($trafficId,$examId,$result,$student);
            $this->exam->sendSms($student,$result);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
