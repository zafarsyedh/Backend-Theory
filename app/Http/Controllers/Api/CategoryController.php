<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\CourseRequest;
use App\Repo\Interfaces\CategoryInterface;
use App\Repo\Interfaces\ExamInterface;
use App\Repo\Interfaces\QuestionInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public  $category;
    public  $exam;
    public  $question;
    public function __construct(CategoryInterface $category,ExamInterface $exam,QuestionInterface $question)
    {
        $this->category=$category;
        $this->exam=$exam;
        $this->question=$question;
    }

    public function index(){
         $response=$this->category->getAllCategory();
        if($response->count() > 0){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 206, $message = 'content not available', $response);
        }
        return response()->json($response);
    }
    //saveCategor
    public function saveCategory(CourseRequest $request){

          $res=$this->category->saveCategory($request);
        if($res=='success'){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $res);
        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res, $res);
        }
        return response()->json($response);
    }
    public function deleteCategory(Request $request){
        $id=$request->id;
        $res=$this->category->deleteCategory($id);
        if($res==1){
            return response()->json(['success' => 'Record deleted successfully']);
            $response=$this->createAPIResponce($is_error=false,$code=200,$message='Record save',$res);
            return response()->json($response, $status = 200);
        }else{
            return response()->json(['error' =>$res]);
            $response=$this->createAPIResponce($is_error=true,$code=401,$message=$res,$res);

            return response()->json($response, $status = 401);
        }
    }
    //editLanguage
    public function editCategory(Request $request){
          $id=$request->id;
         $res=$this->category->editCategory($id);
        if($res){

            $response=$this->createAPIResponce($is_error=false,$code=200,$message='Record found',$res);
            return response()->json($response, $status = 200);
        }else{
            return response()->json(['error' =>$res]);
            $response=$this->createAPIResponce($is_error=true,$code=401,$message=$res,$res);

            return response()->json($response, $status = 401);
        }
    }

    public function updateCategory(CourseRequest $request){

        $res=$this->category->updateCategory($request);
        if($res==1){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'Record updated successfully', $res);
        }else{
            $response=$this->createAPIResponce($is_error=true,$code=401,$message=$res,$res);
        }
        return response()->json($response);
    }

    //getCourseDropdown

    public function getCourseDropdownList(){
        $response=$this->category->getCourseDropdown();
        if($response->count() > 0){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 206, $message = 'content not available', $response);
        }
        return response()->json($response);
    }
    public function createAPIResponce($is_error, $code, $message, $content)
    {
        $result = [];
        if ($is_error) {
            $result['success'] = false;
            $result['code'] = $code;
            $result['message'] = $message;
        } else {
            $result['success'] = true;
            $result['code'] = $code;
            if ($content == null) {
                $result['message'] = $message;
            } else {
                $result['data'] = $content;
            }
        }
        return $result;
    }

    public function saveCourseConfig(CourseRequest $request){

        try{
            $res=$this->category->saveCourseConfig($request);
            if($res=='success'){
                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $res);
            }else{
                $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res, $res);
            }
            return response()->json($response);

        }catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }


    }

    //getCourseConfigInfo
    public function getCourseConfigInfo(Request $request){
        $id=$request->id;
        $res=$this->category->getCourseConfigInfo($id);
        if($res){

            $response=$this->createAPIResponce($is_error=false,$code=200,$message='Record found',$res);
            return response()->json($response, $status = 200);
        }else{
            return response()->json(['error' =>$res]);
            $response=$this->createAPIResponce($is_error=true,$code=401,$message=$res,$res);

            return response()->json($response, $status = 401);
        }
    }

    //getCourseQuestion
    public function countCourseAudioVideoQuestion(Request $request){

        $stdId =$request->std_id;
        $examInfo = $this->exam->getStdExamInfo($stdId);
        if ($examInfo) {
            $data['courseName']=$examInfo->course->cat_title;
            $question = $this->question->countQuestionAcordingCourseAndType($examInfo->course_id,1);
            $data['audioQuestion'] = $question->count();
            $question = $this->question->countQuestionAcordingCourseAndType($examInfo->course_id,2);
            $data['videoTotalQuestion'] = $question->count();
            if ($data['audioQuestion']) {
                $response = Helper::createAPIResponce($is_error = false, $code = 200, $message = 'data found', $data);
            } else {
                $response = Helper::createAPIResponce($is_error = true, $code = 206, $message = 'not content', $data['solvedQuestion']);
            }
            return response()->json($response);
        }

    }
}
