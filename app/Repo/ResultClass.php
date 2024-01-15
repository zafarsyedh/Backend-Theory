<?php

namespace App\Repo;
use App\Models\Category;
use App\Models\CourseConfigration;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Result;
use App\Models\SolvedQuestions;
use Illuminate\Support\Facades\DB;

class ResultClass implements Interfaces\ResultInterface
{
    public function getPracticeTestResult($stdId=null)
    {
         $qry = SolvedQuestions::query();
        $qry = $qry->with('student');
            $qry = $qry->select('std_id','q_type','attempt','created_at', DB::raw('SUM(IF(ans = 1, 1, 0)) as correct'),
            DB::raw('SUM(IF(ans = 0, 1, 0)) as wrong'),DB::raw('SUM(IF(ans =2, 1, 0)) as skip'));
            $qry = $qry->where('exam_type',2);
        ($stdId)?$qry = $qry->where('std_id',$stdId):'';
            $qry = $qry->groupBy('attempt');
        $qry = $qry->orderBy('id','DESC');
           return  $solvedQ = $qry->get();

    }



    public function getExamResult($request)
    {
        $qry=Result::with('student','course');
        ($request->std_id > 0)?$qry= $qry->where('std_id',$request->std_id):"";
        ($request->type ==0)? $qry=$qry->latest('id')->first():$qry=$qry->orderBy('id','DESC')->paginate(10);
        return  $qry;
    }

    public function printExamResult($request)
    {
        $qry=Result::with('student','course');
        $qry= $qry->where('exam_id',$request->examId);
        $qry=$qry->latest('id')->first();
        return  $qry;
    }

    public function getExamResultForAdminView($std_id)
    {


        $qry = SolvedQuestions::query();
        $qry=$qry->with('exam','exam.course','exam.student','exam.config');
        $qry = $qry->select('solved_questions.exam_id','solved_questions.created_at', DB::raw('SUM(IF(solved_questions.ans = 1, 1, 0)) as correct'),
            DB::raw('SUM(IF(solved_questions.ans = 0, 1, 0)) as wrong'));
        ($std_id > 0)?$qry = $qry->where('solved_questions.std_id',$std_id):'';
        $qry=$qry->where('solved_questions.exam_type',1);
        $qry = $qry->groupBy('solved_questions.exam_id');
        $qry = $qry->orderBy('solved_questions.id','DESC');
        return  $solvedQ = $qry->paginate(5);

    }

    public function getExamResultDetail($examId)
    {
        $qry=SolvedQuestions::query();
        $qry=$qry->with(['translations' => function ($query) {
            return $query->where('lang_id',1);
        },'translations.question']);

        $qry=$qry->where('exam_id',$examId);
        $qry=$qry->where('exam_type',1);
        $qry=$qry->get();
        return $qry;

    }

    public function courseWiseResults($courseId){
        $data['totalResults']=  Result::where('course_id',$courseId)->count();
        $data['passResults']=  Result::where('course_id',$courseId)->where('status',1)->count();
        $data['failResults']=  Result::where('course_id',$courseId)->where('status',0)->count();
        $data['todayResult']=  Result::where('course_id',$courseId)->whereDate('created_at', date('Y-m-d'))->count();
        return $data;
    }

    public function countOverAllResults(){
        $data['totalRes']=  Result::count();
        $data['passRes']=  Result::where('status',1)->count();
        $data['failRes']=  Result::where('status',0)->count();
        $data['todayRes']=  Result::whereDate('created_at', date('Y-m-d'))->count();
        return $data;
    }

}
