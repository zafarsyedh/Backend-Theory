<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    use HasFactory;
    protected  $fillable=['std_id','course_id','is_active'];

    public function course()
    {
        return $this->belongsTo(Course::class,  'course_id','id');
    }

}
