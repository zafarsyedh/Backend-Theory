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
            if($response['topicAreas']['status']){
                $response['langs']=$this->language->getAllLanguages();
                $response= Helper::success($response,$response['topicAreas']['message']);
            }else{
                $response= Helper::error($response['topicAreas']['message'],$response['topicAreas']['data']);
            }
            return $response;
        } catch (\Exception $e) {
            return Helper::error($e->getMessage(),$e);
        }

    }
    public function saveTopicArea(Request $request){

        try{
            $response=$this->topic->createTopics($request);
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

    public function saveTopicTranslation(Request $request){

        try{
            $response=$this->topic->saveTopicTranslation($request);
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


    public function deleteTopic( $id){
        try {
            $response =$this->topic->deleteTopics($id);
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
