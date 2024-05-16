<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticeResult extends Model
{
    use HasFactory;

    protected $fillable=['std_id','traffic_id','exam_id','require_percentage','obtain_percentage','is_eligible'];

}
