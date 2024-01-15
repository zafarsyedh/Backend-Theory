<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\TopicArea;
use App\Repo\Interfaces\LanguageInterface;
use App\Repo\Interfaces\TopicAreaInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopicAreaController extends Controller
{

    public  $topic;
    public  $language;
    public function __construct(TopicAreaInterface $topic,LanguageInterface $language)
    {
        $this->topic=$topic;
        $this->language=$language;
    }

    public function index(){

        try{
            $response['topicAreas']=$this->topic->getAllTopics();
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
    public function saveTopicArea(Request $request){

        try{
            $res=$this->topic->createTopics($request);
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

    public function saveTopicTranslation(Request $request){
        try{
            $res=$this->topic->saveTopicTranslation($request);
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






    public function deleteTopic( $id){
        try{
            $res=$this->topic->deleteTopics($id);
            if($res==1){
                $response=$this->createAPIResponce($is_error=false,$code=200,$message='Record deleted successfully',$res);
                return response()->json($response, $status = 200);
            }else{
                $response=$this->createAPIResponce($is_error=true,$code=401,$message=$res,$res);
                return response()->json($response, $status = 401);
            }
        } catch (\Exception $e) {
            return Helper::sendError($e->getMessage(),$errors= [], $code = 206);
        }
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




}
