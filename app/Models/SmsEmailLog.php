<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsEmailLog extends Model
{
    use HasFactory;
    protected $fillable = ['exam_id','content','type','is_send'];


    public function exam()
    {
        return $this->belongsTo(ExamSchedule::class,  'exam_id','id');
    }

}
