<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Repo\Interfaces\CourseInterface;
use App\Repo\Interfaces\LanguageInterface;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public  $course;
    public  $language;
    public function __construct(CourseInterface $course,LanguageInterface $language)
    {
        $this->course=$course;
        $this->language=$language;
    }

    public function saveCourse(Request $request){
        try{
            $res=$this->course->saveCourse($request);
                if( $res['status'] == 'success'){
                    $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
                }else{
                    $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
                }
            return response()->json($response);
            } catch (\Exception $e) {
         return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }

    }
    public function saveCourseTranslation(Request $request){
        try{
            $res=$this->course->saveCourseTranslation($request);
                if( $res['status'] == 'success'){
                    $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
                }else{
                    $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
                }
            return response()->json($response);
            } catch (\Exception $e) {
         return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }

    }



    public function index(){
        try{
        $response['courses']=$this->course->getAllCourses();
        $response['langs']=$this->language->getAllLanguages();
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


    public function deleteCourse($id){
        try{
        $res=$this->course->deleteCourse($id);
        if($res==1){
            $response=Helper::createAPIResponce($is_error=false,$code=200,$message='Record deleted successfully',$res);
            return response()->json($response, $status = 200);
        }else{
            $response=Helper::createAPIResponce($is_error=true,$code=401,$message=$res,$res);
            return response()->json($response, $status = 401);
        }
    } catch (\Exception $e) {
        return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
    }

    public function updateCourse(Request $request){
        try{
        $res=$this->course->updateCourse($request);
        if( $res['status'] == 'success'){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
        }
        return response()->json($response);
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 400);
        }
    }


    public function getCourseConfig($id){
        try{
        $response=$this->course->getCourseConfig($id);
        if($response){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 404, $message = 'data not found', $response);
        }
        return response()->json($response);
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 400);
        }
    }

    public function saveCourseConfig(Request $request){
        try{
            $res=$this->course->saveCourseConfig($request);
            if( $res['status'] == 'success'){
                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
            }else{
                $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }

    }






}
