<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(Student::class, 'std_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function qLang()
    {
        return $this->belongsTo(Language::class, 'q_lang', 'lang_short');
    }
    public function audioLang()
    {
        return $this->belongsTo(Language::class, 'q_lang', 'lang_short');
    }

    public function invigilator()
    {
        return $this->belongsTo(User::class, 'invg_id', 'id');
    }

    public function system()
    {
        return $this->belongsTo(System::class, 'system_id', 'id');
    }
}
