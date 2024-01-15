<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Repo\Interfaces\CategoryInterface;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\QuestionInterface;
use App\Repo\Interfaces\ResultInterface;
use App\Repo\Interfaces\StudentInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $course;
    protected $students;
    protected $exams;
    protected $questions;
    protected $result;


    public function __construct(CategoryInterface $course,StudentInterface $students,ExamInterface $exams,QuestionInterface $questions,ResultInterface $result)
    {
        $this->course=$course;
        $this->students=$students;
        $this->exams=$exams;
        $this->questions=$questions;
        $this->result=$result;

    }
    public function adminDashboardStates(){

          $courseRes=$this->course->getCourseDropdown();
         $studentRes=$this->students->getAllStdDropdown();
         $examRes=$this->questions->countResult();
         $qRes=$this->questions->getAllQuestions();

         $course=$this->course->getCourseConfigInfoAndQuestions();
         $courseResults=$this->result->courseWiseResults($course->id);
         $results=$this->result->countOverAllResults();

         $resSection=array(
            'course_id'=>$course->id,
            'course_name'=>$course->cat_title,
            'course_code'=>$course->course_code,
            'totalResult'=>$courseResults['totalResults'],
            'passResult'=> $courseResults['passResults'],
            'failResult'=> $courseResults['failResults'],
            'todayResult'=> $courseResults['todayResult'],
            'results'=> $results,

        );



         $data=array(
             'totalCourse'=>$courseRes->count(),
             'totalStudent'=>$studentRes->count(),
             'totalExam'=>$examRes,
             'totalQuestion'=>$qRes->count(),
             'courseInfo'=>$this->course->getCourseConfigInfoAndQuestions(),
             'resultSection'=>$resSection
         );

       return $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $data);



    }
}
