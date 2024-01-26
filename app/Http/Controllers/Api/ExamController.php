<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\ExamRequest;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\QuestionInterface;
use Illuminate\Http\Request;

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

}
