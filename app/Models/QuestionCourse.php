<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCourse extends Model
{
    use HasFactory;
    protected $fillable = [
        'q_id', 'course_id',
    ];
}
