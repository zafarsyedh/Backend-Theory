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
    public function index(){
        try{
            $response['courses']=$this->course->getAllCourses();
            if($response['courses']['status']){
                $response['langs']=$this->language->getAllLanguages();
                $response= Helper::success($response,$response['courses']['message']);
            }else{
                $response= Helper::error($response['courses']['message'],$response['courses']['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }
    }

    public function saveCourse(Request $request){
        try{
            $response=$this->course->saveCourse($request);
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

    public function saveCourseTranslation(Request $request){
        try{
            $response=$this->course->saveCourseTranslation($request);
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

    public function deleteCourse($id){
        try {
            $response = $this->course->deleteCourse($id);
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

    public function getCourseConfig($id){
        try{
             $response=$this->course->getCourseConfig($id);
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

    public function saveCourseConfig(Request $request){
        try{
            $response=$this->course->saveCourseConfig($request);
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
