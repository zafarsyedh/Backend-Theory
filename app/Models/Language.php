<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

//    public function getStatusAttribute($value)
//    {
//        if($value==1){
//            $getVal='Active';
//        }
//        if($value==0){
//            $getVal='In-Active';
//        }
//        return $getVal;
//    }
//
//    public function getDirectionAttribute($value)
//    {
//        if($value==2){
//            $getVal='LTR';
//        }
//        if($value==1){
//            $getVal='RTL';
//        }
//        return $getVal;
//    }

    public function questionTranslation()
    {
        return $this->hasOne(QuestionTranslation::class, 'lang_id', 'id');
    }
}
