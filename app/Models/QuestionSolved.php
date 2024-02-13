<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionSolved extends Model
{
    use HasFactory;
    protected $fillable=['attempt_id','q_id','is_answered','q_lang','audio_lang','topic_id'];

    public function question()
    {
        return $this->belongsTo(Question::class,  'q_id','id')->select(['id','q_is_video','correct_opt','q_image','q_video','opt_a_image','opt_b_image','opt_c_image']);
    }

}
