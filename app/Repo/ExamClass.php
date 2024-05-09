<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Attempt;
use App\Models\Configuration;
use App\Models\Course;
use App\Models\ExamSchedule;
use App\Models\QuestionSolved;
use App\Models\Result;
use App\Models\SmsEmailLog;
use App\Models\Student;
use App\Models\TopicArea;
use App\Models\User;
use App\Notifications\SendMailandSmsNotification;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Faker\Provider\DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use function Laravel\Prompts\error;


class ExamClass implements Interfaces\ExamInterface
{

    public function saveExamQuestion($request)
    {
        try {
            DB::beginTransaction();

            if(count($request->markedQuestions) > 0) {
                $correctAns=0;
                foreach ($request->markedQuestions as $q) {

                    if($q['selectedValue']){

                    ($q['correctAns'] == $q['selectedValue'])? $correctAns++ :'';

                    $examQ = QuestionSolved::find($q['id']);
                    $examQ->choosed_option = $q['selectedValue'];
                    $examQ->is_correct_ans = ($q['correctAns'] == $q['selectedValue']) ? 1 : 0;
                    $examQ->is_answered = 1;
                    $examQ->save();
                    }
                }

                $startDate = Carbon::parse($request->createdAt);
                 $endDate = Carbon::now();


                $diff = $startDate->diff($endDate);
               // $mainDiff= $diff->h.':'. $diff->i .':'. $diff->s;
                $mainDiff= $diff->i .':'. $diff->s;

                $data=[
                    'exam_id'=>$request->exam_id,
                    'totalCorrectAns'=>$correctAns,
                    'test_duration'=> $mainDiff,
                ];

                $this->createResult($data);
               $this->updateAttemptStatus($request->examAttemptId);
                $this->updateExamScheduleStatus($request->exam_id,3);
            }else{
              $solvedQuestion= QuestionSolved::where('attempt_id',$request->examAttemptId) ->get();
              if($solvedQuestion->count() > 0){

                  $startDate = Carbon::parse($request->createdAt);
                  $endDate = Carbon::now();


                  $diff = $startDate->diff($endDate);
                  $mainDiff= $diff->i .':'. $diff->s;

                  foreach ($solvedQuestion as $solveQ) {

                      $examQ = QuestionSolved::find($solveQ->id);
                      $examQ->choosed_option = null;
                      $examQ->is_correct_ans =0;
                      $examQ->is_answered =0;
                      $examQ->save();
                  }

                  $data=[
                      'exam_id'=>$request->exam_id,
                      'totalCorrectAns'=>0,
                      'test_duration'=> $mainDiff,
                  ];

                  $this->createResult($data);
                  $this->updateAttemptStatus($request->examAttemptId);
                  $this->updateExamScheduleStatus($request->exam_id,3);
              }
            }

            DB::commit();

            return Helper::successWithData([],'record save');

        }  catch (\Exception $e) {
            DB::rollBack();
            return Helper::errorWithData($e->getMessage(), []);
        }
    }
    public function savePracticeQuestion($request)
    {
        try {
                $examQ= QuestionSolved::find($request->id);
                $examQ->choosed_option=$request->selectedOpt;
                $examQ->is_correct_ans=($request->selectedOpt==$request->correctOpt)?1:0;
                $examQ->is_answered=1;
                $examQ->save();


            return Helper::successWithData($examQ->attempt_id,'record save');

        }  catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), []);
        }
    }
    //getScheduleExamList
    public function  getScheduleExamList($request)
    {
        try {
               $user= User::find($request->invgId);

            $qry=ExamSchedule::query();
            $qry->with('student:id,std_name,traffic_id,email','course:id,short_name','qLanguage:id,lang,lang_short','audioLanguage:id,lang,lang_short');
            $qry->with('invigilator:id,name','system:id,title,system_ip');
            ($user AND $user->role_id!==1)?$qry=$qry->where('invg_id',$request->invgId):'';
            $qry=$qry->whereDate('created_at', '>=',$request->start_date);
            $qry=$qry->whereDate('created_at', '<=',$request->end_date);
            $examSchedule=$qry->get();
            return Helper::successWithData($examSchedule,'record found');

        }  catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), []);
        }
    }

    public function  getAllResultsList($request)
    {
        try {
            $qry=Result::query();
            $qry=$qry->with('exam.attempt:id,exam_id','exam.course:id,short_name','exam.student:id,traffic_id,std_name');
            $qry=$qry->whereDate('created_at', '>=',date('Y-m-d',strtotime($request->start_date)));
            $qry=$qry->whereDate('created_at', '<=',date('Y-m-d',strtotime($request->end_date)));
            $examSchedule=$qry->get();

            return Helper::successWithData($examSchedule,'record found');

        }  catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), []);
        }
    }

    //getPracticeResult
    public function  getPracticeResult($request)
    {
        try {



            $qry = ExamSchedule::query();
            $qry=$qry->with('student:id,std_name,traffic_id');
            $qry->has('attempts');


//            $qry->with(['attempts' => function ($query) {
//                $query->with('solvedQuestion');
//            }]);

            $qry->with(['attempts' => function ($query) use ($request) {
                $query->whereDate('created_at', '>=', date('Y-m-d',strtotime($request->start_date)))
                    ->whereDate('created_at', '<=', date('Y-m-d',strtotime($request->end_date)))
                    ->with('solvedQuestion');
            }]);

            $records = $qry->get();
            if($records->count() > 0){
                return Helper::successWithData($records, 'Records found');
            }else{
                return Helper::error('Records not found',[]);
            }

        }  catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), []);
        }
    }



    public function createExam($request,$data=null)
    {

        try {

            $trans = ExamSchedule::updateOrCreate(
                [
                    'id' =>0
                ],
                [
                    'system_id' =>$request->system_id,
                    'std_id' =>$data['stdId'],
                    'course_id' =>$data['courseId'],
                    'invg_id' =>$data['invgId'],
                    'q_lang' =>$request->q_lang,
                    'audio_lang' =>$request->audio_lang,
                    'exam_status' =>1,
                    'exam_type' =>$request->exam_type,

                ]
            );

            return Helper::success($trans,'Record created successfully');
        } catch (ValidationException $validationException) {
            throw $validationException;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateExam($request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'exam_type' => 'required',
                'q_lang' => 'required',
                'system_id' => 'required',

            ]);
            if ($validator->fails())
                return Helper::errorWithData($validator->errors()->first(), $validator->errors());

            if($exam=ExamSchedule::find($request->id)){
                $exam->system_id=$request->system_id;
                $exam->q_lang=$request->q_lang;
                $exam->audio_lang=$request->audio_lang;
                $exam->exam_type=$request->exam_type;
                $exam->save();


                $qry=ExamSchedule::query();
                $qry->with('student:id,std_name,traffic_id,email','course:id,short_name','qLanguage:id,lang,lang_short','audioLanguage:id,lang,lang_short');
                $qry->with('invigilator:id,name','system:id,title,system_ip');
                $examSchedule=$qry->find($request->id);
                return  Helper::successWithData($examSchedule,'Record created successfully');

            }
            else{
                return Helper::error('Exam not exist',[]);
            }

        } catch (ValidationException $validationException) {
            throw $validationException;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    //    public function deleteSystem($id);
    public function deleteExam($id)
    {

        try {
            $role = ExamSchedule::find($id);
            $role->delete();
            return Helper::successWithData($role, $message="Exam Deleted");
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }

    public function checkExamStatus($stdData,$examType)
    {
        try {
            $isContinue=1;
           if($std= Student::where('traffic_id',$stdData['regnnumb'])->latest('id')->first()){

               if (ExamSchedule::where('std_id', $std->id)
                       ->where(function ($query) {
                           $query->where('exam_status', 1)
                               ->orWhere('exam_status', 2);
                       })
                       //->whereDate('created_at', date('Y-m-d'))
//                       ->where('exam_type',$examType)
                       ->count() > 0) {
                   $isContinue = 0;
               }
           }
            return $isContinue;
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function checkExamStartOrNot($id)
    {
        try {
            $isContinue=0;
            $exam=ExamSchedule::find($id);
            if($exam->exam_status==1 OR $exam->exam_status==2){
                $isContinue=1;
            }
            return $isContinue;
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function updateExamScheduleStatus($id,$status)
    {
        try {
            $exam=ExamSchedule::find($id);
            $exam->exam_status=$status;
            $exam->save();
            return $exam;

        }catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateAttemptStatus($id)
    {
        try {
            $attempt=Attempt::find($id);
            $attempt->status=1;
            $attempt->save();

            }catch (\Exception $e) {
                throw $e;
            }
    }

    public function getAttemptInfo($attemptId)
    {
        try {
            $qry=Attempt::query();
            $qry=$qry->with('student.activeCourse.course');
          return $qry=$qry->find($attemptId);

        }catch (\Exception $e) {
            throw $e;
        }
    }


    public function getSolvedQuestionAccordingAttempt($attemptId)
    {
        try {

            $qry = TopicArea::with(['topicAreaTranslation', 'solvedQuestion' => function($query) use ($attemptId) {
                $query->where('attempt_id', $attemptId);
            }])->get();
            return $qry;
        }catch (\Exception $e) {
            throw $e;
        }
    }

    public function createResult($data)
    {
        try {

            $course=new CourseClass();
             $courseInfo= Helper::fetchOnlyData($course->getCourseConfig(1));
            $courseConfiguration=$courseInfo->courseConfig;

            if($courseConfiguration->require_type==1){
                $totalRequireQuestion=$courseConfiguration->specific_require +  $courseConfiguration->common_require + $courseConfiguration->video_require;
            }else{
                $totalRequireQuestion=$courseConfiguration->total_require;
            }

            $result = Result::updateOrCreate(
                [
                    'id' =>0,
                ],

                [
                    'exam_id' =>$data['exam_id'],
                    'total_duration' =>$courseConfiguration->total_duration,
                    'test_duration' =>$data['test_duration'],
                    'total_question' => $courseConfiguration->specific_question +  $courseConfiguration->common_question + $courseConfiguration->video_question,
                    'correct_ans' => $data['totalCorrectAns'],
                    'correct_ans_required'=>$totalRequireQuestion,
                    'status' =>($data['totalCorrectAns'] >=$totalRequireQuestion)?1:0,
                ]
            );
        }catch (\Exception $e) {
            throw $e;
        }
    }

    public function getExamWiseResult($examId)
    {
        try {
            $qry = Result::query();
            $qry=$qry->with('attempt.student','exam.qLanguage');
            $qry=$qry->where('exam_id',$examId);
            $qry=$qry->latest('id')->first();
            return $qry;
            }catch (\Exception $e) {
            throw $e;
        }
    }


    public function checkPracticeType($request)
    {
        try {
            $attemptId=404;
            $qry = Attempt::query();
            $qry=$qry->where('std_id',$request->std_id);
            $qry=$qry->where('practice_type',$request->practice_type);
//            $qry=$qry->where('exam_id',$request->exam_id);
//            $qry=$qry->where('status',0);
            $qry=$qry->latest('id')->first();

            if($qry AND $qry->status==0){
                $attemptId= $qry->id;
            }
            return $attemptId;
        }catch (\Exception $e) {
            throw $e;
        }
    }

    public function sendEmail($trafficId,$examId,$result,$student,$mailData)
    {
        try {
            $student->notify(new SendMailandSmsNotification($mailData,$trafficId));
            if($result){
                $this->updateMailAndSmsStatus($result->id,1);
            }



        }catch (\Exception $e) {
            throw $e;
        }
    }

    public function sendSms($student,$result,$otpText)
    {

                $contact =$student->mobile_no;
                $trafficId=$student->traffic_id;

                $purpose='Result Notification';

                $isUseSmsGlobal = env('Is_USE_SMS_GLOBAL');
                if($isUseSmsGlobal==true){
                    $response=Http::get('https://api.smsglobal.com/http-api.php?action=sendsms&user=quwsoxno&password=pR9gUDSq&from=BELHASA-DC&to=' . $contact . '&text=' . $otpText);
                }else{
                    $response=Http::get('https://sms.bdc.ae/api/send?api_token='.env('SMS_API_TOKEN').'&traffic_id='.$trafficId.'&phone='.$contact.'&message='.$otpText.'&purpose='.$purpose);
                }

                $parts = explode(' ', $response);
                if (count($parts) > 1 && $parts[0] !== 'ERROR:') {
                    $this->updateMailAndSmsStatus($result->id,2);
                    // 'message  send';
                }
    }

    public function storeSmsEmailLog($examId,$type,$isSend,$content)
    {
        $email = SmsEmailLog::updateOrCreate(
            [
                'exam_id' =>$examId,
                'type' =>$type,
            ],
            [
                'exam_id' =>$examId,
                'content' =>$content,
                'type' =>$type,
                'is_send' =>$isSend,
            ]
        );
    }
    public function updateMailAndSmsStatus($resultId,$type)
    {
        // type 1 mean for email and 2 mean for sms
      if( $result=Result::find($resultId)){
          ($type==1)?$result->is_send_email=1:$result->is_send_sms=1;
          $result->save();
      }
    }

    public function  getLogs($request)
    {
        try {
            $qry=SmsEmailLog::query();
            $qry=$qry->with('exam.student');
            $qry=$qry->whereDate('created_at', '>=',date('Y-m-d',strtotime($request->start_date)));
            $qry=$qry->whereDate('created_at', '<=',date('Y-m-d',strtotime($request->end_date)));
            $examSchedule=$qry->get();
            return Helper::successWithData($examSchedule,'record found');

        }  catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), []);
        }
    }
    public function  getExamAttemptInfo($examId)
    {
        try {
            $qry=Attempt::query();
            $qry=$qry->where('exam_id',$examId);
            return $qry->get();

        }  catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), []);
        }
    }

    public function getExamIdOnTheBaseOfTrafficIdNumber($trafficId)
    {
        try {
            $examId=0;
            if($std= Student::where('traffic_id',$trafficId)->latest('id')->first()){

                $exam=ExamSchedule::where('std_id', $std->id)
                        ->where(function ($query) {
                            $query->where('exam_status',1)
                                ->orWhere('exam_status',2);
                        })
                        ->first();
                if($exam){
                    $examId=$exam->id;
                }

            }
            return  $examId;
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
}
