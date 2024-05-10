<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
public function getStudentInfo($trafficId){

   return $response = Http::get(env('API_URL').'student/'.$trafficId);
}

    public function getSharjahStudent($trafficId){
        return $response = Http::get(env('SHARJAH_API_URL').'student/'.$trafficId);
    }


}
