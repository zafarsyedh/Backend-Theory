<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionTranslation extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'q_id',
        'lang_id',
        'q_title',
        'opt_a',
        'opt_b',
        'opt_c',
        'opt_a_audio',
        'opt_b_audio',
        'opt_c_audio',
        'lang',
        'q_audio',
        'code',
    ];

    public function lang()
    {
        return $this->belongsTo(Language::class, 'lang_id', 'id')->select(['id','lang','direction']);
    }


    public function questions()
    {
        return $this->belongsTo(Question::class,  'q_id','id');
    }
}
