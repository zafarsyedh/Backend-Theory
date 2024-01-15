<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        return 'home';
    }

    //test
    public function test(){

        $array1 = [1, 2, 3];
        $array2 = [4, 5, 6];

        $mergedArray = array_merge($array1, $array2);

return $mergedArray;


    }
}
