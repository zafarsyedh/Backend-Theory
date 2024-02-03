<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class System extends Model
{


    use HasFactory,SoftDeletes;
    protected $fillable = [
        'room_id','title','system_ip','status'
    ];
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
