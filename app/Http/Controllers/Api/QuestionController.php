<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\ExamRequest;
use App\Http\Requests\QuestionRequest;
use App\Imports\QuestionImport;
use App\Imports\UsersImport;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\Language;
use App\Models\Question;
use App\Models\Result;
use App\Models\SolvedQuestions;
use App\Repo\Interfaces\CategoryInterface;
use App\Repo\Interfaces\CourseInterface;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\LanguageInterface;
use App\Repo\Interfaces\QuestionInterface;
use App\Repo\Interfaces\TopicAreaInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{
    public  $question;
    public  $category;
    public  $language;
    public  $course;
    public  $topicArea;
    public  $exam;
    public  $response='';

    public function __construct(QuestionInterface $question,CategoryInterface $category,LanguageInterface $language,ExamInterface $exam, CourseInterface $course,TopicAreaInterface $topicArea)
    {
        $this->question=$question;
        $this->category=$category;
        $this->course=$course;
        $this->topicArea=$topicArea;
        $this->language=$language;
        $this->exam=$exam;
    }

    public function index(){
        $response=$this->question->getAllQuestionForAdminSide();
        if($response->count() > 0){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 206, $message = 'not content', $response);
        }
        return response()->json($response);
    }

    public function getDataQuestion($id)
    {
        try{
            if($id>0){
                $response['editQuestion']=$this->question->findQuestionById($id);
            }
            $response['langs']=$this->language->getAllLanguages();
            $response['courses']=$this->course->getAllCourseForDropdown();
            $response['topicAreas']=$this->topicArea->getAllTopicAreaForDropdown();
            if($response){
                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);

            }else{
                $response =Helper::createAPIResponce($is_error = true, $code = 206, $message = 'content not available', $response);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
    public function getTranslationQuestion($id)
    {
        try{
            if($id>0){
                $response['question']=$this->question->getQuestionTranslationsById($id);
            }
            $response['langs']=$this->language->getAllLanguages();
            $response['courses']=$this->course->getAllCourseForDropdown();
            $response['topicAreas']=$this->topicArea->getAllTopicAreaForDropdown();
            if($response){
                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);

            }else{
                $response =Helper::createAPIResponce($is_error = true, $code = 206, $message = 'content not available', $response);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }
    public function createQuestion(Request $request){
        $request->all();
         $res=$this->question->createQuestions($request);
        if( $res['status'] == 'success'){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
        }
        return response()->json($response);
    }
    public function saveQuestionTranslation(Request $request){
         $request->all();
         $res=$this->question->saveQuestionTranslation($request);
        if( $res['status'] == 'success'){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
        }
        return response()->json($response);
    }
    public function importQuestion()
    {
        if( Excel::import(new QuestionImport,request()->file('file'))){
            $res='Upload successfully';
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res, $res);
        }else{
            $res='Not uploaded';
            $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res, $res);
        }
        return response()->json($response);
    }

    public function deleteQuestion($id)
    {
        try {
            $res = $this->question->deleteQuestion($id);
            return Helper::ajaxSuccess($res->get('data'),$res->get('message'));
        } catch (\Exception $e) {
            return Helper::ajaxError($e->getMessage());
        }
    }


    }

