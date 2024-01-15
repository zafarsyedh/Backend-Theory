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
        $response=$this->config->getConfigInfo();
        if($response && $response->count() > 0){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);
        }else{
            $response= Helper::createAPIResponce($is_error = true, $code = 206, $message = 'Content not available', $response);
        }
        return response()->json($response);
    }
    public function saveConfig(Request $request){

         $request->all();
        $res=$this->config->saveConfig($request);
        if( $res['status'] == 'success'){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
        }
        return response()->json($response);
    }
}
