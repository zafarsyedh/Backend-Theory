<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSchedule extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable=['system_id','std_id','course_id','invg_id','q_lang','audio_lang','exam_status','exam_type'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'std_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function qLanguage()
    {
        return $this->belongsTo(Language::class, 'q_lang', 'lang_short');
    }
    public function audioLanguage()
    {
        return $this->belongsTo(Language::class, 'audio_lang', 'lang_short');
    }

    public function invigilator()
    {
        return $this->belongsTo(User::class, 'invg_id', 'id');
    }

    public function system()
    {
        return $this->belongsTo(System::class, 'system_id', 'id');
    }
    public function attempt()
    {
        return $this->hasOne(Attempt::class, 'exam_id', 'id');
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class, 'exam_id', 'id');
    }

    protected $casts = [
        'created_at' => 'datetime:d M Y h:i:s a',
    ];



}
