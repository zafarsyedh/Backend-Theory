<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes,HasFactory;

    protected $casts = [
        'created_at' => 'datetime:d M Y h:i:s a',
    ];
    public function getStatusAttribute($value)
    {
        if($value==1){
            $getVal='Active';
        }
        if($value==0){
            $getVal='In-Active';
        }
        return $getVal;
    }

    public function result()
    {
        return $this->hasOne(Result::class, 'std_id', 'id')->select(['id','std_id','status']);
    }

    public function history()
    {
        return $this->hasOne(StudentLogedHistory::class, 'std_id', 'id')->select(['id','std_id','created_at']);
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class, 'std_id', 'id');
    }

}
