<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\Student;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\QuestionInterface;
use App\Repo\Interfaces\ResultInterface;
use Illuminate\Http\Request;

class ResultController extends Controller
{


    public $result;
    public $exam;
    public $question;

    public function __construct(ResultInterface $result, ExamInterface $exam, QuestionInterface $question)
    {
        $this->result = $result;
        $this->exam = $exam;
        $this->question = $question;

    }

    public function getPracticeResult(Request $request)
    {

        $stdId =$request->std_id;

        $examInfo = $this->exam->getStdExamInfo($stdId);
        if ($examInfo) {
            $question = $this->question->countQuestionAcordingCourseAndType($examInfo->course_id,1);
            $data['totalQuestion'] = $question->count();
            $question = $this->question->countQuestionAcordingCourseAndType($examInfo->course_id,2);
            $data['videoTotalQuestion'] = $question->count();
             $data['solvedQuestion'] = $this->result->getPracticeTestResult($request->std_id);
            if ($data['solvedQuestion']) {
                $response = Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $data);
            } else {
                $response = Helper::createAPIResponce($is_error = true, $code = 206, $message = 'not content', $data['solvedQuestion']);
            }
            return response()->json($response);
        }
    }

    //practiceResultForAdminReport
    public function practiceResultForAdminReport(Request $request)
    {
        $qry= Student::withCount('attempts');
                $qry=$qry->whereHas('attempts', function($query)
                {
                    $query->where('type',1);
                });
       return $qry=$qry->where('status',1)->paginate(10);


    }

    //getExamResult

    public function getExamResult(Request $request)
    {
             $data['examResult'] = $this->result->getExamResult($request);
            if ($data['examResult']) {
                $response = Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $data);
            } else {
                $response = Helper::createAPIResponce($is_error = true, $code = 206, $message = 'not content', $data);
            }
            return response()->json($response);
        }

        //printExamResult
    public function printExamResult(Request $request)
    {
         $request->all();
        $data['examResult'] = $this->result->printExamResult($request);
        if ($data['examResult']) {
            $response = Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $data);
        } else {
            $response = Helper::createAPIResponce($is_error = true, $code = 206, $message = 'not content', $data);
        }
        return response()->json($response);
    }


    public function getExamResultForAdmin(Request $request)
    {

//            $res = $this->result->getExamResultForAdminView($request->std_id);
                $res = $this->result->getExamResult($request);
            if ($res) {
                $response = Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $res);
            } else {
                $response = Helper::createAPIResponce($is_error = true, $code = 206, $message = 'not content',$res);
            }
            return response()->json($response);

    }

//getResultDetail
    public function getResultDetail(Request $request)
    {

             $examId =$request->exam_id;
             $examDetail = $this->result->getExamResultDetail($examId);
            if($examDetail) {
                $response = Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $examDetail);
            }else {
                $response = Helper::createAPIResponce($is_error = true, $code = 206, $message = 'not content', $examDetail);
            }
            return response()->json($response);
    }

}
