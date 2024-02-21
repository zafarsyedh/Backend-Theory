<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'title','status',
    ];


    public function rooms()
    {
        return $this->hasMany(Room::class, 'branch_id', 'id')->select(['id','title','branch_id']);
    }

}
