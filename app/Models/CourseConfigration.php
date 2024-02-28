<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseConfigration extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'specific_question', 'common_question', 'video_question', 'require_type', 'specific_require', 'common_require', 'video_require', 'total_require', 'total_duration', 'video_duration','practice_duration'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
