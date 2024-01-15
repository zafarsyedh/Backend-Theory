<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicAreaTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'topic_area_id', 'lang', 'full_name',
    ];

    public function topicArea()
    {
        return $this->belongsTo(TopicArea::class, 'topic_area_id', 'id');
    }
}
