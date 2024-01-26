<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseTranslation extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'course_id', 'lang', 'full_name',
    ];



    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
