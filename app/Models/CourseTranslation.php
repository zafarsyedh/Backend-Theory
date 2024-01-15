<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id', 'lang', 'full_name',
    ];



    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
