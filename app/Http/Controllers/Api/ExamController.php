<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\ExamRequest;
use App\Repo\Interfaces\ExamInterface;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public  $exam;
    public function __construct(ExamInterface $exam)
    {
        $this->exam=$exam;
    }

    public function index(){
        $response=$this->exam->getAllExamList();
        if($response && $response->count() > 0){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 206, $message = 'Content not available', $response);
        }
        return response()->json($response);
    }
    public function saveExam(ExamRequest $request){

         $request->all();
        $res=$this->exam->saveExam($request);
        if($res=='success'){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $res);
        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res, $res);
        }
        return response()->json($response);
    }


    public function editExam(Request $request){
        $id=$request->id;
        $res=$this->exam->editExam($id);
        if($res){

            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'Record found', $res);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message = 'data not found', $res);

        }
        return response()->json($response);
    }
    public function updateExam(Request $request){
         $request->all();
        $res=$this->exam->updateExam($request);
        if($res==1){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'Record updated successfully', $res);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 206, $message =$res,$res);
        }
        return response()->json($response);
    }

    public function deleteExam(Request $request){
        $id=$request->id;
        $response=$this->exam->deleteExam($id);
        if($response==1){
            $response= Helper::createAPIResponce($is_error = false, $code = 201, $message = 'Record deleted', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = false, $code = 206, $message = $response, $response);
        }
        return response()->json($response);
    }

    //isExamSchedule
    public function isExamSchedule(Request $request){
            $request->all();
        return $response=$this->exam->isStdExamSchedule($request->std_id);
        if($response==1){
            $response= Helper::createAPIResponce($is_error = false, $code = 201, $message = 'Schedlue', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = false, $code = 206, $message = 'Not Schedule', $response);
        }
        return response()->json($response);
    }

    public function getStdExamInfo(Request $request){
         $response=$this->exam->getStdExamInfo($request->std_id,1);
        if($response){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message = 'Not found', $response);
        }
        return $response;
    }
}
