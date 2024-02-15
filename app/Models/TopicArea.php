<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicArea extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'title','status',
    ];
    public function topicAreaTranslation()
    {
        return $this->hasMany(TopicAreaTranslation::class, 'topic_area_id', 'id');
    }

    public function solvedQuestion()
    {
        return $this->hasMany(QuestionSolved::class, 'topic_id', 'id');
    }
}
