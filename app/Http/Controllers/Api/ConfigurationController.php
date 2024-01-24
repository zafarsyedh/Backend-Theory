<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\ConfigurationRequest;
use App\Repo\Interfaces\ConfigurationInterface;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public  $config;
    public function __construct(ConfigurationInterface $config)
    {
        $this->config=$config;
    }

    public function index(){

        try{
            $response=$this->config->getConfigInfo();
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
    public function saveConfig(Request $request){
        try{
            $response=$this->config->saveConfig($request);
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
