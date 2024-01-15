<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;
    protected $casts = [
        'created_at' => 'datetime:d M Y',
    ];

    public function student()
    {
        return $this->hasOne(Student::class, 'id', 'std_id')->select(['id','name','tarffic_id']);
    }

    public function course()
    {
        return $this->hasOne(Category::class, 'id', 'course_id')->select(['id','cat_title']);
    }

    public function getStatusAttribute($value)
    {
        if($value==1){
            $getVal='Pass';
        }
        if($value==0){
            $getVal='Fail';
        }
        return $getVal;
    }
}
