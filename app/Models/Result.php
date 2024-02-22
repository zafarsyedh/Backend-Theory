<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;
    protected $fillable=['exam_id','total_duration','test_duration','total_question','correct_ans','correct_ans_required','status'];

    public function attempt()
    {
        return $this->belongsTo(Attempt::class, 'exam_id', 'exam_id');
    }

}
