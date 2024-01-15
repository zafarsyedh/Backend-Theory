<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use   SoftDeletes, HasFactory;
    protected $casts = [
        'created_at' => 'datetime:d M Y',
    ];

    public function invigilator()
    {
        return $this->belongsTo(User::class, 'invg_id', 'id')->select(['id','name']);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'std_id', 'id')->select(['id','name','email','image','tarffic_id']);
    }
    public function course()
    {
        return $this->belongsTo(Category::class, 'course_id', 'id')->select(['id','cat_title']);
    }

    public function getDirectionAttribute($value)
    {
        if($value==1){
            $getVal='LTR';
        }
        if($value==0){
            $getVal='RTL';
        }
        return $getVal;
    }

    public function config()
    {
        return $this->hasOne(CourseConfigration::class, 'course_id', 'course_id');
    }

    public function getStatusAttribute($value)
    {
        if($value==0){
            $getVal='Active';
        }
        if($value==1){
            $getVal='In-Active';
        }
        return $getVal;
    }
}
