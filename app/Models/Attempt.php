<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(Student::class,  'std_id','id');
    }
    protected $casts = [
        'created_at' => 'datetime:d M Y h:i:s a',
    ];
}
