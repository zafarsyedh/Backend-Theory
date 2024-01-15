<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\LanguageRequest;
use App\Repo\Interfaces\LanguageInterface;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
     public  $language;
     public function __construct(LanguageInterface $language)
     {
         $this->language=$language;
     }
     public function index(){

         $response=$this->language->getAllLanguages();
        if($response->count() > 0){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);

        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 206, $message = 'content not available', $response);

        }
         return response()->json($response);
    }
     public function getAllLangForDropdown(){

        $response=$this->language->getAllLangForDropdown();
        if($response->count() > 0){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);

        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 206, $message = 'content not available', $response);

        }
        return response()->json($response);
    }
     public function saveLanguage(LanguageRequest $request){
             $res=$this->language->saveLanguage($request);
            if( $res['status'] == 'success'){
                $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
             }else{
                $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
             }
         return response()->json($response);
             }
     public function deleteLanguage($id){
        $res=$this->language->deleteLanguage($id);
        if($res==1){
            $response=$this->createAPIResponce($is_error=false,$code=200,$message='Record deleted successfully',$res);
            return response()->json($response, $status = 200);
        }else{
            $response=$this->createAPIResponce($is_error=true,$code=401,$message=$res,$res);
            return response()->json($response, $status = 401);
        }
    }
     public function editLanguage(Request $request){
        $id=$request->id;
        $res=$this->language->editLanguage($id);
        if($res){
            $response=$this->createAPIResponce($is_error=false,$code=200,$message='Record found',$res);
            return response()->json($response, $status = 200);
        }else{
            $response=$this->createAPIResponce($is_error=true,$code=401,$message=$res,$res);
            return response()->json($response, $status = 401);
        }
    }
     public function updateLanguage(Request $request){
             $request->all();
            $res=$this->language->updateLanguage($request);
            if( $res['status'] == 'success'){
             $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = $res['messege'], $res['data'] );
         }else{
             $response =Helper::createAPIResponce($is_error = true, $code = 404, $message = $res['messege'], $res['status']);
         }
                return response()->json($response);
    }
     public function getLangInfo(Request $request){

        $response=$this->language->editLanguage($request->lang_id);
        if($response->count() > 0){
            $response= Helper::createAPIResponce($is_error = false, $code = 200, $message = 'success', $response);

        }else{
            $response =Helper::createAPIResponce($is_error = true, $code = 206, $message = 'content not available', $response);

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
}
