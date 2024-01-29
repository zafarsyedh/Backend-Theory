<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\ExamRequest;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\QuestionInterface;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ExamController extends Controller
{
    public  $exam;
    public  $questions;
    public function __construct(ExamInterface $exam,QuestionInterface $questions)
    {
        $this->exam=$exam;
        $this->questions=$questions;
    }

    public function getQuestionsForExam(Request $request){

        try{

             $request->all();
            $std_id=$request->std_id;
            $lang= $request->q_lang;
            $exam_type=$request->exam_type;

             $response=$this->questions->questionMoveInSolvedQuestionTable($request);
            if($response['status']){

               $res=$this->questions->getMovedQuestionForTheoryPractice($request,$response['data']);
               return Helper::success($res,'Questions list');
            }else{
                return Helper::errorWithData('Record not exist',[]);
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
}
