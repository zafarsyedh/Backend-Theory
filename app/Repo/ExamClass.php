<?php

namespace App\Repo;
use App\Http\Helpers\Helper;
use App\Models\ExamSchedule;
use App\Models\QuestionSolved;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Validator;


class ExamClass implements Interfaces\ExamInterface
{

    public function saveExamQuestion($request)
    {
        try {
            if(count($request->markedQuestions) > 0) {
                foreach ($request->markedQuestions as $q) {

                    $examQ = QuestionSolved::find($q['id']);
                    $examQ->choosed_option = $q['selectedValue'];
                    $examQ->is_correct_ans = ($q['correctAns'] == $q['selectedValue']) ? 1 : 0;
                    $examQ->is_answered = 1;
                    $examQ->save();

                }
            }
            return Helper::successWithData([],'record save');

        }  catch (\Exception $e) {
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
                return  Helper::successWithData($exam,'Record created successfully');

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
}
