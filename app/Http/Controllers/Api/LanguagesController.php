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

         try{
             $response=$this->language->getAllLanguages();
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
     public function getAllLangForDropdown(){

         try{
             $response=$this->language->getAllLangForDropdown();
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
     public function saveLanguage(Request $request){

         try{
             $response=$this->language->saveLanguage($request);
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
     public function deleteLanguage($id){
         try {
             $response = $this->language->deleteLanguage($id);
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
