<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\Attempt;
use App\Models\Course;
use App\Models\ExamSchedule;
use App\Models\QuestionSolved;
use App\Models\Result;
use App\Models\Student;
use App\Models\TopicArea;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ExamClass implements Interfaces\ExamInterface
{

    public function saveExamQuestion($request)
    {
        try {
            DB::beginTransaction();

            if(count($request->markedQuestions) > 0) {
                $correctAns=0;
                foreach ($request->markedQuestions as $q) {

                    ($q['correctAns'] == $q['selectedValue'])? $correctAns++ :'';

                    $examQ = QuestionSolved::find($q['id']);
                    $examQ->choosed_option = $q['selectedValue'];
                    $examQ->is_correct_ans = ($q['correctAns'] == $q['selectedValue']) ? 1 : 0;
                    $examQ->is_answered = 1;
                    $examQ->save();

                }
                $data=[
                    'exam_id'=>$request->exam_id,
                    'totalCorrectAns'=>$correctAns,
                    'test_duration'=>intval($request->examDuration/60),
                ];

                $this->createResult($data);
                $this->updateAttemptStatus($examQ->attempt_id);
                $this->updateExamScheduleStatus($request->exam_id);
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
            return Helper::successWithData([],'record save');

        }  catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(), []);
        }
    }
    //getScheduleExamList
    public function  getScheduleExamList()
    {
        try {
            $qry=ExamSchedule::query();
            $qry->with('student:id,std_name,traffic_id,email','course:id,short_name','qLanguage:id,lang,lang_short','audioLanguage:id,lang,lang_short');
            $qry->with('invigilator:id,name','system:id,title,system_ip');
            $examSchedule=$qry->get();
            return Helper::successWithData($examSchedule,'record found');

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
                'audio_lang' => 'required',
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

    public function checkExamStatus($stdData)
    {
        try {
            $isContinue=1;
           if($std= Student::where('traffic_id',$stdData['regnnumb'])->latest('id')->first()){
               if(ExamSchedule::where('std_id',$std->id)->where('exam_status','!=',3)->count() > 0){
                   $isContinue=0;
               }
           }
            return $isContinue;
        }catch (\Exception $e) {
            return Helper::errorWithData($e->getMessage(),$e);
        }
    }
    public function updateExamScheduleStatus($id)
    {
        try {
            $exam=ExamSchedule::find($id);
            $exam->exam_status=3;
            $exam->save();

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

            if($courseConfiguration[0]->require_type==1){
                $totalRequireQuestion=$courseConfiguration[0]->specific_require +  $courseConfiguration[0]->common_require + $courseConfiguration[0]->video_require;
            }else{
                $totalRequireQuestion=$courseConfiguration[0]->total_require;
            }

            $result = Result::updateOrCreate(
                [
                    'id' =>0,
                ],

                [
                    'exam_id' =>$data['exam_id'],
                    'total_duration' =>$courseConfiguration[0]->total_duration,
                    'test_duration' =>$courseConfiguration[0]->total_duration - $data['test_duration'],
                    'total_question' => $courseConfiguration[0]->specific_question +  $courseConfiguration[0]->common_question + $courseConfiguration[0]->video_question,
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
            $qry=$qry->with('attempt.student');
            $qry=$qry->where('exam_id',$examId);
            $qry=$qry->latest('id')->first();
            return $qry;
            }catch (\Exception $e) {
            throw $e;
        }
    }
}
