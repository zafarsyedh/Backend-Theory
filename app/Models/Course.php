<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'id', 'short_name', 'status',
    ];

    public function courseTranslation()
    {
        return $this->hasMany(CourseTranslation::class, 'course_id', 'id');
    }
    public function courseConfig()
    {
        return $this->hasOne(CourseConfigration::class, 'course_id', 'id');
    }

    public function courseQuestions()
    {
        return $this->hasMany(QuestionCourse::class, 'course_id', 'id');
    }



}
