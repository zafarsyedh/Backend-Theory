<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use   SoftDeletes, HasFactory;

    protected $fillable = [
        'code', 'q_type', 'q_is_video', 'course_id', 'topic_id', 'correct_opt', 'q_image', 'q_audio', 'q_video', 'status',
    ];

    public function questionDetail()
    {
        return $this->hasOne(QuestionTranslation::class, 'q_id', 'id');
    }

    public function questionTranslations()
    {
        return $this->hasMany(QuestionTranslation::class, 'q_id', 'id');
    }


    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')->select(['id','short_name']);
    }
    public function topic()
    {
        return $this->belongsTo(TopicArea::class, 'topic_id', 'id')->select(['id']);
    }

    public function qWithLang()
    {
      return   $this->hasOne(QuestionTranslation::class, 'q_id', 'id');
    }
    public function qLangAudio()
    {
        return   $this->hasOne(QuestionTranslation::class, 'q_id', 'id')->select(['id','q_id','q_content','q_audio','opt_a_audio','opt_b_audio','opt_c_audio']);;
    }

    public function setQTypeAttribute($value)
    {
        if($value=='specific'){
            $value=2;
        }
        if($value=='common'){
            $value=1;
        }
        $this->attributes['q_type'] =$value;
    }
    public function getQTypeAttribute($value)
    {
        if($value==1){
            $getVal='common';
        }
        if($value==2){
            $getVal='specific';
        }
        return $getVal;
    }

    public function qCourses()
    {
        return $this->hasMany(QuestionCourse::class,  'q_id','id');
    }






}
