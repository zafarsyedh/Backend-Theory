<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolvedQuestions extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime:d M Y ',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'std_id', 'id')->select(['id','name','l_name','tarffic_id','image']);
    }
    public function translations()
    {
        return $this->belongsTo(QuestionTranslation::class, 'q_id', 'q_id')->select(['id','q_id','q_content','opt_a','opt_b','opt_c']);
    }

}
