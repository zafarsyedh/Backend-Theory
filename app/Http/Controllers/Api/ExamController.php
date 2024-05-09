<?php

namespace App\Http\Controllers\Api;

use App\Events\CourseEvent;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\ExamRequest;
use App\Models\Attempt;
use App\Models\Configuration;
use App\Models\ExamSchedule;
use App\Models\QuestionSolved;
use App\Models\Result;
use App\Models\Student;
use App\Models\System;
use App\Models\User;
use App\Notifications\SendMailandSmsNotification;
use App\Repo\ConfigurationClass;
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

                    $attempt=Attempt::find($response['data']);
                    $request->merge(['exam_id' =>$attempt->exam_id]);
                    $this->checkPracticeAttemptComplete($request);
                }
                return Helper::success($response,'Practice Questions saved');
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


    public function getAllResults(Request $request){

        try{


            $response=$this->exam->getAllResultsList($request);
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
    public function getPracticeResult(Request $request){

        try {

            $response = $this->exam->getPracticeResult($request);
            $resData = collect([]);
            if($response['status']) {

               foreach ($response['data'] as $row) {


                   $data['specificSolved']= 0;
                   $data['commonSolved']= 0;
                   $data['videoSolved']= 0;
                   $data['specificCorrect']= 0;
                   $data['commonCorrect']= 0;

                   $data['specificAttemptId']= 0;
                   $data['commonAttemptId']= 0;
                   $data['videoAttemptId']= 0;

                   $data['specificTotalQ']=$row->student->activeCourse->course->courseConfig->p_specific_question;
                   $data['commonTotalQ']= $row->student->activeCourse->course->courseConfig->p_common_question;
                   $data['videoTotalQ']= $row->student->activeCourse->course->courseConfig->p_video_question;


                   $totalQ=$data['specificTotalQ'] +   $data['commonTotalQ'];

                foreach ($row->attempts as $attempts){

                   // $totalQ= $attempts->solvedQuestion->count();
                    $solvedQ= $attempts->solvedQuestion->where('is_answered',1)->count();
                    $correctAns= $attempts->solvedQuestion->where('is_correct_ans',1)->count();



                    if($attempts->practice_type==1){

                        $data['specificSolved']= $solvedQ;
                        $data['specificCorrect']= $correctAns;
                        $data['specificAttemptId']= $attempts->id;
                    }
                    if($attempts->practice_type==2){

                        $data['commonSolved']= $solvedQ;
                        $data['commonCorrect']= $correctAns;
                        $data['commonAttemptId']= $attempts->id;
                    }
                    if($attempts->practice_type==3){

                        $data['videoSolved']= $solvedQ;
                        $data['videoCorrect']= $correctAns;
                        $data['videoAttemptId']= $attempts->id;
                    }

                    $totalCorrect=$data['specificCorrect'] + $data['commonCorrect'];
                    $correctPercentage=($totalCorrect /$totalQ) *100;
                }



                   $array = array(
                        'id' => $row->id,
                        'test_date' => date('d M Y', strtotime($row->attempts[0]->created_at)),
                        'std_name' => $row->student->std_name,
                        'traffic_id' => $row->student->traffic_id,
                        'course' => $row->student->activeCourse->course->short_name,

                        'specificAttemptId' => $data['specificAttemptId'],
                        'commonAttemptId' => $data['commonAttemptId'],
                        'videoAttemptId' => $data['videoAttemptId'],
                        'specificQ' => $data['specificTotalQ'],
                        'specificSolved' => $data['specificSolved'],
                        'commonQ' => $data['commonTotalQ'],
                        'commonSolved' => $data['commonSolved'],
                        'videoTotalQ' => $data['videoTotalQ'],
                        'videoSolved' => $data['videoSolved'],
                       'grandTotalQ'=> $totalQ,
                       'totalSolved'=>  $data['specificSolved'] + $data['commonSolved'],
                       'percentage'=>round($correctPercentage),
                    );

                   $resData->push($array);

               }

            }
            if($resData->count() > 0){
                return Helper::success($resData,'Practice results');
            }else{
                return Helper::error('Record not exist',[]);
            }


//            if($response['status']) {
//                $resData = collect([]);
//                foreach ($response['data'] as $row) {
//
//                    $practiceType='';
//                    if($row->practice_type==1){
//                        $practiceType='Specific';
//                    }
//                    if($row->practice_type==2){
//                        $practiceType='Common';
//                    }
//                    if($row->practice_type==3){
//                        $practiceType='Video';
//                    }
//
//                    //use 1 for common 2 for specific
//
//
////                      $specificTotalCount = $row->solvedQuestion()->whereHas('question', function ($query) {
////                        $query->where('q_type',2);
////                         })->count();
////
////
////                    $specificSolvedCount = $row->solvedQuestion()->whereHas('question', function ($query) {
////                        $query->where('q_type', 2);
////                    })->where('is_answered', 1)->count();
////
////
////                    $commonTotalCount = $row->solvedQuestion()->whereHas('question', function ($query) {
////                        $query->where('q_type',1);
////                        })->count();
////
////                      $commonSolvedCount = $row->solvedQuestion()->whereHas('question', function ($query) {
////                        $query->where('q_type', 1);
////                    })->where('is_answered', 1)->count();
////
////                    $videoTotalCount= $row->solvedQuestion()->whereHas('question', function ($query) {
////                        $query->where('q_is_video',1);
////                    })->count();
////
////                    $videoSolvedCount = $row->solvedQuestion()->whereHas('question', function ($query) {
////                        $query->where('q_is_video',1);
////                    })->where('is_answered', 1)->count();
//
//
//
//
//                    $totalAnsweredQ=$row->solvedQuestion->where('is_answered',1)->count();
//                    $correctQ=$row->solvedQuestion->where('is_correct_ans',1)->count();
//                    $wrongQ=$totalAnsweredQ - $correctQ;
//
//                    $totalQ=$row->solvedQuestion->count();
//                    $skipQ=$row->solvedQuestion->where('is_answered',0)->count();
//
//                    $array = array(
//                        'id' => $row->id,
//                        'attempt_id' => $row->id,
//                        'test_date' => date('d M Y', strtotime($row->created_at)),
//                        'std_name' => $row->student->std_name,
//                        'traffic_id' => $row->student->traffic_id,
//                        'course' => $row->student->activeCourse->course->short_name,
////
//                        'totalQ' =>$totalQ,
////                        'specificTotal' => $specificTotalCount,
////                        'specificSolved' => $specificSolvedCount,
////                        'commonTotal' => $commonTotalCount,
////                        'commonSolved' => $commonSolvedCount,
////                        'videoTotal' => $videoTotalCount,
////                        'videoSolved' => $videoSolvedCount,
//                        'correctAns' =>$row->solvedQuestion->where('is_correct_ans',1)->count(),
//                        'wrongAns' =>$wrongQ,
//                        'skipAns' => $skipQ,
//                        'solvedQ'=>$totalQ - $skipQ,
//                        'practice_type'=>$practiceType
//                    );
//                    $resData->push($array);
//                }
//                return Helper::success($resData, 'Result list');
//            }
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
                        'created_at' =>date('d M,Y H:i:s',strtotime($row->created_at)),
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
                return Helper::error('Result not found',[]);
            }

        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }

    //getLogs
    public function getLogs(Request $request){

        try{

            $res=$this->exam->getLogs($request);
            if($res){
                $resData = collect([]);
                foreach ($res->get('data') as $row){
                    $array = array(
                        'std_name' =>$row->exam->student->std_name,
                        'traffic_id' =>$row->exam->student->traffic_id,
                        'created_at' => date('d M,Y',strtotime($row->created_at)),
                        'content' => $row->content,
                        'type' => ($row->type==2)?'Email':'SMS',
                        'isSend' => ($row->is_send==2)?'No':'Yes',
                        'exam_id' => $row->exam_id,
                        'notificationType' =>$row->type,
                    );
                    $resData->push($array);
                }
                return Helper::success($resData,'Logs list');
            }else{
                return Helper::error('Logs not found',[]);
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

            if($request->exam_id AND $request->closeType==1){
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
        $examId=6;
        $trafficId=16109071;

        $result = Result::where('exam_id',$examId)->first();
        $student = Student::where('traffic_id', $trafficId)->first();

        $config=new ConfigurationClass();
        $otpText =$config->getEmailSmsTemplate($student,$result,2);

      return $res=$this->exam->sendSms($student,$result,$otpText);
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



                $this->sendSmsAndEmail($trafficId,$examId,2);
                $this->sendSmsAndEmail($trafficId,$examId,1);
                    return response()->json(['message' => 'PDF uploaded successfully']);
                }
                return response()->json(['message' => 'No PDF file uploaded'], 400);
            }

        } catch (\Exception $e) {
            return Helper::error($e->getMessage(), $e);
        }
    }

    public function sendSmsAndEmail($trafficId,$examId,$type)
    {
        try {

            $result = Result::where('exam_id',$examId)->first();
            $student = Student::where('traffic_id', $trafficId)->first();


            $configuration=Configuration::latest('id')->first();

            $config=new ConfigurationClass();
            $emailContent=$config->getEmailSmsTemplate($student,$result,1);
            $mailData = [
                'email' =>$emailContent,
            ];

            $isSendEmail=2;
            if($configuration AND $configuration->enable_email==1 AND $type==2){
                $this->exam->sendEmail($trafficId,$examId,$result,$student,$mailData);
                $isSendEmail=1;
            }
            if($type==2){
                //email log
                $this->exam->storeSmsEmailLog($examId,2,$isSendEmail,$emailContent);
            }


            $config=new ConfigurationClass();
            $otpText =$config->getEmailSmsTemplate($student,$result,2);

            $isSendSms=2;
            if($configuration AND $configuration->enable_sms==1 AND $type==1){
                $isSendSms=1;
                $this->exam->sendSms($student,$result,$otpText);
            }

            if($type==2) {
                //sms log
                $this->exam->storeSmsEmailLog($examId, 1, $isSendSms, $otpText);
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function resendNotification(Request $request){


        $configuration=Configuration::latest('id')->first();
        if($request->type==2 AND $configuration->enable_email==0){
            return Helper::error('Email disabled',[]);
        }
        if($request->type==2){
            $this->sendSmsAndEmail($request->traffic_id,$request->exam_id,2);
            return Helper::success([],'Email send successfully');
        }

        if($request->type==1 AND $configuration->enable_sms==0){
            return Helper::error('SMS disabled',[]);
        }
        if($request->type==1){
            $this->sendSmsAndEmail($request->traffic_id,$request->exam_id,1);
            return Helper::success([],'Sms send successfully');
        }
    }

    public  function getExamAttemptInfo(Request $request)
    {
        try {
            $data['isEnableSpecific']=1;
            $data['isEnableCommon']=1;
            $data['isEnableVideo']=1;

           $examAttemptInfo=$this->exam->getExamAttemptInfo($request->exam_id);

           if($examAttemptInfo->count() > 0){
               $specific= $examAttemptInfo->where('practice_type',1)->first();
               $common= $examAttemptInfo->where('practice_type',2)->first();
               $video= $examAttemptInfo->where('practice_type',3)->first();

               if($specific){
                   if($specific->practice_type==1 AND $specific->status==0){
                       $data['isEnableCommon']=0;
                       $data['isEnableVideo']=0;
                   }else{
                       $data['isEnableSpecific']=0;
                   }
               }

               if($common){
                   if($common AND $common->practice_type==2 AND $common->status==0){
                       $data['isEnableSpecific']=0;
                       $data['isEnableVideo']=0;
                   }else{
                       $data['isEnableCommon']=0;
                   }
               }

               if($video){
                   if($video AND $video->practice_type==3 AND $video->status==0){
                       $data['isEnableSpecific']=0;
                       $data['isEnableCommon']=0;
                   }else{
                       $data['isEnableVideo']=0;
                   }
               }
           }
             $response=Helper::fetchOnlyData($this->course->getCourseConfig($request->course_id));

           if($response AND $response->courseConfig){
                if($response->courseConfig->p_specific_question==0){
                    $data['isEnableSpecific']=0;
                }
               if($response->courseConfig->p_common_question==0){
                   $data['isEnableCommon']=0;
               }
               if($response->courseConfig->p_video_question==0){
                   $data['isEnableVideo']=0;
               }

           }
           return Helper::success($data,'Record found');


        } catch (\Exception $e) {
            throw $e;
        }
    }


    public  function checkPracticeAttemptComplete(Request $request)
    {
        try {

            $exam=ExamSchedule::with('course.courseConfig','system:id,system_ip')->find($request->exam_id);

            $data['specificSolved']=0;
            $data['commonSolved']=0;
            $data['videoSolved']=0;

            $attempt=Attempt::where('exam_id',$request->exam_id)->get();

                if($exam AND $exam->course->courseConfig->p_specific_question > 0 ){
                    if($attempt AND $attempt->where('practice_type',1)->pluck('status')->first()==1){
                        $data['specificSolved']=1;
                }
            }else{
                    $data['specificSolved']=1;
                }

            if($exam AND $exam->course->courseConfig->p_common_question > 0 ){
                if($attempt AND $attempt->where('practice_type',2)->pluck('status')->first()==1){
                    $data['commonSolved']=1;
                }
            }else{
                $data['commonSolved']=1;
            }


            if($exam AND $exam->course->courseConfig->p_video_question > 0 ){
                if($attempt AND $attempt->where('practice_type',3)->pluck('status')->first()==1){
                    $data['videoSolved']=1;
                }
            }else{
                $data['videoSolved']=1;
            }
            if($data['commonSolved']==1 AND   $data['specificSolved']==1 AND   $data['videoSolved']==1){
                $request->merge(['exam_id' => $request->exam_id]);
                $request->merge(['system_ip' => $exam->system->system_ip]);


                return  $this->examSystemStatusUpdate($request);
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

}
