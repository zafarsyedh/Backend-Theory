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

    public function __construct(QuestionInterface $question,LanguageInterface $language,ExamInterface $exam, CourseInterface $course,TopicAreaInterface $topicArea)
    {
        $this->question=$question;
        $this->course=$course;
        $this->topicArea=$topicArea;
        $this->language=$language;
        $this->exam=$exam;
    }

    public function index(){

        try{
            $response=$this->question->getAllQuestionForAdminSide();
            if($response['status']){
                $response= Helper::success($response,$response['message']);
            }else{
                $response= Helper::error($response['message'],$response['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }

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
                $response= Helper::success($response,'data');
            }else{
                $response= Helper::error($response['editQuestion']['message'],$response['editQuestion']['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
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
                $response= Helper::success($response,'data');
            }else{
                $response= Helper::error($response['question']['message'],$response['question']['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }
    public function createQuestion(Request $request){

        try{
            $response=$this->question->createQuestions($request);;
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
    public function saveQuestionTranslation(Request $request){
        try{
            $response=$this->question->saveQuestionTranslation($request);
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
    public function importQuestion()
    {
        try{
                $file =Excel::import(new QuestionImport,request()->file('file'));
                $response= Helper::success("imported",'Upload successfully');
                return $response;
            } catch (\Exception $e) {
                return Helper::error($e->getMessage(),$e);
            }
    }

    public function deleteQuestion($id)
    {
        try {
            $response = $this->question->deleteQuestion($id);
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


    }

